<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Hash;
use Auth;
use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function index(Request $request) {
      $return = null;
      $this->validate($request, [
        'username' => 'required',
        'password' => 'required',
      ]);

      $params = $request->all();
      if(Auth::attempt($params)) {
        User::updateLogin(Auth::user()->id);
        return redirect()->intended('/dashboard');
      } else {
        return redirect('/login')->with('message', 'Incorrect username and password');
      }
    }

    public function logout() {
      Auth::logout();

      return redirect('/login')->with('message', 'Sign out successful');
    }
}
