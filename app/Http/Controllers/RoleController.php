<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\tb_role;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $tb_role = tb_role::orderBy('name', 'asc')->get();
        
        $counts = DB::table('role_users')
            ->select('role_id', DB::raw('count(*) as total'))
            ->groupBy('role_id')
            ->pluck('total', 'role_id');
            
        foreach($tb_role as $role) {
            $role->users_count = $counts[$role->id] ?? 0;
        }

        $menu = 'user_management';
        return view('role_management.index', compact('tb_role', 'menu'));
    }

    public function create()
    {
        $menu = 'user_management';
        return view('role_management.create', compact('menu'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
        ]);

        tb_role::create([
            'name' => $request->name,
        ]);

        return redirect('role-management')->with('success', 'Role created successfully.');
    }

    public function edit($id)
    {
        $role = tb_role::findOrFail($id);
        $menu = 'user_management';
        return view('role_management.edit', compact('role', 'menu'));
    }

    public function update(Request $request, $id)
    {
        $role = tb_role::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$role->id,
        ]);

        $role->name = $request->name;
        $role->save();

        return redirect('role-management')->with('success', 'Role updated successfully.');
    }

    public function destroy($id)
    {
        $role = tb_role::findOrFail($id);
        
        // Remove from mappings first
        DB::table('role_users')->where('role_id', $id)->delete();
        $role->delete();

        return redirect('/role-management')->with('success', 'Role deleted successfully.');
    }
}
