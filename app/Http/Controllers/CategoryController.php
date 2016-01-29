<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Auth;
use Session;
use Response;
use App\Category;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function getTree() {
        $category = Session::get('category');

        return Response::json($category);
    }

    /*
    Get category list
    - Show category with no company ID
    - Show category associated with company id that associated with user id
    */
    public function getSubCategory($categoryId) {
      $rows = DB::table('subcategory')->where('category_id', $categoryId)->get();

      return $rows;
    }

    public function getSubSubCategory($categoryId) {
      $rows = DB::table('subsubcategory')->where('category_id', $categoryId)->get();

      return $rows;
    }
}
