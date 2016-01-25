<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Hash;
use Session;
use App\User;
use App\Company;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index() {
      return view('dashboard');
    }

    // Create or update user
    public function createUser(Request $request) {
      $params = $request->all();

      $rules = [
        'username' => 'required|unique:users',
        'company_name' => 'required',
        'state' => 'required',
        'email' => 'required|unique:users|email',
        'password' => 'required|min:7|one_or_more_lower_char|one_or_more_upper_char|one_or_more_number|confirmed',
        'password_confirmation' => 'required'
      ];
      $this->validate($request, $rules);
      $request->flash();

      // Check if this user exists
      $user = null;
      if(!empty($params['id'])) {
        $user = User::find($params['id']);
      }

      $companyName = $params['company_name'];
      $state = $params['state'];
      $company = Company::where('name', $companyName)->where('state', $state)->first();
      if(!empty($company)){
        $company = $company->companyID;
      } else {
        $company = 0;
      }

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
        'CompanyID' => $company,
        'created' => date("Y-m-d H:i:s")
      );

      $message = null;
      if(!empty($user)) {
        $user->username = $rows['username'];
        $user->password = $rows['password'];
        $user->email = $rows['email'];
        $user->created = $rows['created'];
        $user->expires = $rows['expires'];
        $user->CompanyID = $rows['CompanyID'];
        $user->created = $rows['created'];
        $user->save();

        $message = 'User has been updated';
      } else {
        User::create($rows);
        $message = 'User has been created';
      }

      return redirect('/dashboard')->with('message', $message);
    }

    public function editUser(Request $request, $id) {
     $user = User::find($id);
     return view('dashboard.edituser', array('title' => 'Edit User', 'user' => $user));
   }

   public function deleteUser($id) {
     User::find($id)->delete();
     return redirect('/dashboard')->with('message', 'Delete user successful');
   }
}
