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

  public static function getInterval($repeat_type, DateTime $dateTime = null) {
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

      case 'month-2':
        $tempWeekOrder = ['first', 'second', 'third', 'fourth', 'last'];

        // second sun of February 2016
        $day = $dateTime->format('D');
        $month = self::getNextMonth($dateTime->getTimestamp())->format('F');
        $year = $dateTime->format('Y');
        $weekOrder = self::weekOfMonth($dateTime->getTimestamp()) - 1;
        // first Thursday of next month
        $modify = ':weekOrder :day of next month';

        $modify = str_replace(':weekOrder', $tempWeekOrder[$weekOrder], $modify);
        $modify = str_replace(':day', $day, $modify);
        break;
    }

    return $modify;
  }

  private static function weekOfMonth($timestamp) {
    //Get the first day of the month.
    $firstOfMonth = strtotime(date("Y-m-01", $timestamp));
    //Apply above formula.
    return intval(date("W", $timestamp)) - intval(date("W", $firstOfMonth)) + 1;
  }

  private static function getNextMonth($timestamp) {
    $now = new DateTime();
    $now->setTimestamp($timestamp);
    $year = $now->format('Y');
    $month = $now->format('n') + 1;
    if($month > 12)
      $month = 1;

    $day = 1;
    $now->setDate($year, $month, $day);

    return $now;
  }
}
