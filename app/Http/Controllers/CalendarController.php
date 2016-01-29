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
      $categories = Category::where('CompanyID', 0)->get();
      $subCategories = SubCategory::all();

      $data = [
        'categories' => $categories,
        'subCategories' => $subCategories
      ];
      return view('calendar', $data);
    }

    public function save(Request $request) {
      $return = [
        'success' => 0
      ];
      $params = $request->all();

      $rules = [
        'category' => 'required',
        'subCategoryID' => 'required',
        'title' => 'required',
        'description' => 'required',
        'client' => 'required',
        'allDay' => 'required'
      ];
      $validator = Validator::make($params, $rules);

      if(!$validator->fails()) {
        $return['success'] = 1;

        // Set start, and field with user start, and
        $dateTime = new DateTime();
        $params['start'] = $dateTime->format(DATETIME_FORMAT);
        $dateTime->modify('+1 hour');

        $params['end'] = $dateTime->format(DATETIME_FORMAT);

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