<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DateTime;
use Hash;
use Session;
use Auth;
use App\User;
use App\Client;
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
      $exp=$today + (15*24*60*60);
      $date = date(DATETIME_FORMAT, $today);
      $expires = date(DATE_FORMAT, $exp);

      $firstLogin = null;
      if(!empty($params['firstLogin'])) $firstLogin = $params['firstLogin'];

      $lastLogin = null;
      if(!empty($params['lastLogin'])) $lastLogin = $params['lastLogin'];

      $rows = array(
        'username' => $params['username'],
        'userType' => $params['userType'],
        'email' => $params['email'],
        'CompanyID' => $params['CompanyID'],
        'active' => $params['active'],
        'firstLogin' => $firstLogin,
        'lastLogin' => $lastLogin,
        'loginCount'=> $params['loginCount'],
        'created' => $date,
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
       $user->email = $params['email'];
       $user->CompanyID = $params['CompanyID'];
       $user->active = $params['active'];
       $user->firstLogin = $params['firstLogin'];
       $user->lastLogin = $params['lastLogin'];
       $user->loginCount = $params['loginCount'];
       $user->expires = $params['expires'];

       $user->save();

       $message = 'User has been updated';
     }

     return redirect('/dashboard')->with('message', $message);
   }

   public function deleteUser($id) {
     User::find($id)->delete();
     return redirect('/dashboard')->with('message', 'Delete user successful');
   }

   public function setActiveUser($id) {
     $return = redirect('/dashboard')->with('message', 'User ID not found!');

     $user = User::find($id);

     if(!empty($user)) {
       $exp = time() + (15*24*60*60);
       $expires = date(DATE_FORMAT, $exp);

       $user->active = 1;
       $user->expires = $expires;
       $user->save();

       $return = redirect('/dashboard')->with('message', 'Set active successful. Expires +15 days');
     }

     return $return;
   }

   public function createClient(Request $request) {
     $params = $request->all();

     // Validation
     $rules = [
       'user_id' => 'required|exists:users,id',
       'clientCode' => 'required',
       'name' => 'required',
       'gender' => 'required|in:' . implode(',', config('steve.gender')),
       'type' => 'required|in:' . implode(',', config('steve.client_type')),
       'note' => 'required'
     ];
     $this->validate($request, $rules);

     // Insert
     Client::create($params);
     return redirect('/dashboard/client')->with('message', 'Client has been created.');
   }

   public function editClient(Request $request) {
     $params = $request->all();

     // Validation
     $rules = [
       'user_id' => 'required|exists:users,id',
       'clientCode' => 'required',
       'name' => 'required',
       'gender' => 'required|in:' . implode(',', config('steve.gender')),
       'type' => 'required|in:' . implode(',', config('steve.client_type')),
       'note' => 'required'
     ];
     $this->validate($request, $rules);

     // Update field
     $row = Client::find($params['id']);
     $message = 'Client not found!';
     if(!empty($row)) {
       $row->user_id = $params['user_id'];
       $row->clientCode = $params['clientCode'];
       $row->name = $params['name'];
       $row->gender = $params['gender'];
       $row->type = $params['type'];
       $row->note = $params['note'];

       // Save it
       $row->save();

       $message = 'Client has been updated';
     }

     return redirect('/dashboard/client')->with('message', $message);
   }

   public function deleteClient($id) {
     Client::find($id)->delete();

     return redirect('/dashboard/client')->with('message', 'Delete client successful');
   }
}
