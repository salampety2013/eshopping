<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
  
class StudentController  extends Controller

{
    /**
     * Display a listing of the myformPost.
     *
     * @return \Illuminate\Http\Response
     */
    public function ajaxRequest()
    {
        return view('student_ajax');
    }
 public function studentAjax(Request $request)
    {
		
         $input = $request->all();
          
       // Log::info($input);
     
        return response()->json(['success'=>$input]);
    }

    /**
     * Display a listing of the myformPost.
     *
     * @return \Illuminate\Http\Response
     */
    public function ajaxsStudentStore(Request $request)
    {
		//return "test";
		//dd("hhhhhhhhh");
       /* $validator = Validator::make($request->all(), [
            'password' => 'required',
            'email' => 'required|email',
            'address' => 'required',
        ]);

        if ($validator->passes()) {*/

            // Store Data in DATABASE from HERE 
			  $input = $request->all();
return response()->json(['success'=>$input]);
            //return response()->json(['success'=>'Added new records.']);
            
       // }

       // return response()->json(['error'=>$validator->errors()]);
    }
}
