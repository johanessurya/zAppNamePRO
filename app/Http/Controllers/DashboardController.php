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
     $userType = array('User', 'Manager', 'Admin');
     $data = array(
       'title' => 'Edit User',
       'user' => $user,
       'userType' => $userType
     );
     return view('dashboard.edituser', $data);
   }

   public function doEditUser(Request $request) {
     $params = $request->all();

     // Search users.id is exist or not
     $user = User::find($params['id']);

     $message = 'User not found';
     if(!empty($user)) {
       // Grab all user except this this user
       $users = User::where('id', '!=', $params['id'])->get();

       $emails = array();
       $usernames = array();
       foreach($users as $x) {
         $emails[] = $x->email;
         $usernames[] = $x->username;
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

       $user->username = $params['username'];
       $user->userType = $params['userType'];
       $user->CompanyID = $params['CompanyID'];
       $user->active = $params['active'];
       $user->firstLogin = $params['firstLogin'];
       $user->lastLogin = $params['lastLogin'];
       $user->loginCount = $params['loginCount'];

       $user->save();

       $message = 'User has been updated';
     }

     return redirect('/dashboard')->with('message', $message);
   }

   public function deleteUser($id) {
     User::find($id)->delete();
     return redirect('/dashboard')->with('message', 'Delete user successful');
   }
}
