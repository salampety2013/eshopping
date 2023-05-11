<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Enumerations\CategoryType;
use App\Http\Requests\MainCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainCategoriesController extends Controller
{

    public function index()
    {
        $categories = Category::with('_parent')->orderBy('id','DESC') -> paginate(PAGINATION_COUNT);
        return view('dashboard.categories.index', compact('categories'));
    }

    public function create()
    {
         $categories =   Category::select('id','parent_id')->get();
        return view('dashboard.categories.create',compact('categories'));
    }

    public function store(MainCategoryRequest $request)
    {


        try {

         //   DB::beginTransaction();
          
            //validation

            if (!$request->has('is_active'))
                $request->request->add(['is_active' => 0]);
            else
                $request->request->add(['is_active' => 1]);

                /////////////upload image/////////////////////
                 
                $filePath = "";
                if ($request->has('photo')) {
    
                     $filePath = uploadImage('maincategories', $request->photo);
                }
    

            //if user choose main category then we must remove paret id from the request
            if($request -> type == CategoryType::mainCategory) //main category
            {
                 $request->request->add(['parent_id' => null]);
               
            }
          /*  if($request -> type == CategoryType::mainCategory) //main category
            {
               // $request->request->add(['parent_id' => null]);
               $parent_id = null;
            }else{
                $parent_id = $request->parent_id;

            }
*/
            
         //  dd($filePath); 
 // return  $request;
            //if he choose child category we mus t add parent id


             $category = Category::create($request->except('_token'));
          /*  $category = Category::create([
               'name' => $request->name ,
               'slug' => $request->slug ,
               'parent_id' => $parent_id ,
               'is_active' => $request->is_active ,
               'type' => 1 ,
               'photo' =>  $filePath ,

               
               
   
               ] );
              */  
             // $category->photo='maincategories/'.$filePath;
              $category->photo=$filePath;
              $category->save();
           
            //save translations
            $category->name = $request->name;
            $category->save();

            return redirect()->route('admin.maincategories')->with(['success' => 'تم ألاضافة بنجاح']);
          //  DB::commit();

        } catch (\Exception $ex) {
           // DB::rollback();
            return $ex;
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }

    }


    public function edit($id)
    {

        //get specific categories and its translations
        $category = Category::orderBy('id', 'DESC')->find($id);

        if (!$category)
            return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود ']);

        return view('dashboard.categories.edit', compact('category'));

    }


    public function update($id, MainCategoryRequest $request)
    {
        try {
            //validation

            //update DB


            $category = Category::find($id);

            if (!$category)
                return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود']);

            if (!$request->has('is_active'))
                $request->request->add(['is_active' => 0]);
            else
                $request->request->add(['is_active' => 1]);

            $category->update($request->all());

            //save translations
            $category->name = $request->name;
            $category->save();

            return redirect()->route('admin.maincategories')->with(['success' => 'تم التحديث بنجاح']);
        } catch (\Exception $ex) {

            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }

    }


    public function destroy($id)
    {

        try {
            //get specific categories and its translations
            $category = Category::orderBy('id', 'DESC')->find($id);

            if (!$category)
                return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود ']);

            $category->delete();

            return redirect()->route('admin.maincategories')->with(['success' => 'تم  الحذف بنجاح']);

        } catch (\Exception $ex) {
           return $ex;
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

}
