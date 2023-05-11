<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Currency;
 
 use Illuminate\Support\Carbon;
use App\Http\Requests\CurrencyRequest;
use Exception;
use Illuminate\Support\Facades\DB;


class CurrenciesController extends Controller
{
    public function index()
    {
           $currencies = Currency::latest()->get();
         return view('dashboard.currencies.index_currencies', compact('currencies'));
 
    }
    public function create()
    {
        
        return view('dashboard.currencies.create_currencies' );
    }

    public function store(Request $request)
    {

        try {

               DB::beginTransaction();

            //validation

            //dd( $request);
            /* $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'img' => 'required',
        ], [
            'name_en.required' => 'Input Currency English Name',
            'name_ar.required' => 'Input Currency AR Name',
        ]);*/

            ////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////

            $filePath = "";
            if ($request->has('img')) {
                //dd($request->img);
                // $filePath = uploadImage('currencies', $request->img);
                $filePath = uploadImage('assets/images/currencies/', $request->img);
            }
            /////////////////////////////////////////////////////////////////////////////////////

            if (!$request->has('status'))
                $flag = 0;
            else
                $flag = 1;

            Currency::insert([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'code' => $request->code,
                'exchange_rate' => $request->exchange_rate,
                'symbol' => $request->symbol,
                'tax_value' => $request->tax_value,
                'img' => $filePath,
                'status' => $flag,
                'created_at' => Carbon::now()
            ]);

            $notification = array(
                'msg' => 'Currency Added Successfully',
                'alert-type' => ' success'
            );
             DB::commit();
             return redirect()->route('admin.currencies')->with($notification);
          //  return redirect()->route('admin.currencies')->with(['success' => 'تم ألاضافة بنجاح']);
          
        } catch (\Exception $ex) {
            DB::rollback();
            return $ex;
            return redirect()->route('admin.currencies')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





    public function edit($id)
    {
       
        $currency = Currency::find($id);

        if (!$currency)
            return redirect()->route('admin.currencies')->with(['error' => 'هذا القسم غير موجود']);
        // $currencies = Currency::findOrFail($id);
        return view('dashboard.currencies.edit_currencies', compact('currency'));
    }




    public function update($id, Request $request)
    {
        try {
			DB::beginTransaction();

            //return $request->all();
            $currencies = Currency::find($request->id);
            if (!$currencies)
                return redirect()->route('admin.currencies')->with(['error' => 'هذا القسم غير موجود']);
            /////////////upload image/////////////////////
            $old_img = $currencies->img;

            $old_img_path = 'assets/images/currencies/' . $old_img;
            $filePath = "";
            if ($request->has('img')) {
                //dd($request->img);
                // $filePath = uploadImage('currencies', $request->img);
                 if($old_img!=null){
					
						if (file_exists($old_img_path)) {
						unlink($old_img_path);
                		}
				}

                $filePath = uploadImage('assets/images/currencies/', $request->img);
            } else {

                $filePath = $old_img;
            }
            /////////////////////////////////////////////////////////////////////////////////////

            if (!$request->has('status'))
                $flag = 0;
            else
                $flag = 1;
            // return $request->all();
            //return $request->quantity ;

            $currencies->update([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'code' => $request->code,
                'exchange_rate' => $request->exchange_rate,
                'symbol' => $request->symbol,
                'tax_value' => $request->tax_value,
                'img' => $filePath,
                'status' => $flag,
               
                'updated_at' => Carbon::now()
            ]);


            //  $currencies->name_en = $request->name_en;
            // $currencies->save();
            //$currencies->update($request->all());
            //$product = Currency::get();
            // DB::enableQueryLog();
            //$query = DB::getQueryLog();
            //$query = end($query);
            //dd($query);
			DB::commit();


            $notification = array(
                'msg' => 'Currency Updated Successfully',
                'alert-type' => 'info'
            );

           // return redirect()->route('admin.currencies')->with(['success' => 'تم الحفظ بنجاح']);

             return redirect()->route('admin.currencies')->with($notification);

        } catch (\Exception $ex) {
 return  $ex;
			DB::rollback();

            return redirect()->route('admin.currencies')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





    public function deactivate($id)
    {

        try {
            //get specific categories and its translations
            //  $category = Currency::orderBy('id', 'DESC')->find($id);
            $currencies = Currency::find($id);
            if (!$currencies)
                return redirect()->route('admin.currencies')->with(['error' => 'هذا القسم غير موجود ']);

            $currencies->status = 0;
            $currencies->save();

            return redirect()->route('admin.currencies')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.currencies')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }



    public function activate($id)
    {

        try {
            //get specific categories and its translations
            //  $category = Currency::orderBy('id', 'DESC')->find($id);
            $currencies = Currency::find($id);
            if (!$currencies)
                return redirect()->route('admin.currencies')->with(['error' => 'هذا القسم غير موجود ']);

            $currencies->status = 1;
            $currencies->save();
$notification = array(
                'msg' => 'Updated Successfully',
                'alert-type' => 'success'
            );
			
           // return redirect()->route('admin.currencies')->with(['success' => 'تم الحفظ بنجاح']);

             return redirect()->route('admin.currencies')->with($notification)->with(['success' => 'تم الحفظ بنجاح']);

         } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.currencies')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
    public function destroy($id)
    {

        try {
			DB::beginTransaction();

            //get specific categories and its translations
            //  $category = Currency::orderBy('id', 'DESC')->find($id);
            $currencies = Currency::find($id);
			
            if (!$currencies)
                return redirect()->route('admin.currencies')->with(['error' => 'هذا القسم غير موجود ']);



            $old_img = $currencies->img;
            $old_img_path = 'assets/images/currencies/' . $old_img;

            if (file_exists($old_img_path)) {
                unlink($old_img_path);
            }

            $currencies->delete();
			DB::commit();
		

            return redirect()->route('admin.currencies')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
				DB::rollback();
            return redirect()->route('admin.currencies')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function delAll(Request $request)
    {
        try {
            if ($request->ids == "")
                return redirect()->route('admin.currencies')->with(['error' => 'من فضلك قم بالاختيار ليتم الحذف']);

            $ids = $request->ids;
            Currency::whereIn('id', $ids)->delete();

            return redirect()->route('admin.currencies')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            return $ex;
            return redirect()->route('admin.currencies')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
}
