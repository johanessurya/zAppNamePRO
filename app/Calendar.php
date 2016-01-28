<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
      'user_id', 'categoryID', 'subCategoryID', 'subSubCategoryID' 'clientID', 'title', 'description', 'allDay', 'start', 'end', 'color',
  ];

  protected $primaryKey = 'id';
  protected $table = 'calendar';

  public $timestamps = false;
}
