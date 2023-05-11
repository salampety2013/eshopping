<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 use App\Models\User;
 use Auth;
 class Sms extends Model

{
    use HasFactory;
     //protected $table ="sub_categories";
    protected $guarded = [];
   
   // protected $fillable = ['name','user_id', 'pincode',]; 
	
	 
    public static function  sendSms($message,$mobile)
{
 $user_id=Auth::user()->id;
 
    /*Code for SMS Script Starts*/
$request ="";
$param['authorization']="0fghGt7O6rJ1C8fsddpUXSEPLWv2aDRuMkyeif7mKBwNHxd4vw0gKcTfrhemqdsFS8gb6Do59Nzp1Ry5fi";
$param['sender_id'] = 'FSTSMS';
//$param['message']= 'This is the test SMS from Stack Developers Youtube Channel';
//$param['numbers']= '9800000000';
$param['message']= $message;
$param['numbers']= $mobile;
$param['language']="english";
$param['route']="p";

foreach($param as $key=>$val) {
    $request.= $key."=".urlencode($val);
    $request.= "&";
}
$request = substr($request, 0, strlen($request)-1);

$url ="https://www.fast2sms.com/dev/bulk?".$request;
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$curl_scraped_page = curl_exec($ch);
curl_close($ch);
/*Code for SMS Script Ends*/
	 
		 
		 
		 
	return $deliveryAddress;
     	
}
 
         
 
}
