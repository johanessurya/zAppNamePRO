<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index() {
      return view('dashboard');
    }

    // Create or update user
    public function createUser(Request $request) {
      $request->flash();
      $this->validate($request, [
        'username' => 'required|unique:users',
        'company_name' => 'required',
        'state' => 'required',
        'email' => 'required|unique:users|email',
        'password' => 'required|min:7|one_or_more_lower_char|one_or_more_upper_char|one_or_more_number|confirmed',
        'password_confirmation' => 'required',
        'agree' => 'required'
      ]);
    }
}
