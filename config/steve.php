<?php

return [
  // Depend in database table definition.
  // Gender const
  'gender' => [
    'Male' => 'Male',
    'Female' => 'Female'
  ],

  // Client type const
  'client_type' => [
    'Startup' => 'Startup',
    'Small' => 'Small',
    'Medium' => 'Medium',
    'Large' => 'Large'
  ],

  // Time list
  'time_list' => unserialize('a:23:{i:0;s:4:"1 AM";i:1;s:4:"2 AM";i:2;s:4:"3 AM";i:3;s:4:"4 AM";i:4;s:4:"5 AM";i:5;s:4:"6 AM";i:6;s:4:"7 AM";i:7;s:4:"8 AM";i:8;s:4:"9 AM";i:9;s:5:"10 AM";i:10;s:5:"11 AM";i:11;s:5:"12 PM";i:12;s:4:"1 PM";i:13;s:4:"2 PM";i:14;s:4:"3 PM";i:15;s:4:"4 PM";i:16;s:4:"5 PM";i:17;s:4:"6 PM";i:18;s:4:"7 PM";i:19;s:4:"8 PM";i:20;s:4:"9 PM";i:21;s:5:"10 PM";i:22;s:5:"11 PM";}'),

  // Dt list
  'dt' => [5,10,15, 20, 25, 30, 35, 45, 50, 55, 60],

  'mysql_datetime_format' => 'Y-m-d H:i:s',

  'time_format' => 'H:i:s'
]
?>
