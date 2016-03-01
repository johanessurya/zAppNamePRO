<?php

namespace App;

use DB;
use DateTime;
use Auth;
use Illuminate\Database\Eloquent\Model;

class Calendar extends MyModel
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
      'user_id', 'categoryID', 'subCategoryID', 'subSubCategoryID', 'clientID', 'title', 'description', 'allDay', 'start', 'end', 'color', 'repeat_type', 'repeat_id'
  ];

  protected $primaryKey = 'id';
  protected $table = 'calendar';

  public $timestamps = false;

  public function save(array $options = array()) {
    // Before save
    $start = DateTime::createFromFormat(DATETIME_FORMAT, $this->start);
    $end = DateTime::createFromFormat(DATETIME_FORMAT, $this->end);

    $interval = ($end->getTimestamp() - $start->getTimestamp()) / 60; // Get second then convert to minutes

    $this->duration = $interval;

    parent::save($options);
    // After save
  }

  /**
   * Get the category record associated with the calendar.
   */
  public function category() {
    return $this->hasOne('App\Category', 'id', 'categoryID');
  }

  // Get a row of sub category
  public function subCategory() {
    return $this->hasOne('App\SubCategory', 'id', 'subCategoryID');
  }

  // Get a row of sub category
  public function client() {
    return $this->hasMany('App\CalendarClient', 'calendar_id', 'id');
  }

  public static function createEvent(array $attributes = array()) {
    // Create an event
    // This is parent event. All repeat event will get this id as their parent.

    // Correction repeat type
    $repeatType = $attributes['repeat_type'];

    $calendar = self::create($attributes);
    $calendar->repeat_id = $calendar->id;
    $calendar->save();

    // Is clientID empty
    $clientsID = [];
    if(!empty($attributes['clients'])) {
      // Get ClientID list
      foreach($attributes['clients'] as $x) {
        // y for Client ID
        $y = $x;
        $tempX = $x;

        // Parse tempX to integer
        settype($tempX, 'integer');

        // NOW tempX is integer
        if($tempX == 0) {
          // Not available then create new client

          // A row of client
          $row = Client::create([
            'user_id' => Auth::user()->id,
            'clientCode' => $x, // client name
            'name' => $x, // client name
            'gender' => 'Male',
            'type' => 'Startup',
            'note' => null
          ]);

          $y = $row->id;
        }

        $clientsID[] = $y;
      }

      // Create calendar client to be inserted
      $calClient = [];
      foreach($clientsID as $y)
        $calClient[] = [
          'calendar_id' => $calendar->id,
          'client_id' => $y
        ];

      // Insert clientID to calendar_client
      DB::table('calendar_client')->insert($calClient);
    }

    // Is repeat event?
    // Fill with event if repeat
    $rows = [];

    // If repeatable event
    if($attributes['repeat_type'] && $attributes['repeatN'] >= 1) {
      $temp = $attributes;

      $parentId = $calendar->id;

      $dateTime_start = DateTime::createFromFormat(DATETIME_FORMAT, $calendar->start);
      $dateTime_end = DateTime::createFromFormat(DATETIME_FORMAT, $calendar->end);

      $interval = $dateTime_start->diff($dateTime_end);

      $temp_start = null;
      $temp_end = null;

      $modify = self::getInterval($repeatType, $dateTime_start);
      // var_dump($modify, $calendar); die('modify');
      for($i = 0; $i < $attributes['repeatN']; $i++) {
        // Change repeat_id
        unset($temp['id']);

        $dateTime_start->modify($modify);
        $dateTime_end->modify($modify);

        // Fix bug for end of month. It would cause error
        // Clone date time
        $temp_start = DateTime::createFromFormat(DATETIME_FORMAT, $dateTime_start->format(DATETIME_FORMAT));

        // Add by +1 day
        $temp_start->modify($interval->format('%R%a days')); // make day dynamic by substract end date and start date

        if($repeatType == 'month') {
          // Replace with
          $dateTime_end = $temp_start;

          // Save old date time to temp variable before shift to next day
          $temp_start = DateTime::createFromFormat(DATETIME_FORMAT, $dateTime_start->format(DATETIME_FORMAT));
          $temp_end = DateTime::createFromFormat(DATETIME_FORMAT, $dateTime_end->format(DATETIME_FORMAT));
        }

        self::shiftWeekendDate($repeatType, $modify, $dateTime_start, $dateTime_end);

        $temp['start'] = $dateTime_start->format(DATETIME_FORMAT);
        $temp['end'] = $dateTime_end->format(DATETIME_FORMAT);
        $temp['repeat_id'] = $parentId;

        if($repeatType == 'month') {
          // After save datetime with modify on it. Back to old before shiftWeekendDate
          $dateTime_start = $temp_start;
          $dateTime_end = $temp_end;
        }

        $rows[] = $temp;
      }

      // var_dump($calendar->start, $calendar->end);
      // var_dump($rows); die;

      // Save other repeat event
      foreach($rows as $x) {
        $calendar = self::create($x);

        // Create calendar client to be inserted
        $calClient = [];
        foreach($clientsID as $y)
          $calClient[] = [
            'calendar_id' => $calendar->id,
            'client_id' => $y
          ];

        // Insert clientID to calendar_client
        DB::table('calendar_client')->insert($calClient);
      }
    }

    return true;
  }

  public static function shiftWeekendDate($repeatType, $modify, $dateTime_start, $dateTime_end) {
    // Shift day if weekend
    if($repeatType == 'month' || $repeatType == 'month-2') {
      $modify = '+1 day';
      // Skip sunday or saturday
      $dayname = $dateTime_start->format('l');
      if($dayname == 'Saturday') {
        $dateTime_start->modify($modify);
        $dateTime_end->modify($modify);
      }
      $dayname = $dateTime_start->format('l');
      if($dayname == 'Sunday') {
        $dateTime_start->modify($modify);
        $dateTime_end->modify($modify);
      }
    } else {
      // Skip sunday or saturday
      $dayname = $dateTime_start->format('l');
      if($dayname == 'Saturday') {
        $dateTime_start->modify($modify);
        $dateTime_end->modify($modify);
      }
      $dayname = $dateTime_start->format('l');
      if($dayname == 'Sunday') {
        $dateTime_start->modify($modify);
        $dateTime_end->modify($modify);
      }
    }
  }

  // ====== Accessor =======
  /**
   * Change first login format to d-m-Y H:i:s
   */
  public function getStartAttribute($value) {
    return self::dateTime($value);
  }

  /**
   * Change end login format to d-m-Y H:i:s
   */
  public function getEndAttribute($value) {
    return self::dateTime($value);
  }

  // ====== Mutator =======
  /**
   * Change first login format to d-m-Y H:i:s
   */
  public function setStartAttribute($value) {
    $this->attributes['start'] = self::revDateTime($value);
  }

  /**
   * Change end login format to d-m-Y H:i:s
   */
  public function setEndAttribute($value) {
    $this->attributes['end'] = self::revDateTime($value);
  }
}
