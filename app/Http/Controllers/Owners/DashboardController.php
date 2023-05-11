<?php

namespace App\Http\Controllers\Owners;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
       return view('dashboard.index');

    }
 public function index_owner()
    {
       return view('owners.index_owner');

    }
}
