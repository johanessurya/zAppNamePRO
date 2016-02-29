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
use App\Config;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class LogController extends Controller
{
    public function getReport(Request $request) {
      $return = [];
      $params = $request->all();

      $start = DateTime::createFromFormat(DATETIME_FORMAT, $params['start']);
      $end = DateTime::createFromFormat(DATETIME_FORMAT, $params['end']);
      $start = $start->format(config('steve.mysql_datetime_format'));
      $end = $end->format(config('steve.mysql_datetime_format'));

      $query = Calendar::where('start', '>=', $start)->where('end', '<=', $end);

      $rows = [];
      if($params['type'] == 'activity') {
        $rows = $query->get();
      } elseif($params['type'] == 'topic' && !empty($params['value'])) {
        $filter = explode('=', $params['value']);

        switch($filter[0]) {
          case 'c':
            $rows = $query->where('categoryID', $filter[1])->get();
            break;
          case 'sc':
            $rows = $query->where('subCategoryID', $filter[1])->get();
            break;
        }
      }

      $start = null;
      $end = null;
      foreach($rows as $x) {
        $start = DateTime::createFromFormat(DATETIME_FORMAT, $x['start']);
        $end = DateTime::createFromFormat(DATETIME_FORMAT, $x['end']);

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

        $topic = $x->category['abbrev'] . ' | ' . $x->subCategory['title'] . ' ' . $clientName . ' - ' . $x['description'];

        $return[] = [
          'date' => $start->format(DATE_FORMAT),
          'start' => $start->format(config('steve.time_format')),
          'end' => $end->format(config('steve.time_format')),
          'description' => $topic,
          'note' => $x['note']
        ];
      }

      return ['data' => $return];
    }

    public function getActivityPieChart(Request $request) {
      $return = [];
      $params = $request->all();

      if(isset($params['type'])){
        if($params['type'] == 'activity')
          $return = $this->getActivityReport($params);
        elseif($params['type'] == 'topic' && !empty($params['value']))
          $return = $this->getTopicReport($params);
      }
      return $return;
    }

    private function getTopicReport($params) {
      $return = [];
      $grayColor = '#777777';
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
