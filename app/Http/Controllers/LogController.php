<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DateTime;

use DB;
use Auth;

use App\User;
use App\MyModel;
use App\Category;
use App\SubCategory;
use App\SubSubCategory;
use App\Calendar;
use App\Client;
use App\Config;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class LogController extends Controller
{
    public function topicStat() {
      return view('dashboard.topicstat');
    }

    public function clientService() {
      $rows = Client::where('user_id', Auth::user()->id)->get();
      return view('dashboard.clientservicelog')->with('clients', $rows);
    }

    public function useOfTime() {
      return view('dashboard.useoftime');
    }

    public function getReport(Request $request) {
      $return = [];
      $params = $request->all();

      // Return
      if(empty($params['start']) && empty($params['end']))
        return ['data' => $return];

      $start = DateTime::createFromFormat(DATETIME_FORMAT, $params['start']);
      $end = DateTime::createFromFormat(DATETIME_FORMAT, $params['end']);
      $start = $start->format(config('steve.mysql_datetime_format'));
      $end = $end->format(config('steve.mysql_datetime_format'));

      $query = Calendar::where('start', '>=', $start)->where('end', '<=', $end);

      $rows = [];
      if($params['type'] == 'activity') {
        $rows = $query
                ->where('user_id', Auth::user()->id)
                ->get();
      } elseif($params['type'] == 'topic' && !empty($params['value'])) {
        $filter = explode('=', $params['value']);

        switch($filter[0]) {
          case 'c':
            $rows = $query
              ->where('user_id', Auth::user()->id)
              ->where('categoryID', $filter[1])
              ->get();
            break;
          case 'sc':
            $rows = $query
              ->where('user_id', Auth::user()->id)
              ->where('subCategoryID', $filter[1])
              ->get();
            break;
        }
      } elseif($params['type'] == 'client-service' && !empty($params['value'])) {
        // Client ID
        $filter = $params['value'];
        $rows = DB::table('calendar_client')
                ->where('client_id', $filter)
                ->select('calendar_id')
                ->get();

        $calendarList = [];
        foreach($rows as $x)
          $calendarList[] = $x->calendar_id;

        $rows = $query->whereIn('id', $calendarList)->get();
      } elseif($params['type'] == 'topic-stat') {
        $rows = $query->select('categoryID', 'subCategoryID', DB::raw('SUM(TIMESTAMPDIFF(minute, start, end)) as total'), DB::raw('COUNT(*) as freq'))
                ->where('user_id', Auth::user()->id)
                ->groupBy('categoryID', 'subCategoryID')
                ->orderBy('total', 'DESC')
                ->get();
      } elseif($params['type'] == 'use-of-time') {
        $rows = $query->select('categoryID', DB::raw('SUM(TIMESTAMPDIFF(minute, start, end)) as total'), DB::raw('COUNT(*) as freq'))
                ->where('user_id', Auth::user()->id)
                ->groupBy('categoryID')
                ->orderBy('total', 'DESC')
                ->get();
      }

      if(in_array($params['type'], ['activity', 'topic', 'client-service'])) {
        $start = null;
        $end = null;
        foreach($rows as $x) {
          $start = DateTime::createFromFormat(DATETIME_FORMAT, $x->start);
          $end = DateTime::createFromFormat(DATETIME_FORMAT, $x->end);

          $clients = DB::table('calendar_client')
            ->where('calendar_id', $x->id)
            ->join('client', 'calendar_client.client_id', '=', 'client.id')->get();

          $clientName = [];
          for($i = 0; $i < count($clients); $i++)
            $clientName[] = $clients[$i]->name;

          if(!empty($clientName)) {
            $clientName = '[' . implode(', ', $clientName) . ']';
          } else {
            $clientName = null;
          }

          $topic = $x->category['abbrev'] . ' | ' . $x->subCategory['title'] . ' ' . $clientName;

          $return[] = [
            'date' => $start->format(DATE_FORMAT),
            'start' => $start->format(config('steve.time_format')),
            'end' => $end->format(config('steve.time_format')),
            'description' => $topic,
            'note' => $x['description']
          ];
        }
      } elseif(in_array($params['type'], ['topic-stat', 'use-of-time'])) {
        $totalTime = 0;
        $currTotalTime = 0;

        foreach($rows as $x)
          $totalTime += $x->total;

        for($i = 0; $i < count($rows); $i++) {
          $x = $rows[$i];

          $time = round($x->total / $totalTime * 100, 1);
          if($i == count($rows) - 1)
            $time = 100 - $currTotalTime;

          $currTotalTime += $time;

          $return[] = [
            'category' => $x->category['abbrev'],
            'subcategory' => $x->subcategory['title'],
            'time' => number_format($time, 1),
            'hours' => number_format(round($x->total / 60, 1), 1),
            'freq' => $x->freq,
            'total' => number_format($currTotalTime, 1)
          ];
        }
      }

      return ['data' => $return];
    }

    public function getActivityPieChart(Request $request) {
      $return = [];
      $params = $request->all();

      // Return
      if(empty($params['start']) && empty($params['end']))
        return $return;

      if(isset($params['type'])){
        if($params['type'] == 'activity')
          $return = $this->getActivityReport($params);
        elseif($params['type'] == 'topic' && !empty($params['value']))
          $return = $this->getTopicReport($params);
        elseif($params['type'] == 'client-service')
          $return = $this->getClientService($params);
        elseif($params['type'] == 'topic-stat')
          $return = $this->getTopicStat($params);
        elseif($params['type'] == 'use-of-time')
          $return = $this->getUseOfTime($params);
      }
      return $return;
    }

    private function getUseOfTime($params) {
      $return = [];

      $grayColor = config('steve.gray_color');
      $user = User::find(Auth::user()->id);

      $start = DateTime::createFromFormat(DATETIME_FORMAT, $params['start']);
      $end = DateTime::createFromFormat(DATETIME_FORMAT, $params['end']);
      $start = $start->format(config('steve.mysql_datetime_format'));
      $end = $end->format(config('steve.mysql_datetime_format'));

      // Get calendar associated with client id
      $rows = Calendar::select('categoryID', DB::raw('SUM(TIMESTAMPDIFF(minute, start, end)) as total'), DB::raw('COUNT(*) as freq'))
              ->where('start', '>=', $start)
              ->where('end', '<=', $end)
              ->where('user_id', Auth::user()->id)
              ->groupBy('categoryID')
              ->orderBy('total', 'DESC')
              ->get();

      $total = 0;
      foreach($rows as $x)
        $total += $x['total'];

      $total2 = 0;

      for($i = 0; $i < count($rows); $i++) {
        $x = $rows[$i];

        if($i < count($rows) - 1)
          $value = round($x['total'] / $total * 100, 1);
        else
          $value = round(100 - $total2, 1);

        $label = $x->category->abbrev;
        $return[] = [
          'value' => $value,
          'color' => $x->category->color,
          'highlight' => $x->category->color,
          'label' => $label
        ];

        $total2 += $value;
      }

      return $return;
    }

    private function getTopicStat($params) {
      $return = [];

      $grayColor = config('steve.gray_color');
      $user = User::find(Auth::user()->id);

      $start = DateTime::createFromFormat(DATETIME_FORMAT, $params['start']);
      $end = DateTime::createFromFormat(DATETIME_FORMAT, $params['end']);
      $start = $start->format(config('steve.mysql_datetime_format'));
      $end = $end->format(config('steve.mysql_datetime_format'));

      // Get calendar associated with client id
      $rows = Calendar::select('categoryID', 'subCategoryID', DB::raw('SUM(TIMESTAMPDIFF(minute, start, end)) as total'), DB::raw('COUNT(*) as freq'))
              ->where('start', '>=', $start)
              ->where('end', '<=', $end)
              ->where('user_id', Auth::user()->id)
              ->groupBy('categoryID', 'subCategoryID')
              ->orderBy('total', 'DESC')
              ->get();

      $total = 0;
      foreach($rows as $x)
        $total += $x['total'];

      $total2 = 0;

      for($i = 0; $i < count($rows); $i++) {
        $x = $rows[$i];

        if($i < count($rows) - 1)
          $value = round($x['total'] / $total * 100, 1);
        else
          $value = round(100 - $total2, 1);

        $label = $x->category->abbrev . '-' . $x->subcategory->title;
        $return[] = [
          'value' => $value,
          'color' => $x->subcategory->color,
          'highlight' => $x->subcategory->color,
          'label' => $label
        ];

        $total2 += $value;
      }

      return $return;
    }

    private function getClientService($params) {
      $return = [];

      if(empty($params['value']))
        return $return;

      $grayColor = config('steve.gray_color');
      $user = User::find(Auth::user()->id);

      $start = DateTime::createFromFormat(DATETIME_FORMAT, $params['start']);
      $end = DateTime::createFromFormat(DATETIME_FORMAT, $params['end']);
      $start = $start->format(config('steve.mysql_datetime_format'));
      $end = $end->format(config('steve.mysql_datetime_format'));

      // Get calendar associated with client id
      $rows = Calendar::select('id', DB::raw('SUM(TIMESTAMPDIFF(minute, start, end)) as total'))
              ->where('start', '>=', $start)
              ->where('end', '<=', $end)
              ->where('user_id', Auth::user()->id)
              ->groupBy('id')
              ->get();

      $clientRow = Client::find($params['value']);

      $total = 0;
      foreach($rows as $x)
        $total += $x['total'];

      $total2 = 0;
      $client = [
        'value' => 0,
        'color' => '#ff00ff',
        'highlight' => '#ff00ff',
        'label' => $clientRow->name,
      ];

      $other = [
        'value' => 0,
        'color' => $grayColor,
        'highlight' => $grayColor,
        'label' => 'Other',
      ];

      for($i = 0; $i < count($rows); $i++) {
        $x = $rows[$i];

        if($i < count($rows) - 1)
          $value = round($x['total'] / $total * 100, 1);
        else
          $value = round(100 - $total2, 1);

        $temp = $x->client()->where('client_id', $params['value'])->first();

        if(!empty($temp)) {
          // Add client
          $client['value'] += $value;
        } else {
          // Add other
          $other['value'] += $value;
        }

        $total2 += $value;
      }

      $return[0] = $client;
      $return[1] = $other;

      return $return;
    }

    private function getTopicReport($params) {
      $return = [];
      $grayColor = config('steve.gray_color');
      $user = User::find(Auth::user()->id);

      $start = DateTime::createFromFormat(DATETIME_FORMAT, $params['start']);
      $end = DateTime::createFromFormat(DATETIME_FORMAT, $params['end']);
      $start = $start->format(config('steve.mysql_datetime_format'));
      $end = $end->format(config('steve.mysql_datetime_format'));

      $query = Calendar::select('categoryID', 'subCategoryID', DB::raw('SUM(TIMESTAMPDIFF(minute, start, end)) as total'))
              ->where('start', '>=', $start)
              ->where('end', '<=', $end)
              ->where('user_id', Auth::user()->id);

      $filter = null;
      if(!empty($params['value']))
        $filter = explode('=', $params['value']);

      // Filter by category id or sub category id
      switch($filter[0]) {
        // Category
        case 'c':
          $rows = $query->groupBy('categoryID', 'subCategoryID')->get();

          // Find total
          $total = 0;
          foreach($rows as $x)
            $total += $x['total'];

          // $total2 to keep track sum of all row to match 100% by substract
          // with $total
          $total2 = 0;

          // Sub Category List to query other sub category that don't have any
          // Assocated event
          $categoryList = [];
          $other = [
            'value' => 0,
            'color' => $grayColor,
            'highlight' => $grayColor,
            'label' => 'Other',
          ];
          for($i = 0; $i < count($rows); $i++) {
            $x = $rows[$i];

            // CategoryList
            $categoryList[] = $x['categoryID'];

            // Get % of each sub category
            if($i < count($rows) - 1)
              $value = round($x['total'] / $total * 100, 1);
            else
              // If this the last, just substract with $total2 with 100(mean 100%)
              $value = 100 - $total2;

            if($x['categoryID'] == $filter[1]) {
              $return[] = [
                'value' => $value,
                'color' => $x->subCategory->color,
                'highlight' => $x->subCategory->color,
                'label' => $x->subCategory->title,
              ];
            } else {
              $other['value'] += $value;
            }

            $total2 += $value;
          }

          $rows = SubCategory::where('category_id', $filter[1])
                  ->whereNotIn('id', $categoryList)->get();

          foreach($rows as $x) {
            $return[] = [
              'value' => 0,
              'color' => $x->color,
              'highlight' => $x->color,
              'label' => $x->title,
            ];
          }

          if($other['value'] != 0)
            $return[] = $other;

          break;
        // Sub category
        case 'sc':
          $rows = $query->groupBy('subCategoryID', 'subSubCategoryID')->get();

          // Find total
          $total = 0;
          foreach($rows as $x)
            $total += $x['total'];

          // $total2 to keep track sum of all row to match 100% by substract
          // with $total
          $total2 = 0;

          // Sub Category List to query other sub category that don't have any
          // Assocated event
          $categoryList = [];
          $other = [
            'value' => 0,
            'color' => $grayColor,
            'highlight' => $grayColor,
            'label' => 'Other',
          ];
          for($i = 0; $i < count($rows); $i++) {
            $x = $rows[$i];

            // CategoryList
            $categoryList[] = $x['subCategoryID'];

            // Get % of each sub category
            if($i < count($rows) - 1)
              $value = round($x['total'] / $total * 100, 1);
            else
              // If this the last, just substract with $total2 with 100(mean 100%)
              $value = 100 - $total2;

            if($x['subCategoryID'] == $filter[1]) {
              $return[] = [
                'value' => $value,
                'color' => $x->subCategory->color,
                'highlight' => $x->subCategory->color,
                'label' => $x->subCategory->title,
              ];
            } else {
              $other['value'] += $value;
            }

            $total2 += $value;
          }

          $rows = SubSubCategory::where('category_id', $filter[1])
                  ->whereNotIn('id', $categoryList)->get();

          foreach($rows as $x) {
            $return[] = [
              'value' => 0,
              'color' => $x->color,
              'highlight' => $x->color,
              'label' => $x->title,
            ];
          }

          if($other['value'] != 0)
            $return[] = $other;

          break;
      }

      return $return;
    }


    private function getActivityReport($params) {
      $return = [];

      $user = User::find(Auth::user()->id);

      $start = DateTime::createFromFormat(DATETIME_FORMAT, $params['start']);
      $end = DateTime::createFromFormat(DATETIME_FORMAT, $params['end']);
      $start = $start->format(config('steve.mysql_datetime_format'));
      $end = $end->format(config('steve.mysql_datetime_format'));

      $rows = Calendar::select('categoryID', DB::raw('SUM(TIMESTAMPDIFF(minute, start, end)) as total'))
              ->where('start', '>=', $start)
              ->where('end', '<=', $end)
              ->where('user_id', Auth::user()->id)
              ->groupBy('categoryID')
              ->get();

      $total = 0;
      foreach($rows as $x)
        $total += $x['total'];

      $total2 = 0;
      $categoryList = [];
      for($i = 0; $i < count($rows); $i++) {
        $x = $rows[$i];

        // CategoryList
        $categoryList[] = $x['categoryID'];

        if($i < count($rows) - 1)
          $value = round($x['total'] / $total * 100, 1);
        else
          $value = 100 - $total2;

        $return[] = [
          'value' => $value,
          'color' => $x->category->color,
          'highlight' => $x->category->color,
          'label' => $x->category->title,
        ];

        $total2 += $value;
      }

      // Not in category
      $rows = Category::select('category.*')
              ->where('CompanyID', $user->CompanyID)
              ->whereNotIn('id', $categoryList)
              ->get();

      foreach($rows as $x) {
        $return[] = [
          'value' => 0,
          'color' => $x->color,
          'highlight' => $x->color,
          'label' => $x->title,
        ];
      }

      $rows = Category::where('companyID', null)
              ->whereNotIn('id', $categoryList)->get();

      foreach($rows as $x) {
        $return[] = [
          'value' => 0,
          'color' => $x->color,
          'highlight' => $x->color,
          'label' => $x->title,
        ];
      }

      return $return;
    }

    public function getConfig(Request $request) {
      $params = $request->all();
      $key_name = $params['key_name'];

      $row = DB::table('config')->where('key_name', $key_name)->first();

      return response()->json($row);
    }

    public function setConfig(Request $request) {
      $params = $request->all();
      $key_name = $params['key_name'];

      $row = Config::find($key_name);

      if ($row === null) {
           $row = new Config;
           $row->key_name = $key_name;
      }

      $row->value = $params['value'];
      $row->save();
    }
}
