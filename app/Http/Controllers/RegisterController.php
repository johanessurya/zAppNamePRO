<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    public function index(Request $request) {
      // Validator
      $validator = Validator::make($request->all(), [
        'full-name' => 'required',
        'email' => 'required'
      ]);

      if ($validator->fails()) {
          return redirect()->back()->withErrors($validator->errors());
      }

      $post['name'] = $request->input();

      if(!empty($post['name'])) {
        var_dump($post);
        die('index');
      }

      return view('register');
    }
}
