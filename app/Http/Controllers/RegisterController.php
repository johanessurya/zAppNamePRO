<?php

namespace App\Http\Controllers;

use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
          'state' => 'required',
          'email' => 'required|unique:users|email',
          'password' => 'required|alpha_num|min:7|confirmed',
          'password_confirmation' => 'required',
          'agree' => 'required'
        ], $messages);

        if ($validator->fails()) {
          $error = true;
          $data['message'] = $validator->errors()->all();
        }

        $company = $this->getCompany($params['company_name'], $params['state']);
        if(empty($company)){
          $error = true;

          $data['message'][] = "Company doesn't exists";
        }

        if($error){
          $data['messageType'] = 'danger';

          $request->flash();
          $return = view('register', $data);
        }else{
          // Get today and today+14
          $today=time();
          $exp=$today + (14*24*60*60);
          $date = date("Y-m-d H:i:s", $today);
          $date2 = date("Y-m-d H:i:s", $exp);

          $rows = array(
            'username' => $params['username'],
            'password' => Hash::make($params['password']),
            'email' => $params['email'],
            'created' => $today,
            'expires' => $date2,
            'CompanyID' => $company->companyID,
            'created' => date("Y-m-d H:i:s")
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
      $c = $this->companyExist('Apple','ca');

      var_dump($c);

      die('test');
    }
}
