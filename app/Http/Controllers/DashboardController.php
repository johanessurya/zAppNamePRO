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
        'userType' => 'required|in:0,1,2',
        'email' => 'required|unique:users|email',
        'active' => 'required',
        'loginCount' => 'numeric'
      ];
      $this->validate($request, $rules);
      $request->flash();

      // Check if this user exists
      $user = null;
      if(!empty($params['id'])) {
        $user = User::find($params['id']);
      }

      // Get today and today+14
      $today=time();
      $exp=$today + (14*24*60*60);
      $date = date("Y-m-d H:i:s", $today);
      $expires = date("Y-m-d H:i:s", $exp);

      $rows = array(
        'username' => $params['username'],
        'userType' => $params['userType'],
        'email' => $params['email'],
        'CompanyID' => $params['CompanyID'],
        'active' => $params['active'],
        'firstLogin' => $params['firstLogin'],
        'lastLogin' => $params['lastLogin'],
        'loginCount'=> $params['loginCount'],
        'created' => $today,
        'expires' => $expires
      );

      User::create($rows);
      $message = 'User has been created';

      return redirect('/dashboard')->with('message', $message);
    }

   public function editUser(Request $request, $id) {
     $user = User::find($id);
     return view('dashboard.edituser', array('title' => 'Edit User', 'user' => $user));
   }

   public function doEditUser(Request $request) {
     $params = $request->all();

     // Search users.id is exist or not
     $user = User::where('id', $params['id'])->first();
     if(!empty($user)) {
       // Grab all user except this this user
       $users = User::where('id', '!=', $params['id'])->get();

       $emails = array();
       $usernames = array();
       foreach($users as $user) {
         $emails[] = $user->email;
         $usernames[] = $user->username;
       }

       // Do validate
       $rules = [
         'username' => 'required|not_in:' . implode(',', $usernames),
         'userType' => 'required|in:0,1,2',
         'email' => 'required|email|not_in:' . implode(',', $emails),
         'active' => 'required',
         'loginCount' => 'numeric'
       ];
       $this->validate($request, $rules);
       $request->flash();

       // Do user validate
     }

     return redirect('/dashboard/user');
   }

   public function deleteUser($id) {
     User::find($id)->delete();
     return redirect('/dashboard')->with('message', 'Delete user successful');
   }
}
