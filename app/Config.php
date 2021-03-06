<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Config extends Model {
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
      'key_name', 'value'
  ];

  protected $primaryKey = 'key_name';
  protected $table = 'config';

  public $timestamps = false;
}
