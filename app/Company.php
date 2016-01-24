<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
      'companyID', 'name', 'state',
  ];

  protected $primaryKey = 'companyID';
  protected $table = 'company';

  public $timestamps = false;
}
