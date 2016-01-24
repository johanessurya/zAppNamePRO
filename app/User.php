<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'password', 'userType', 'email', 'dt', 'showWeekends',
        'dayStartTime', 'dayEndTime', 'CompanyID', 'state', 'OfficeNo',
        'OfficeName', 'zipcode', 'created', 'active', 'firstLogin', 'lastLogin',
        'loginCount', 'expires', 'resetToken', 'remember_token'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $primaryKey = 'id';
    protected $table = 'users';

    public $timestamps = false;

    public static function updateLogin($id) {
      $user = self::find($id);
      $user->loginCount++;

      if(empty($user->firstLogin)) {
        $user->firstLogin = date("Y-m-d H:i:s");
      }else{
        $user->lastLogin = date("Y-m-d H:i:s");
      }

      $user->save();
    }
}
