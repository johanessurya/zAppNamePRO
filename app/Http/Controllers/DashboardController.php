<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Hash;
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

      $this->validate($request, [
        'username' => 'required|unique:users',
        'company_name' => 'required',
        'state' => 'required',
        'email' => 'required|unique:users|email',
        'password' => 'required|min:7|one_or_more_lower_char|one_or_more_upper_char|one_or_more_number|confirmed',
        'password_confirmation' => 'required'
      ]);

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

      User::create($rows);

      return redirect('/dashboard/user')->with('message', 'User has been created');
    }
}
