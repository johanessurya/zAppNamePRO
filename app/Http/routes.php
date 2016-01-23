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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/demo', function () {
    return view('demo');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/register', function () {
    return view('register');
});
Route::post('/register', 'RegisterController@index');

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/api/v1/user', function (App\User $user) {
  $users = $user->all();

  $json['data'] = array();
  foreach($users as $user) {
    $json['data'][] = array(
      $user->username, $user->email
    );
  }

  return json_encode($json);
});

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
    //
});
