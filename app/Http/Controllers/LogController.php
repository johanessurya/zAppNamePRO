<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DateTime;

use DB;
use App\MyModel;
use App\Calendar;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class LogController extends Controller
{
    public function getActivity(Request $request) {
      $return = [];
      $params = $request->all();

      $start = DateTime::createFromFormat(DATETIME_FORMAT, $params['start']);
      $end = DateTime::createFromFormat(DATETIME_FORMAT, $params['end']);
      $start = $start->format(config('steve.mysql_datetime_format'));
      $end = $end->format(config('steve.mysql_datetime_format'));

      $rows = Calendar::where('start', '>=', $start)->where('end', '<=', $end)->get();

      // var_dump($params, $rows); die('test');

      $start = null;
      $end = null;
      foreach($rows as $x) {
        $start = DateTime::createFromFormat(DATETIME_FORMAT, $x['start']);
        $end = DateTime::createFromFormat(DATETIME_FORMAT, $x['end']);

        $return[] = [
          'date' => $start->format(DATE_FORMAT),
          'start' => $start->format(config('steve.time_format')),
          'end' => $end->format(config('steve.time_format')),
          'description' => $x['description'],
          'note' => $x['note']
        ];
      }

      return ['data' => $return];
    }

    public function getActivityPieChart(Request $request) {
      $return = [];
      $params = $request->all();

      $start = DateTime::createFromFormat(DATETIME_FORMAT, $params['start']);
      $end = DateTime::createFromFormat(DATETIME_FORMAT, $params['end']);
      $start = $start->format(config('steve.mysql_datetime_format'));
      $end = $end->format(config('steve.mysql_datetime_format'));

      // $rows = Calendar::where('start', '>=', $start)->where('end', '<=', $end)->get();
      $rows = Calendar::select('categoryID', DB::raw('COUNT(*) as total'))
              ->where('start', '>=', $start)
              ->where('end', '<=', $end)
              ->groupBy('categoryID')
              ->get();

      foreach($rows as $x) {
        $return[] = [
          'value' => $x['total'],
          'color' => $x->category->color,
          'highlight' => $x->category->color,
          'label' => $x->category->title,
        ];
      }

      return $return;
    }

    public function getComment($key_name) {
      $row = DB::table('config')->where('key_name', $key_name)->first();

      return response()->json($row);
    }
}
