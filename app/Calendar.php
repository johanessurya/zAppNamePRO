<?php

namespace App;

use DateTime;
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
    // var_dump($attributes);
    $calendar = self::create($attributes);
    $calendar->repeat_id = $calendar->id;
    $calendar->save();

    // Is repeat event?
    // Fill with event if repeat
    $rows = [];
    if($attributes['repeat_type'] && $attributes['repeatN'] > 0) {
      $temp = $attributes;

      $parentId = $calendar->id;

      $dateTime_start = DateTime::createFromFormat(DATETIME_FORMAT, $calendar->start);
      $dateTime_end = DateTime::createFromFormat(DATETIME_FORMAT, $calendar->end);
      for($i = 0; $i < $attributes['repeatN']; $i++) {
        // Change repeat_id
        unset($temp['id']);

        switch($calendar->repeat_type) {
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
        }

        $dateTime_start->modify($modify);
        $dateTime_end->modify($modify);

        $temp['start'] = $dateTime_start->format(DATETIME_FORMAT);
        $temp['end'] = $dateTime_end->format(DATETIME_FORMAT);
        $temp['repeat_id'] = $parentId;

        $rows[] = $temp;
      }

      // Save other repeat event
      foreach($rows as $x)
        self::create($x);
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
