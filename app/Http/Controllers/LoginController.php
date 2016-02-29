<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DateTime;

use Mail;
use Hash;
use Auth;
use Session;
use DB;

use App\Category;
use App\SubCategory;
use App\Config;
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
      $user = array(
        'username' => $params['username'],
        'password' => $params['password']
      );
      if(Auth::attempt($user)) {
        User::updateLogin(Auth::user()->id);

        $row = Auth::user();
        $expires = DateTime::createFromFormat(DATE_FORMAT, $row->expires);
        $now = new DateTime();

        // If expired then logout
        if($now > $expires){
          Auth::logout();

          return redirect('/login')->with('message', 'Your account reach expired date');
        } else {
          // Storing session
          $categoryTree = $this->getCategoryTree();
          Session::put('category', $categoryTree);
          Session::put('subcategory', $this->getSubCategory());

          return redirect()->intended('/dashboard');
        }
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

        $return = redirect('/login')->with('message', "Reset password link has been sent to your email. Please don't forget to look in your Spam folder.");
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
          $user->resetToken = null;
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
      // Reset all
      $this->resetSetting();

      Auth::logout();

      return redirect('/login')->with('message', 'Sign out successful');
    }

    // Reset all setting that doesn't need
    private function resetSetting() {
      $config = Config::where('key_name', 'daterange')->first();
      $config->value = null;
      $config->save();
    }

    private function getCategoryTree() {
      $return = [
        // Get all categories
        'category' => Category::getByUserId(Auth::user()->id)->get()->toArray()
      ];

      // Get sub category
      if(!empty($return['category'])) {
        for($i = 0; $i < count($return['category']); $i++) {
          $x = $return['category'][$i];
          $subCategory = DB::table('subcategory')->where('category_id', $x['id'])->get();
          $subCategory = json_decode(json_encode($subCategory), true);

          if(!empty($subCategory)) {
            $return['category'][$i]['subcategory'] = $subCategory;

            for($j = 0; $j < count($return['category'][$i]['subcategory']); $j++) {
              $x = $return['category'][$i]['subcategory'][$j];

              $subsubCategory = DB::table('subsubcategory')->where('category_id', $x['id'])->get();
              $subsubCategory = json_decode(json_encode($subsubCategory), true);

              if(!empty($subsubCategory)) {
                $return['category'][$i]['subcategory'][$j]['subsubcategory'] = $subsubCategory;
              }
            }
          }
        }
      }

      return $return;
    }

    private function getSubCategory() {
      $return = [];

      // Generate all category
      $rows = Category::all();
      foreach($rows as $x) {
        $title = $x->abbrev;

        $return[] = [
          'id' => $x->id,
          'title' => $title,
          'value' => 'c=' . $x->id
        ];
      }

      // Generate all subcategory
      $rows = SubCategory::all();
      foreach($rows as $x) {
        $category = Category::find($x->category_id);

        $title = $category->abbrev . ' | ' . $x->title;

        $return[] = [
          'id' => $x->id,
          'title' => $title,
          'value' => 'sc=' . $x->id
        ];
      }

      return $return;
    }
}
