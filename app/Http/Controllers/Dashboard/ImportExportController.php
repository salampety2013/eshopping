<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
 
use App\Models\Product;
 
use Illuminate\Support\Carbon;
 use Exception;
use Illuminate\Support\Facades\DB;


class ImportExportController  extends Controller
{
     
    public function importFiles()
    {
        
        return view('dashboard.CSV.import_files' );
    } 
//----------------------------------------------------------------------


 public function importFilesCatSub()
    {
       $categories = Category::where('is_active',"1")->orderBy('name_ar', 'ASC')->get();
       $brands = Brand::where('is_active',"1")->orderBy('name_ar', 'ASC')->get();

        return view('dashboard.CSV.import_files_choose_cat', compact('categories','brands'));

         
    }
	
//--------------------------------------------------------------------

//////////////////////////////////////////////////////////////////////
 public function GetSubCategory($cat_id){
			 
			  $subcat = SubCategory::where('cat_id',$cat_id)->orderBy('name_en','ASC')->get();
        return json_encode($subcat);
     } 
	
	//////////////////////////////////////////////////////////




    public function importExcelCsv(Request $request)
    {

        try {

               DB::beginTransaction();

            //validation

            //dd( $request);
            /* $request->validate([
             
            'file' => 'required|mimes:csv,xlsx',
        ], [
            'file.required' => 'File required      ',
            'file.mimes' => 'type must be csv,xlsx',
        ]);*/

            ////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////
 //dd($request->upload_file);
             
             

            if (!empty($request->files) && $request->hasFile('upload_file')) {
                $file = $request->file('upload_file');
                $type_ext = $file->getClientOriginalExtension();
				
                $real_path = $file->getRealPath();
                /*if ($type_ext <> 'csv') {
					 $notification = array(
                'msg' => 'Wrong file extension', 'Only CSV is allowed',
                'alert-type' => 'error'
            );
  				return redirect()->route('admin.import.files')->with($notification);

				    //return redirect()->back();
                }*/
				
				
				/*$fileName = uploadImage('assets/images/csv/', $request->upload_file);
				$filePath = realpath('assets/images/csv/'.$fileName); 
			 	 $data = $this->readCSV($filePath) ;*/
				  $data = $this->readCSV($real_path) ;
 				// $data = $this->readCSV($real_path, array('delimiter' => ','));
				// dd( $data);//print csv file 
				$latest_data=array();
			   foreach($data as $key => $row ){
				  // $latest_data[$key]['id']=$row['id'];
				   $latest_data[$key]['name_ar']=$row['name_ar'];
				   $latest_data[$key]['name_en']=$row['name_en'];
				   $latest_data[$key]['cat_id']=$row['cat_id'];
				   $latest_data[$key]['sub_cat_id']=$row['sub_cat_id'];
				   
				  $latest_data[$key]['slug_ar']=$row['slug_ar'];
				   $latest_data[$key]['slug_en']=$row['slug_en'];

				    
 				   
				   $latest_data[$key]['code']=$row['code'];
				   $latest_data[$key]['price']=$row['price'];
				   $latest_data[$key]['discount_price']=$row['discount_price'];
				   $latest_data[$key]['details_ar']=$row['details_ar'];
				   $latest_data[$key]['details_en']=$row['details_en'];
				   $latest_data[$key]['new_trends']=$row['new_trends'];
				   $latest_data[$key]['new_arrival']=$row['new_arrival'];
				   $latest_data[$key]['flash_sale']=$row['flash_sale'];
				   $latest_data[$key]['img']=$row['img'];
				   $latest_data[$key]['is_active']=$row['is_active'];
				   $latest_data[$key]['created_at']=Carbon::now();
 				   $latest_data[$key]['updated_at']=Carbon::now();
				   
				   }
				 //return $latest_data;
				    //DB::table('products')->delete();
			     // DB::update('ALTER TABLE `products` AUTO_INCREMENT=1');  // make id begin from 1 equal to truncate
  				  	 DB::table('products')->truncate();
					DB::table('products')->insert($latest_data);
            }
             $notification = array(
                'msg' => 'Data Added Successfully',
                'alert-type' => 'success'
            );
             DB::commit();
             return redirect()->route('admin.import.files')->with($notification);
           
        } catch (\Exception $ex) {
            DB::rollback();
            return $ex;
            return redirect()->route('admin.import.files')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

//---------------------------------------------
//--------------------------------------------------------------------
    public function importExcelCsvProducts(Request $request)
    {

        try {

               DB::beginTransaction();

            //validation

            //dd( $request);
            /* $request->validate([
             
            'file' => 'required|mimes:csv,xlsx',
        ], [
            'file.required' => 'File required      ',
            'file.mimes' => 'type must be csv,xlsx',
        ]);*/

            ////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////
  //dd($request->upload_file);
             if (!empty($request->files) &&($request->cat_id) &&($request->subcategory_id) && $request->hasFile('upload_file')) {
                $file = $request->file('upload_file');
                $type_ext = $file->getClientOriginalExtension();
				
                $real_path = $file->getRealPath();
				$cat_id  =  $request->cat_id;
				 $sub_cat_id  =  $request->subcategory_id;
                /*if ($type_ext <> 'csv') {
					 $notification = array(
                'msg' => 'Wrong file extension', 'Only CSV is allowed',
                'alert-type' => 'error'
            );
  				return redirect()->route('admin.import.files')->with($notification);

				    //return redirect()->back();
                }*/
				
				
				/*$fileName = uploadImage('assets/images/csv/', $request->upload_file);
				$filePath = realpath('assets/images/csv/'.$fileName); 
               // $data = $this->readCSV($real_path, array('delimiter' => ','));
			    $data = $this->readCSV($filePath) ;*/
			 	 $data = $this->readCSV($real_path);
				// dd( $data);//print csv file 
				$latest_data=array();
			   foreach($data as $key => $row ){
				  // $latest_data[$key]['id']=$row['id'];
				   $latest_data[$key]['name_ar']=$row['name_ar'];
				   $latest_data[$key]['name_en']=$row['name_en'];
				   $latest_data[$key]['cat_id']=$cat_id;
				   $latest_data[$key]['sub_cat_id']=$sub_cat_id;
				   
				    
				    //$latest_data[$key]['slug_ar']=strtolower(str_replace(' ', '-', $row['name_ar']));
				    //$latest_data[$key]['slug_en']=strtolower(str_replace(' ', '-', $row['name_en']));
				   
				   $latest_data[$key]['code']=$row['code'];
				   $latest_data[$key]['price']=$row['price'];
				   $latest_data[$key]['discount_price']=$row['discount_price'];
				   $latest_data[$key]['details_ar']=$row['details_ar'];
				   $latest_data[$key]['details_en']=$row['details_en'];
				   $latest_data[$key]['new_trends']=$row['new_trends'];
				   $latest_data[$key]['new_arrival']=$row['new_arrival'];
				   $latest_data[$key]['flash_sale']=$row['flash_sale'];
				   $latest_data[$key]['img']=$row['img'];
				   $latest_data[$key]['is_active']=$row['is_active'];
				   $latest_data[$key]['created_at']=Carbon::now();
 				   $latest_data[$key]['updated_at']=Carbon::now();
				   
				   }
				  //return $latest_data;
				    //DB::table('products')->delete();
			     // DB::update('ALTER TABLE `products` AUTO_INCREMENT=1');
  				  	 DB::table('products')->truncate();
					DB::table('products')->insert($latest_data);
            }
             $notification = array(
                'msg' => 'Data Added Successfully',
                'alert-type' => 'success'
            );
             DB::commit();
             return redirect()->route('admin.import.cat.files')->with($notification);
           
        } catch (\Exception $ex) {
            DB::rollback();
            return $ex;
            return redirect()->route('admin.import.cat.files')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

//---------------------------------------------
 // import using fgets function
 //---------------------------------------------------
 
  

 
 
 
public function readCSV($filename = '', $delimiter = ',') {
		if (!file_exists($filename) || !is_readable($filename))
			return FALSE;

		$header = NULL;
		$data = array();
		if (($handle = fopen($filename, 'r')) !== FALSE) {
			while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
				if (!$header)
					$header = $row;
				else
					$data[] = array_combine($header, $row);
			}
			fclose($handle);
		}
		return $data;
	}
	
	public function update_csv() {

		$csvFile = realpath() . '/[Model]-seeder.csv';
		$data = $this->csv_to_array($csvFile);
		
		DB::table('[Model]')->truncate();
		DB::table('[Model]')->insert($data);
		// or
		//foreach($data as $entry)
		//{		
 		//	[Model]::create($data);
 		//}
	}



}
