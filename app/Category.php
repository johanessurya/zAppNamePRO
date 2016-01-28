<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'CompanyID', 'title', 'abbrev', 'description', 'color'
  ];

  protected $primaryKey = 'id';
  protected $table = 'category';

  public $timestamps = false;
}
