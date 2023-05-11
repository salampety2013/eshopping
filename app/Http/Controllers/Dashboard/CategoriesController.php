<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Carbon;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();

        //$categories=Category::orderBy('name_en','Desc')->get();
        //return $categories;
        return view('dashboard.categories.index_cat', compact('categories'));
        //return view('backend.category.view',compact('category'));

    }

    public function store(Request $request)
    {

        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'img' => 'required',
        ], [
            'name_en.required' => 'Input Category English Name',
            'name_ar.required' => 'Input Category AR Name',
        ]);
        $img =  $request->file('img');
        $name_gen = hexdec(uniqid());
        $img_ext = strtolower($img->getClientOriginalExtension());
        $img_name = $name_gen . '.' . $img_ext;
        $up_location = 'images/category/';

        // $name_gen = hexdec(uniqid()).'.'.$img->getClientOriginalExtension();

        $img->move($up_location, $img_name);
        $last_img = $up_location . $img_name;
        
 date_default_timezone_set('Africa/Cairo');
        Category::insert([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,

            'slug_en' => strtolower(str_replace(' ', '-', $request->name_en)),
            'slug_ar' => str_replace(' ', '-', $request->name_ar),
            'img' => $last_img,
            'created_at' => Carbon::now()
        ]);
        
    }
}
