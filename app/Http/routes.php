<?php

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
  Route::get('/test', 'RegisterController@test');
  Route::get('/register', function () {
      return view('register');
  });
  Route::post('/register', 'RegisterController@index');

  // Dashboard
  Route::get('/dashboard', array(
    'middleware' => 'auth',
    'uses' => 'DashboardController@index'
  ));
  Route::get('/dashboard/user', function () {
    return view('dashboard.newuser', array('title' => 'Create New User'));
  });
  Route::get('/dashboard/user/{id}', function () {
    return view('dashboard.newuser', array('title' => 'Edit User'));
  });
  Route::post('/dashboard/user', 'DashboardController@createUser');

  Route::get('/forgot', function () {
    return view('forgot');
  });

  Route::get('/demo', function () {
      return view('demo');
  });

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

  Route::get('/api/v1/user', function (App\User $user) {
    $users = $user->all();

    $json['data'] = array();
    foreach($users as $user) {
      $json['data'][] = array(
        'username' => $user->username,
        'email' => $user->email
      );
    }

    return json_encode($json);
  });
});
