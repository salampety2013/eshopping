<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
       return view('dashboard.index');

    }
 /*public function index_seller()
    {
       return view('dashboard.index_seller');

    }*/
}
