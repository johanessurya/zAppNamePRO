<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CalendarClient extends Model {
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'id', 'calendar_id', 'client_id'
  ];

  protected $primaryKey = 'id';
  protected $table = 'calendar_client';

  public $timestamps = false;
}
