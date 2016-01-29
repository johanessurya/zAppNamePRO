<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use User;

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

  public static function getByUserId($user) {
    $return = self::orWhere('CompanyID', null)
      ->orWhere('CompanyID', 0)
      ->orWhere('CompanyID', $user->CompanyiD);

    return $return;
  }
}
