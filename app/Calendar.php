<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calendar extends MyModel
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
      'user_id', 'categoryID', 'subCategoryID', 'subSubCategoryID', 'clientID', 'title', 'description', 'allDay', 'start', 'end', 'color'
  ];

  protected $primaryKey = 'id';
  protected $table = 'calendar';

  public $timestamps = false;

  public static function createEvent(array $attributes = array()) {
    // Fill with event if repeat
    $rows = [];

    $day = time() + (1*24*60*60);
    $week = time() + (7*24*60*60);
    $month = time() + (30*24*60*60); // Just to test our algorithm

    $calendar = self::create($attributes);

    // Is repeat event
    // if($attributes[''])
    var_dump($attributes, $calendar->id);

    return 'halow from calendar::create';
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
