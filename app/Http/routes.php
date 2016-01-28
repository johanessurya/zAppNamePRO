<?php
define('DATETIME_FORMAT','m/d/y H:i');
define('DATE_FORMAT', 'm/d/y');

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {

  // Register
  Route::get('/', function () {
    return redirect('/login');
  });
  Route::get('/test', 'RegisterController@test2');
  Route::get('/register', function () {
      return view('register');
  });
  Route::post('/register', 'RegisterController@index');

  // ==== Dashboard ====
  Route::get('/dashboard', array(
    'middleware' => 'auth',
    'uses' => 'DashboardController@index'
  ));

  // User Management(START)
  // Create user form
  Route::get('/dashboard/user', function () {
    $today = time();
    $expires = time()+(15*24*60*60);

    $data = array(
      'title' => 'Create New User',
      'created' => date("Y-m-d H:i:s", $today),
      'expires' => date("Y-m-d H:i:s", $expires),
    );
    return view('dashboard.newuser', $data);
  });
  Route::post('/dashboard/user', 'DashboardController@createUser');

  // Edit user form
  Route::get('/dashboard/user/{id}', 'DashboardController@editUser');
  Route::post('/dashboard/user/edit/do', 'DashboardController@doEditUser');

  // Delete user
  Route::get('/dashboard/user/delete/{id}', 'DashboardController@deleteUser');
  // Set user to active = 1 and expires = +15
  Route::get('/dashboard/user/active/{id}', 'DashboardController@setActiveUser');

  // User Management(START)

  // Client Management(START)
  // List table
  Route::get('/dashboard/client', function () {
    return view('dashboard.listclient');
  });

  // Create form
  Route::get('/dashboard/client/create', function() {
    $today = time();
    $expires = time()+(15*24*60*60);

    $data = array(
      'gender' => config('steve.gender'),
      'type' => config('steve.client_type'),
      'created' => date("Y-m-d H:i:s", $today),
      'expires' => date("Y-m-d H:i:s", $expires)
    );
    return view('dashboard.newclient', $data);
  });
  Route::post('/dashboard/client/create', 'DashboardController@createClient');

  // Edit form
  Route::get('/dashboard/client/edit/{id}', function (App\Client $client, $id) {
    $rows = $client->find($id);
    $data = array(
      'client' => $rows
    );

    return view('dashboard.editclient', $data);
  });
  Route::post('/dashboard/client/edit', 'DashboardController@editClient');

  // Delete
  Route::get('/dashboard/client/delete/{id}', 'DashboardController@deleteClient');
  // Client Management(END)

  // Forgot password
  Route::get('/forgot', function () {
    return view('forgot');
  });

  Route::get('/demo', function () {
      return view('demo');
  });

  // Configuration setting
  Route::post('/dashboard/settings', 'DashboardController@settings');
  Route::post('/dashboard/layoutsettings', 'DashboardController@layoutSettings');

  // Login
  Route::get('/login', function () {
    if(Auth::check()) {
      return redirect('/dashboard');
    }
    return view('login');
  });
  Route::post('/login', 'LoginController@index');
  Route::get('/logout', 'LoginController@logout');

  // Forgot password
  Route::post('/resetpassword', 'LoginController@resetPassword');
  Route::get('/reset/{token}', 'LoginController@reset');
  Route::post('/reset', 'LoginController@reset');

  Route::get('/activation', function () {
    return view('activation');
  });

  // Calendar
  Route::get('/dashboard/calendar', 'CalendarController@index');

  // ==== API Route ====
  // Prefix : /api/v1/
  Route::group(['prefix' => '/api/v1/'], function () {
    // Get user list
    Route::get('user', function (App\User $user) {
      // Matches The "/api/v1/users" URL
      $users = $user->all();

      $json['data'] = array();
      foreach($users as $user) {
        $json['data'][] = array(
          'id' => $user->id,
          'username' => $user->username,
          'active' => $user->active,
          'userType' => $user->userType,
          'email' => $user->email,
          'CompanyID' => $user->CompanyID,
          'created' => $user->created,
          'active' => $user->active,
          'firstLogin' => $user->firstLogin,
          'lastLogin' => $user->lastLogin,
          'loginCount' => $user->loginCount,
          'expires' => $user->expires
        );
      }

      return json_encode($json);
    });

    // Get client list
    Route::get('client', function (App\Client $client) {
      $rows = $client->where('user_id', Auth::user()->id)->get();

      $json['data'] = array();
      foreach($rows as $x) {
        $json['data'][] = array(
          'id' => $x->id,
          'user_id' => $x->user_id,
          'clientCode' => $x->clientCode,
          'name' => $x->name,
          'gender' => $x->gender,
          'type' => $x->type,
          'note' => $x->note
        );
      }

      return json_encode($json);
    });

    // Get event list
    Route::get('/calendar/event', 'CalendarController@apiEventList');

    // Save an event
    Route::post('/calendar/save', 'CalendarController@save');
  });
});
