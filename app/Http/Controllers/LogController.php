<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Calendar;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class LogController extends Controller
{
    public function getActivity() {
      $return = [];
      $rows = Calendar::all();

      foreach($rows as $x) {
        $return[] = [
          'date' => $x['start'],
          'start' => $x['start'],
          'end' => $x['end'],
          'description' => $x['description'],
          'note' => $x['note']
        ];
      }

      return ['data' => $return];
    }
}
