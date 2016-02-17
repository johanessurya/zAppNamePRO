<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DateTime;

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
}
