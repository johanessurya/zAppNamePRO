<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Auth;
use DateTime;
use App\Calendar;
use App\Client;
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
      $clients = Client::where('user_id', Auth::user()->id)->get();

      $data = [
        'categories' => $categories,
        'clients' => $clients
      ];
      return view('calendar', $data);
    }

    // Get an event
    public function get(Request $request) {
      $params = $request->all();
      // Get event
      $event = Calendar::find($params['id']);

      // Get the category
      $row = Category::find($event->categoryID);

      // Convert to array
      $row = $row->toArray();
      $row['category'] = $row['title'];
      $row['description_editable'] = $row['description'];

      return response()->json($row);
    }

    public function save(Request $request) {
      $return = [
        'success' => 0
      ];
      $params = $request->all();

      // Check all Day
      $params['allDay'] = isset($params['allDay']) ? 1 : 0;

      $rules = [
        'categoryID' => 'required',
        'subCategoryID' => 'required',
        'title' => 'required',
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
        $category = Category::find($x->categoryID);

        $return[] = [
          'id' => $repeatId, // I don't know why, but just follow it. See calendar.php in fullcal project demo
          'original_id' => $x->id,
          'allDay' => $allDay,
          'color' => $category->color,
          'start' => MyModel::revDateTime($x->start),
          'end' => MyModel::revDateTime($x->end),
          'title' => $x->title
        ];
      }

      return $return;
    }

    public function getClient() {
      $rows = Client::all();

      return $rows;
    }

    public function checkRep(Request $request) {
      $return = [
        'repeat' => true
      ];
      
      // Get parameters
      $params = $request->all();

      // Get an event
      $event = Calendar::find($params['id']);

      // Check if repetition event or not
      if($event->id == $event->repeat_id || $event->repeat_id == null) {
        $return['repeat'] = false;
      }

      return response()->json($return);
    }
}
