<?php

namespace App\Http\Controllers;

use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use DateTime;
use Mail;
use App\User;
use App\Company;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    public function index(Request $request) {
      $params = $request->all();

      $return = null;
      $error = false;
      $company = null;

      // Default message
      $data = array(
        'message' => array('Register successful'),
        'messageType' => 'success'
      );

      // Check if request is post
      if($request->isMethod('post')) {
        // Custom validator message
        $messages = [
          'agree.required' => 'Please check "I agree to the terms"',
        ];

        // Declare validator
        $validator = Validator::make($request->all(), [
          'username' => 'required|unique:users',
          'company_name' => 'required',
          'state' => 'required|max:2',
          'email' => 'required|unique:users|email',
          'password' => 'required|alpha_num|min:7|confirmed',
          'password_confirmation' => 'required',
          'agree' => 'required'
        ], $messages);

        if ($validator->fails()) {
          $error = true;
          $data['message'] = $validator->errors()->all();
        }

        /* Password validation
        - One lower case character
        - One upper case character
        - A Number
        */
        if (!preg_match("#[0-9]+#", $params['password'])) {
            $data['message'][] = "Password must include at least one number!";
        }
        if (!preg_match("#[a-z]+#", $params['password'])) {
            $data['message'][] = "Password must include at least one lower case character!";
        }
        if (!preg_match("#[A-Z]+#", $params['password'])) {
            $data['message'][] = "Password must include at least one upper case character!";
        }

        $company = $this->getCompany($params['company_name'], $params['state']);
        $companyId = 0;
        if(!empty($company))
          $companyId = $company->companyID;

        if($error){
          $data['messageType'] = 'danger';

          $request->flash();
          $return = view('register', $data);
        }else{
          // Get today and today+14
          $today=time();
          $exp=$today + (15*24*60*60);
          $created = date(DATETIME_FORMAT, $today);
          $expires = date(DATE_FORMAT, $exp);

          // Check if active record or not
          $active = 0;
          if(preg_match("#(.org|.edu|.gov|.us)$#", $params['email']))
            $active = 1;

          $rows = array(
            'username' => $params['username'],
            'password' => Hash::make($params['password']),
            'email' => $params['email'],
            'active' => $active,
            'created' => $created,
            'expires' => $expires,
            'CompanyID' => $companyId
          );

          User::create($rows);

          $return = redirect('/login')->with('message', 'Register successful');
        }
      }

      return $return;
    }

    private function getCompany($companyName, $state) {
      $company = Company::where('name', $companyName)
                  ->where('state', $state)->first();

      return $company;
    }

    public function test() {
      Mail::send([], [], function ($m){
          $m->from('hello@app.com', 'Your Application');

          $emails = array();
          $emails[] = 'stevez@zaharalabs.com';
          $emails[] = 'szaharakis@po-box.esu.edu';
          $emails[] = 'stevez@zlabs.com';
          $emails[] = 'johanes.surya43@gmail.com';

          foreach($emails as $x) {
            $m->to($emails, 'JustTest')->subject('Testing email from steve.johanessurya.com');
          }
      });
    }

    public function test2() {
      $datetime = new DateTime('2016-01-31 11:39:49');
      $x = $datetime->format(config('steve.mysql_datetime_format'));
      // $datetime->modify('last weeks of next month');
      // $datetime->modify('next month');
      $datetime->modify('second sun of February 2016');
      $y = $datetime->format(config('steve.mysql_datetime_format'));
      var_dump($x, $y);
    }
}
