<?php

namespace App\Http\Controllers\Site;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Rating;
use Auth;
 use Illuminate\Support\Carbon;
 use Exception;
use Illuminate\Support\Facades\DB;

 
class RatingsController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function AddRating(Request $request)
	
    {
		 
        if(!Auth::check()){
			
      		 $notification = array(
                'sweet-alert-msg1' => 'Sorry',
				'sweet-alert-msg' => 'You must Login',
                'sweet-alert-type' => 'error'
            );
             
            
		}else{
			$product_id=$request->product_id;
			$user_id=auth()->user()->id;
		 
		 
			
			   $votes=Rating::where(['product_id'=> $product_id,'user_id'=>$user_id])->first();
			  //return $request->all(); 
			  if(empty($request->rating)){ 
			   $notification = array(
							'sweet-alert-msg1' => 'Sorry',
							'sweet-alert-msg' => 'you Must Add One Star At Least',
							'sweet-alert-type' => 'error');
			}elseif( $votes){
			   
				    $notification = array(
							'sweet-alert-msg1' => 'Sorry',
							'sweet-alert-msg' => 'you Add Review before',
							'sweet-alert-type' => 'error');
				   }else{
				   
						 Rating::create([
								'product_id' => $product_id,
								'user_id' =>$user_id,
								'rating_value' => (float)$request->rating,
								'review' => $request->review,
								//'created_at' => Carbon::now(),
                   		 ]); 
					 		$notification = array(
										'sweet-alert-msg1' => 'Thankyou',	 
										'sweet-alert-msg' => 'Rating Added succesfully!',
										'sweet-alert-type' => 'success'
									);
				   }
         
					 
			 }
		   return redirect()->back()->with($notification); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {

        if (! auth()->user()->wishlistHas(request('productId'))) {
            auth()->user()->wishlist()->attach(request('productId'));
            return response() -> json(['status' => true , 'wished' => true]);
        }
        return response() -> json(['status' => true , 'wished' => false]);  // added before we can use enumeration here
    }

    /**
     * Destroy resources by the given id.
     *
     * @param string $productId
     * @return void
     */
    public function destroy()
    {
        auth()->user()->wishlist()->detach(request('product_id'));
    }
}
