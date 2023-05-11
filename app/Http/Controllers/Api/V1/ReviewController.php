<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\RatingRequest;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Validator;
use App\Http\Resources\V1\ReviewsResource;
 use App\Models\Product;
use App\Models\Rating;
use App\Models\User;
use App\Models\OrdersProduct;
use App\Models\Order;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;
use Auth;





class ReviewController extends Controller
{
    use GeneralTrait;

    public function index(Request $request)
    {
        try {

            $product_id=$request->product_id;
            $rules = [
                'product_id' => 'required|numeric|exists:Products,id',


            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
                // return $this->returnValidationErrorAll('422',$validator);

            }

            $reviews=Rating::where('product_id', $product_id) ->orderBy('created_at', 'desc')->where('status', 1)->paginate(10) ;
            // $reviews = Rating::where('status', 1)->select('id', 'user_id', 'rating_value', 'review', 'created_at')->where('product_id', $product_id)->orderBy('created_at', 'Asc')->get();
            $reviews = Rating::with(['user' => function ($q) {
                $q->select('id', 'name', 'img')->where('status', 1);
            }])->where('status', 1)->select('id', 'user_id', 'rating_value', 'review', 'created_at')->where('product_id', $product_id)->orderBy('created_at', 'Asc')->get();

            $ratings_average = collect($reviews)->average("rating_value");
            $data = [

                 'ratings_average'=>$ratings_average,
                 'reviews'=>ReviewsResource::collection($reviews),

                 ];

            return $this->returnData('data',$data );



          } catch (\Exception $e) {
            return $this->returnError(201, $e->getMessage());
          }



    }



    public function addRating(Request $request)
    {
            //---------------------------------------------
  // the user cant make review and rate if is not login or not buy that product and deliverd it or he commented before
        try {



            $rules = [
                'product_id' => 'required|numeric|exists:Products,id',
                'user_id' => 'required|numeric|exists:users,id',
                'rating_value' => 'required',
                'review' => 'required'

            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
                // return $this->returnValidationErrorAll('422',$validator);

            }

            $product_id = $request->product_id;
            $user_id = $request->user_id;
            $rating_value = $request->rating_value;
            $review = $request->review;

            $product = Product::where('id', $request->product_id)->where('is_active', 1)->first();
            $user = User::where('id', $request->user_id)->where('status', 1)->first();

             $orderDetails=OrdersProduct::where('product_id', $request->product_id)->where('user_id', $request-> user_id)->get();

              $reviewable = false;
               //	$orderDetailsa=OrdersProduct::where('product_id', $request->product_id)->get('order_id');
           // $product->orderDetails;
            $order_ids = array();
            foreach ($orderDetails as $key => $orderDetail) {
                // $order_ids=  $order_ids.",".$orderDetail->order_id ;
                $order_ids[] = $orderDetail->order_id;
            }
           //return  $order_ids;
            //print_r( $order_ids);
               $oders_delivered = Order::whereIn('id', $order_ids)->where('order_status', 'Delivered')->count();
               //return  $oders_delivered;
            $review_before = Rating::where('user_id', $request->user_id)->where('product_id', $product->id)->first();

             //return $order_ids;
             //return $review_before;


             if ($review_before != null) {
                return $this->returnError('E001', 'You review this product before ');

             }
                if (  $oders_delivered ==0) {
                    return $this->returnError('E001', 'You cannot review this product');

            }
            //return $reviewable;
            /* foreach ($product->orderDetails as $key => $orderDetail) {
            if($orderDetail->order != null && $orderDetail->order->user_id == $request->user_id && $orderDetail->delivery_status == 'delivered' && \App\Review::where('user_id', $request->user_id)->where('product_id', $product->id)->first() == null){
                $reviewable = true;
            }
        }
*/


        Rating::create([
            'product_id' => $product_id,
            'user_id' =>$user_id,
            'rating_value' => (float)$rating_value,
            'review' => $review,
            //'created_at' => Carbon::now(),
        ]);
       /*  $count = Rating::where('product_id', $product->id)->count();

         if($count > 0){
            $product->rating = Rating::where('product_id', $product->id)->sum('rating')/$count;
        }
        else {
            $product->rating = 0;
        }
        $product->save();
 */

return $this->returnSuccessMessage('rating added successfully');





             //  return $this->returnData('data',  ProductResource::collection($related_products));
        } catch (\Exception $e) {
            return $this->returnError(201, $e->getMessage());
        }
    }
}
