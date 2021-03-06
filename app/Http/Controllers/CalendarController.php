<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use DB;
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
      $categories = Category::getByUserId(Auth::user()->id)->get();
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

      $clients = DB::table('calendar_client')->where('calendar_id', $event->id)->get();

      // Convert to array
      $row = $row->toArray();
      $row['category'] = $row['title'];
      $row['description_editable'] = $row['description'];
      $row['clients'] = $clients;
      $row['subCategoryID'] = $event->subCategoryID;
      $row['subSubCategoryID'] = $event->subSubCategoryID;
      $row['event'] = $event;

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
        $params['user_id'] = Auth::user()->id;
        $test = Calendar::createEvent($params);
      }

      return response()->json($return);
    }

    public function apiEventList() {
      $return = [];
      $rows = Calendar::where('user_id', Auth::user()->id)->get();

      foreach($rows as $x){
        $allDay = $x->allDay ? true : false;
        $repeatId = $x->repeat_id == null ? $x->id : $x->repeat_id;
        $category = Category::find($x->categoryID);
        $subCategory = DB::table('subcategory')->where('id', $x->subCategoryID)->first();

        $clients = DB::table('calendar_client')
          ->where('calendar_id', $x->id)
          ->join('client', 'calendar_client.client_id', '=', 'client.id')->take(2)->get();

        $clientName = [];

        foreach($clients as $y) {
          $clientName[] = $y->name;
        }

        if(!empty($clientName)) {
          $clientName = '[' . implode(', ', $clientName) . ']';
        } else {
          $clientName = null;
        }


        $title = $category->abbrev . ' | ' . $subCategory->title . ' ' . $clientName . ' - ' . $x->title;

        $return[] = [
          'id' => $repeatId, // I don't know why, but just follow it. See calendar.php in fullcal project demo
          'original_id' => $x->id,
          'allDay' => $allDay,
          'color' => $category->color,
          'start' => MyModel::revDateTime($x->start),
          'end' => MyModel::revDateTime($x->end),
          'title' => $title
        ];
      }

      return $return;
    }

    public function getClient() {
      $rows = Client::where('user_id', Auth::user()->id)->get();

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
      if(empty($event->repeat_type)) {
        $return['repeat'] = false;
      }

      return response()->json($return);
    }

    // Delete repeat event or non repeat event
    public function delete(Request $request) {
      $params = $request->all();

      // Delete an event
      $event = Calendar::find($params['id']);
      if(!empty($event)) {
        $rows = DB::table('calendar_client')->where('calendar_id', $event->id)->delete();
        $event->delete();

        // Check if param rep_id exist or not
        if(isset($params['rep_id']) && !empty($params['rep_id'])) {
          // Delete ALL associated with this event
          // Delete event(calendar table)
          $events = Calendar::where('repeat_id', $params['rep_id'])->get();

          for($i = 0; $i < count($events); $i++) {
            // Delete calendar_client list
            DB::table('calendar_client')->where('calendar_id', $events[$i]->id)->delete();

            // Delete event
            $events[$i]->delete();
          }
        }
      }
    }

    // Update repeatitive event
    public function update(Request $request) {
      $params = $request->all();

      if(isset($params['method'])) {
        // IF repeatitive event
        // Get list of ID
        $rows = Calendar::where('repeat_id', $params['rep_id'])->get();

        // Repeat each row
        foreach($rows as $y) {
          // Update a row
          $row = Calendar::find($y->id);
          $row->categoryID = $params['categoryID'];
          $row->subCategoryID = $params['subCategoryID'];

          $row->subSubCategoryID = null;
          if(isset($params['subSubCategoryID']))
            $row->subSubCategoryID = $params['subSubCategoryID'];

          $row->title = $params['title'];
          $row->description = $params['description'];
          $row->save();

          // Delete all client associated with this calendar
          $clients = DB::table('calendar_client')->where('calendar_id', $row->id)->delete();

          if(isset($params['clients']) && is_array($params['clients'])){
            // Add new client
            $clients = [];
            foreach($params['clients'] as $x) {
              $clients[] = [
                'calendar_id' => $row->id,
                'client_id' => $x
              ];
            }

            // Save all
            DB::table('calendar_client')->insert($clients);
          }
        }
      } else {
        // Single event
        // Just update a row
        $row = Calendar::find($params['id']);
        $row->categoryID = $params['categoryID'];
        $row->subCategoryID = $params['subCategoryID'];

        $row->subSubCategoryID = null;
        if(isset($params['subSubCategoryID']))
          $row->subSubCategoryID = $params['subSubCategoryID'];

        $row->title = $params['title'];
        $row->description = $params['description'];
        $row->save();

        // Delete all client associated with this calendar
        $clients = DB::table('calendar_client')->where('calendar_id', $row->id)->delete();

        if(isset($params['clients']) && is_array($params['clients'])){
          // Add new client
          $clients = [];
          foreach($params['clients'] as $x) {
            $clientID = $x;
            $temp = $clientID;

            // Check if this client has inserted
            if($temp == 0) {
              // This mean this client haven't created
              $newClient = [
                'user_id' => Auth::user()->id,
                'clientCode' => $clientID,
                'name' => $clientID,
                'gender' => 'Male',
                'type' => 'Startup'
              ];

              $newClient = Client::create($newClient);
              $clientID = $newClient->id;
            }

            $clients[] = [
              'calendar_id' => $row->id,
              'client_id' => $clientID
            ];
          }

          // Save all
          DB::table('calendar_client')->insert($clients);
        }
      }
    }

    public function updateDropDrag(Request $request) {
      $params = $request->all();

      $params['start'] = MyModel::dateTime($params['start']);
      $params['end'] = MyModel::dateTime($params['end']);

      $rows = Calendar::orWhere('id', $params['id'])->orWhere('repeat_id', $params['id'])->orderBy('id', 'asc')->get();

      $start = DateTime::createFromFormat(DATETIME_FORMAT, $params['start']);
      $end = DateTime::createFromFormat(DATETIME_FORMAT, $params['end']);
      $temp_start = null;
      $temp_end = null;

      $interval = null;

      // Insert first row
      $x = $rows[0];
      $x->start = $start->format(DATETIME_FORMAT);
      $x->end = $end->format(DATETIME_FORMAT);
      $x->allDay = $params['allDay'] == 'true' ? 1 : 0;
      $rows[0] = $x;

      for($i = 1; $i < count($rows); $i++) {
        $x = $rows[$i];

        $interval = MyModel::getInterval($x->repeat_type, $start);

        $start->modify($interval);
        $end->modify($interval);

        $repeatType = $x->repeat_type;
        if($repeatType == 'month') {
          // Save old date time to temp variable before shift to next day
          $temp_start = DateTime::createFromFormat(DATETIME_FORMAT, $start->format(DATETIME_FORMAT));
          $temp_end = DateTime::createFromFormat(DATETIME_FORMAT, $end->format(DATETIME_FORMAT));
        }

        // Shift weekend date
        Calendar::shiftWeekendDate($x->repeat_type, $interval, $start, $end);

        // Update their date
        $x->start = $start->format(DATETIME_FORMAT);
        $x->end = $end->format(DATETIME_FORMAT);

        if($repeatType == 'month') {
          // After save datetime with modify on it. Back to old before shiftWeekendDate
          $start = $temp_start;
          $end = $temp_end;
        }

        $x->allDay = $params['allDay'] == 'true' ? 1 : 0;

        $rows[$i] = $x;
      }

      // Save
      foreach($rows as $x)
        $x->save();
    }
}
