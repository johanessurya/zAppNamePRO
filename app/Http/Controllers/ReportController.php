<?php

namespace App\Http\Controllers;

use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use DateTime;
use Mail;
use App\User;
use App\Company;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    public function index(Request $request) {
      $params = $request->all();
    }
}
