<?php

namespace App\Http\Controllers;

use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    public function index(Request $request) {
      $data = array(
        'message' => array()
      );

      $messages = [
        'agree.required' => 'Please check "I agree to the terms" checkbox',
      ];

      // Validator
      $validator = Validator::make($request->all(), [
        'full-name' => 'required',
        'email' => 'required|unique:users|email',
        'password' => 'required',
        'retype-password' => 'required',
        'agree' => 'required'
      ], $messages);

      if ($validator->fails()) {
        $data['message'] = $validator->errors()->all();
      } else {
        $params = $request->all();
        $rows = array(
          'username' => $params['full-name'],
          'password' => Hash::make($params['password']),
          'email' => $params['email']
        );

        User::create($rows);
      }

      return view('register', $data);
    }
}
