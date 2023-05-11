<?php

namespace App\Http\Controllers\Site;

use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Models\Product;
 

class CategoryController extends Controller
{
   /* public function productsBySlug($slug)
    { 
        $data = [];
		
           $data['category'] = Category::where('slug_ar', $slug)->first();

       // if ($data['category'])
		
             $data['products'] = $data['category']->products;
			// return  Product::find(10)->images;
			//return  Product::find(10)->images[0];
			//return  Product::find(10)->images[0]->img;

        return view('front.products.products', $data);
    }
*/
}
