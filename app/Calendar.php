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

  public static function createEvent(array $attributes = array()) {
    // Create an event
    // This is parent event. All repeat event will get this id as their parent.

    // Correction repeat type
    $repeatType = $attributes['repeat_type'];
    if($repeatType == 'month-2') {
      $attributes['repeat_type'] = 'month';
    }

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
      for($i = 0; $i < $attributes['repeatN']; $i++) {
        // Change repeat_id
        unset($temp['id']);

        switch($repeatType) {
          case 'day':
            $n = 1*(0+1);
            $modify = '+' . $n . ' day';
            break;

          case 'week':
            $n = 7*(0+1);
            $modify = '+' . $n . ' day';
            break;

          case 'month':
            $n = 1*(0+1);
            $modify = '+' . $n . ' month';
            break;

          case 'month-2':
            // second sun of February 2016
            $modify = ':weekOrder :day of :month :year';

            var_dump($dateTime_start->format(config('steve.mysql_datetime_format')));

            die('month-2: TEST');
            break;
        }

        $dateTime_start->modify($modify);
        $dateTime_end->modify($modify);

        $temp['start'] = $dateTime_start->format(DATETIME_FORMAT);
        $temp['end'] = $dateTime_end->format(DATETIME_FORMAT);
        $temp['repeat_id'] = $parentId;

        $rows[] = $temp;
      }

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
