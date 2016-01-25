<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

use DateTime;

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

      if(empty($user->firstLogin) || '0000-00-00 00:00:00') {
        $user->firstLogin = date("Y-m-d H:i:s");
      }else{
        $user->lastLogin = date("Y-m-d H:i:s");
      }

      $user->save();
    }

    /**
     * Change first login format to d-m-Y H:i:s
     */
    public function getFirstLoginAttribute($value) {
      return self::dateTime($value);
    }

    /**
     * Change last login format to d-m-Y H:i:s
     */
    public function getLastLoginAttribute($value) {
      return self::dateTime($value);
    }

    /**
     * Change created format to d-m-Y H:i:s
     */
    public function getCreatedAttribute($value) {
      return self::dateTime($value);
    }

    /**
     * Change created format to d-m-Y H:i:s
     */
    public function getExpiresAttribute($value) {
      return self::date($value);
    }

    public static function dateTime($value) {
      $return = null;
      if(!empty($value) && $value != '0000-00-00 00:00:00') {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $value);

        $return = $dateTime->format(DATETIME_FORMAT);
      }

      return $return;
    }

    public static function date($value) {
      $return = null;

      if(!empty($value)) {
        $dateTime = DateTime::createFromFormat('Y-m-d', $value);

        $return = $dateTime->format(DATE_FORMAT);
      }

      return $return;
    }
}
