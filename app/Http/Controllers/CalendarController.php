<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Auth;
use DateTime;
use App\Calendar;
use App\Category;
use App\SubCategory;
use App\SubSubCategory;
use App\MyModel;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class CalendarController extends Controller
{
    public function index() {
      $categories = Category::getByUserId(Auth::user())->get();

      $data = [
        'categories' => $categories,
      ];
      return view('calendar', $data);
    }

    // Get an event
    public function get(Request $request) {
      $params = $request->all();
      $event = Calendar::find($params['id']);
      $event = $event->toArray();

      $row = Category::find($event['categoryID']);

      var_dump($event->toArray()); die('get');

      return $event;
    }

    public function save(Request $request) {
      $return = [
        'success' => 0
      ];
      $params = $request->all();

      // Check all Day
      $params['allDay'] = isset($params['allDay']) ? 1 : 0;

      $rules = [
        'category' => 'required',
        'subCategoryID' => 'required',
        'title' => 'required',
        'description' => 'required',
        'client' => 'required',
        'start' => 'required',
        'end' => 'required'
      ];
      $validator = Validator::make($params, $rules);

      if(!$validator->fails()) {
        $return['success'] = 1;

        $test = Calendar::createEvent($params);
      }

      return response()->json($return);
    }

    public function apiEventList() {
      $return = [];
      $rows = Calendar::all();

      foreach($rows as $x){
        $allDay = $x->allDay ? true : false;
        $repeatId = $x->repeat_id == null ? $x->id : $x->repeat_id;

        $return[] = [
          'id' => $repeatId, // I don't know why, but just follow it. See calendar.php in fullcal project demo
          'original_id' => $x->id,
          'allDay' => $allDay,
          'color' => '#587ca3',
          'start' => MyModel::revDateTime($x->start),
          'end' => MyModel::revDateTime($x->end),
          'title' => $x->title
        ];
      }

      return $return;
    }
}
