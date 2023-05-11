<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Support\Carbon;
use App\Http\Requests\SubCategoryRequest;
use Exception;
use Illuminate\Support\Facades\DB;


 class RealTimeDatabaseController extends Controller
{
	
	
     public function index()
    {
		
         return view('dashboard.real_time_firebase.index');
    }
	
	
	//-----------------------------------------------------------------------------
	//    get    data in firebase- database  with curle
	//------------------------------------------------------------------------------
   public function indexCURL()
    {
		 $headers = array();
    	$headers[] = "Key: Value";
		 $url = "https://laravel-ecommerce-62f68-default-rtdb.firebaseio.com/price_mazadat.json";
     $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, $url);
       
        //curl_setopt($ch, CURLOPT_POST, true);
		 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    	// curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
        

        // Execute post
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }        

        // Close connection
        curl_close($ch);

        // FCM response
          
	// Show result
	echo json_encode($result);
		
         return view('dashboard.real_time_firebase.index_CURL');
    }
  	//-----------------------------------------------------------------------------
	//      write data in firebase- database  with curle
	//------------------------------------------------------------------------------

      public function writeCURl()
	  
    {
       
  for ($i = 1; $i <= 4; $i++) {
//    echo $i;
 $mazad_name   =  "mazad_".$i  ;  

$mazad_name_v   =  "reda".$i ;  

 // $data = '{"id": '.$i.' , "name": '.$mazad_name_v.' , "email": "sssss@yahoo.com"}';
 //$data = '{"Peter":"35", "Ben":'.i.', "Joe":"43"}';

	$data = array
			(
				'id'		=> $i,
				'mazad_name'		=> $mazad_name,
				'mazad_name_v'	=> $mazad_name_v 
				 

			);

    $url = "https://laravel-ecommerce-62f68-default-rtdb.firebaseio.com/price_mazadat/".$mazad_name.".json";
     $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
       // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $data ));

        // Execute post
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }        

        // Close connection
        curl_close($ch);

        // FCM response
      //  dd($result);        
	// Show result
	}
	 dd($result) ;

}
	
	
//-----------------------------------------------------------------------------
	//      delete  data in firebase- database
	//------------------------------------------------------------------------------	
	public function deleteCURl()
	  
    {
       
   
 //$url = "https://stars-231617.firebaseio.com/users/jack/name.json";
	
//$url = "https://stars-231617.firebaseio.com/users/jack/name/".$id_code.".json";

//$url = "https://stars-231617.firebaseio.com/users.json";

	 

    $url = "https://laravel-ecommerce-62f68-default-rtdb.firebaseio.com/price_mazadat.json";
     $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, $url);
       // curl_setopt($ch, CURLOPT_POST, true);
		 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
       // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
        

        // Execute post
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }        

        // Close connection
        curl_close($ch);

        // FCM response
          
	// Show result
//	echo json_encode($result);
//die("");
 
	// dd($result) ;

}
	
	
	
	
	
	
	
	
}