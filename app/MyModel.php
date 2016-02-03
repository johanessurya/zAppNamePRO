<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DateTime;

class MyModel extends Model
{
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

      if(is_bool($dateTime)) {
        var_dump($dateTime, $value); die;
      }

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

  public static function getInterval($repeat_type) {
    $modify = null;

    switch($repeat_type) {
      case 'day':
        $n = 1*(0+1);
        $modify = '+' . $n . ' day';
        break;

      case 'week':
        $n = 7*(0+1);
        $modify = '+' . $n . ' day';
        break;

      case 'month':
        $n = 1*(0+1);
        $modify = '+' . $n . ' month';
        break;
    }

    return $modify;
  }
}
