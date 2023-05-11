<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 
class AjaxController  extends Controller
{
     public function ajaxRequest()
    {
        return view('ajaxRequest');
    }
     
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function ajaxRequestPost(Request $request)
    {
		
         $input = $request->all();
          
        // Log::info($input);
     
        return response()->json(['success'=>'Got Simple Ajax Request.']);
    }
}
