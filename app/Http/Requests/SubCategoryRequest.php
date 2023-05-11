<?php

namespace App\Http\Requests;

use App\Http\Enumerations\CategoryType;
use Illuminate\Foundation\Http\FormRequest;

class SubnCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
             'name_ar' => 'required',
             'name_en' => 'required',
             'cat_id' => 'required|nullable |int|exists:categories,id',
            // 'type' => 'required|in:1,2',
            'img' => 'required_without:id|mimes:png,jpg,gif',  
            // 'slug' => 'required|unique:categories,slug,'.$this -> id
        ];
    }

}


