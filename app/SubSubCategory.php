<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubSubCategory extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'category_id', 'title', 'abbrev', 'description', 'color'
  ];

  protected $primaryKey = 'id';
  protected $table = 'subsubcategory';

  public $timestamps = false;
}
