<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests;
 use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\User;

use App\Http\Resources\V1\OrderCollection;
use App\Http\Resources\V1\OrderDetailResource;


 use Illuminate\Support\Facades\DB;
 use Illuminate\Support\Carbon;
 use App\Exceptions\QuantityExceededException;
 use App\Traits\GeneralTrait;
 use Exception;
 use Auth;
use View;
class OrdersController extends Controller
{

    use GeneralTrait;

    //##########################
     // Show all my orders
    //##########################
    public function index(Request $request)
    {

      try {
        $per_page=($request->per_page ? $request->per_page : 10 );
        //$user_id=Auth::user()->id
       $user_id=$request->user_id;
       $user= User::where('id',$user_id)->first();
       if (!$user)
       return $this->returnError('202', __('invalid user '));

         $orders= Order::with('orders_products')->where('user_id',$user_id)->orderBy('id','Desc')->paginate(2);




        return $this->returnData('data', new OrderCollection($orders));
        // return $this->returnData('data',new OrderResource($product));

      } catch (\Exception $e) {
        return $this->returnError(201, $e->getMessage());
      }
    }


     //##########################
     // get orders details.
     //##########################


       public function viewDetails(Request $request)
    {


try {

    $id=$request->id;

       $order= Order::where('id',$id)->first();
       if (!$order)
       return $this->returnError('202',__('general.not found'));
$order_details= Order::with('orders_products')->where('id',$id)->orderBy('id','Desc')->first();





        return $this->returnData('data', new OrderDetailResource($order_details));

      } catch (\Exception $e) {
        return $this->returnError(201, $e->getMessage());
      }


    }









}
