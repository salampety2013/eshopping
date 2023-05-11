<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Requests\AdminRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;
use App\Models\Role;
use  Auth;
use Exception;


class ManagersController extends Controller
{
         public function index() {
         $managers = Admin::latest()->where('id', '<>', auth()->id())->get(); //use pagination here
        return view('dashboard.managers.index', compact('managers'));
    }

    public function create(){
        $roles = Role::get();
        return view('dashboard.managers.create',compact('roles'));
    }


    public function store(AdminRequest $request) {
        $user = new Admin();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);   // the best place on model
        $user->role_id = $request->role_id;

        // save the new user data
        if($user->save())
             return redirect()->route('admin.managers.index')->with(['success' => 'تم الحفظ بنجاح']);
        else
            return redirect()->route('admin.managers.index')->with(['success' => 'حدث خطا ما']);

    }

}
