<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Mail;
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

    public function resetPassword(Request $request) {
      $return = redirect('/forgot')->with('message', "We can't find your email in our database.");

      // Get email
      $email = $request->input('email');

      $row = User::where('email', $email)->first();

      if(!empty($row)) {
        // Generate token
        $token = str_random(32);

        $row->resetToken = $token;
        $row->save();

        // Send token link to email
        Mail::send('resetpassword', ['token' => $token], function ($m) use ($row) {
            $email = $row->email;
            $m->from('hello@app.com', 'Your Application');

            $m->to($email, $row->username)->subject('Resetting password!');
        });

        $return = redirect('/login')->with('message', 'Reset password link has been sent to your email.');
      }

      return $return;
    }

    public function reset(Request $request, $token = null) {
      if($request->isMethod('post')) {
        $params = $request->all();
        $token = $params['token'];

        $return = redirect('/reset//' . $token)->with('message', 'Password has been reset.');

        $this->validate($request, [
          'token' => 'required',
          'password' => 'required|alpha_num|min:7|confirmed',
          'password_confirmation' => 'required',
        ]);

        // Get user
        $user = User::where('resetToken', $token)->first();
        if(!empty($user)) {
          $user->password = Hash::make($params['password']);
          $user->save();

          $return = redirect('/login')->with('message', 'Password has been reset.');
        }

        return $return;
      }else{
        $user = User::where('resetToken', $token)->first();

        if(empty($user)) {
          return redirect('/login')->with('message', 'Reset token not found.');
        }

        return view('reset', array('token' => $token));
      }
    }

    public function logout() {
      Auth::logout();

      return redirect('/login')->with('message', 'Sign out successful');
    }
}
