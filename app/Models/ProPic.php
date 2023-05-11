<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


 class ProPic extends Model

{
    use HasFactory;
    //protected $table ="sub_categories";

  protected $guarded = [];

	  public function getImgAttribute($val)
    {

        return $val ? asset('assets/images/advertisments/'.$val) : '';
    }





}
