<?php

namespace App\Http\Requests;

use App\Http\Enumerations\CategoryType;
use App\Rules\ProductQty;
use Illuminate\Foundation\Http\FormRequest;

class AdvertismentImagesRequest extends FormRequest
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
           // 'advertise_id' => 'required|exists:advertisements,id',
           // 'document' => 'required|array|min:1',
          //  'document.*' => 'required|string',
        ];
    }

}
