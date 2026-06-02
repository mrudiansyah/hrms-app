<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\tb_user;
use App\Models\tb_roleuser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $tb_user = tb_user::orderBy('name', 'asc')->get();
        $menu = 'user_management';
        return view('user_management.index', compact('tb_user', 'menu'));
    }

    public function create()
    {
        $menu = 'user_management';
        return view('user_management.create', compact('menu'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        $user = tb_user::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('sai 2021'),
            'email_verified_at' => now(),
        ]);

        tb_roleuser::create([
            'user_id' => $user->id,
            'role_id' => 61,
        ]);

        return redirect('user-management')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = tb_user::findOrFail($id);
        $menu = 'user_management';
        return view('user_management.edit', compact('user', 'menu'));
    }

    public function update(Request $request, $id)
    {
        $user = tb_user::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if($request->filled('password')){
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect('user-management')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = tb_user::findOrFail($id);
        
        if ($user->email_verified_at) {
            $user->email_verified_at = null;
            $statusStr = 'unverified / deactivated';
        } else {
            $user->email_verified_at = now();
            $statusStr = 'verified / activated';
        }
        
        $user->save();

        return redirect('user-management')->with('success', "User successfully $statusStr.");
    }
}
