<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 use Illuminate\Support\Carbon;
 use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Todo;


class AddRemoveFieldController extends Controller
{
    public function index() 
    {
        return view("dashboard.sizes.add-remove-input-fields");
    }
    public function store(Request $request)
    {
        $request->validate([
            'moreFields.*.title' => 'required',
            'moreFields.*.description' => 'required',
        ]);
     
        foreach ($request->moreFields as $key => $value) {
            Todo::create($value);
        }
     
        return back()->with('success', 'Todos Has Been Created Successfully.');
    }
}
