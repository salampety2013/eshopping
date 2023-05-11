<?php

namespace App\Http\Controllers\Site;

use App\Http\Requests;
use App\Basket\Basket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
 use App\Models\Page;

use Illuminate\Support\Facades\Session;
//use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
 use Illuminate\Support\Carbon;
 use App\Exceptions\QuantityExceededException;

 use Exception;
// use Session;
use Auth;

class PagesController extends Controller
{

		//////////////////////////////delete//////////////////////////////////////////
		///////////////////////////////////////////////////////////////////////
		public function getPages($id)
    {


              $page =  Page::where('id',$id)->where('status',1)->first();
           /* if (!$page)
                return redirect()->route('admin.colors')->with(['error' => 'هذا القسم غير موجود ']);
*/


            return view('front.pages.index',compact('page'));



    }





}
