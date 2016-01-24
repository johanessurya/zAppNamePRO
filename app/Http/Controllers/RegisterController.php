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
          'password' => 'required|confirmed',
          'password_confirmation' => 'required',
          'agree' => 'required'
        ], $messages);

        if ($validator->fails()) {
          $error = true;
          $data['message'] = $validator->errors()->all();
        }

        if(!$this->companyExist($params['company_name'], $params['state'])) {
          $error = true;

          $data['message'][] = "Company doesn't exists";
        }

        if($error){
          $data['messageType'] = 'danger';

          $request->flash();
          $return = view('register', $data);
        }else{
          $company = Company::where('name', '=', $params['company_name']);
          $company = Company::where('name', '=', 'apple');

          $rows = array(
            'username' => $params['username'],
            'password' => Hash::make($params['password']),
            'email' => $params['email']
          );

          User::create($rows);

          $return = redirect('/login')->with('message', 'Register successful');
        }
      }

      return $return;
    }

    private function companyExist($companyName, $state) {
      $company = Company::where('name', $companyName)
                  ->where('state', $state)->get();

      return count($company) > 0;
    }

    public function test() {
      $company = Company::where('name', '=', 'apple')->get()->first();
      var_dump($company->name);

      die('test');
    }
}
