<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CalendarController extends Controller
{
    public function save(Request $request) {
      $return = [
        'success' => 1
      ];
      $params = $request->all();

      var_dump($params);
      die('test');

      return json_encode($return);
    }
}
