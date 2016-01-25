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

      $dateTime = date(DATETIME_FORMAT);
      if(empty($user->firstLogin) || $user->firstLogin == '0000-00-00 00:00:00') {
        $user->firstLogin = $dateTime;
      }

      $user->lastLogin = $dateTime;
      $user->save();
    }

    // ====== Accessor =======
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

    // ====== Mutator =======
    /**
     * Change first login format to d-m-Y H:i:s
     */
    public function setFirstLoginAttribute($value) {
      $this->attributes['firstLogin'] = self::revDateTime($value);
    }

    /**
     * Change last login format to d-m-Y H:i:s
     */
    public function setLastLoginAttribute($value) {
      $this->attributes['lastLogin'] = self::revDateTime($value);
    }

    /**
     * Change created format to d-m-Y H:i:s
     */
    public function setCreatedAttribute($value) {
      $this->attributes['created'] = self::revDateTime($value);
    }

    /**
     * Change created format to d-m-Y H:i:s
     */
    public function setExpiresAttribute($value) {
      $this->attributes['expires'] = self::revDate($value);
    }

    // Reverse DateTime format to MySql standard
    // param $value format : m/d/y H:i
    public static function revDateTime($value) {
      $return = null;
      if(!empty($value) && $value != '0000-00-00 00:00:00') {
        $dateTime = DateTime::createFromFormat(DATETIME_FORMAT, $value);

        if($value == '25/01/16 18:42') {
          var_dump($dateTime, $value, DATETIME_FORMAT);
          die('bool');
        }

        $return = $dateTime->format('Y-m-d H:i:00');
      }

      return $return;
    }

    // Reverse Date format to MySql standard
    // param $value format : d/m/y
    public static function revDate($value) {
      $return = null;

      if(!empty($value)) {
        $dateTime = DateTime::createFromFormat(DATE_FORMAT, $value);

        if(!$dateTime) {
          var_dump($dateTime, $value, DATE_FORMAT); die('bool');
        }

        $return = $dateTime->format('Y-m-d 00:00:00');
      }

      return $return;
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
