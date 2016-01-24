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
          'username' => 'required',
          'company_name' => 'required',
          'state' => 'required',
          'email' => 'required|unique:users|email',
          'password' => 'required|confirmed',
          'password_confirmation' => 'required',
          'agree' => 'required'
        ], $messages);

        if ($validator->fails()) {
          $data['message'] = $validator->errors()->all();
          $data['messageType'] = 'danger';
        } else {
          $params = $request->all();

          $company = Company::where('name', '=', $params['company_name']);
          $company = Company::where('name', '=', 'apple');

          $rows = array(
            'username' => $params['username'],
            'password' => Hash::make($params['password']),
            'email' => $params['email']
          );

          var_dump($rows, $company);
          die('test');
          User::create($rows);
        }
      }

      var_dump($data);
      die('out if');
      return view('register', $data);
    }
}
