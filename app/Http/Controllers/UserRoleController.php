<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\tb_user;
use App\Models\tb_role;
use App\Models\tb_roleuser;
use Illuminate\Support\Facades\DB;

class UserRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $tb_user = tb_user::orderBy('name', 'asc')->get();
        $tb_role = tb_role::orderBy('name', 'asc')->get();
        $menu = 'user_management';
        return view('userrole_management.index', compact('tb_user', 'tb_role', 'menu'));
    }

    public function selectUser(Request $request)
    {
        $user_id = $request->input('user_id');
        
        $all_roles = tb_role::orderBy('name', 'asc')->get();
        $user_roles = DB::table('role_users')->where('user_id', $user_id)->pluck('role_id')->toArray();
            
        $output = '';
        foreach($all_roles as $role) {
            $checked = in_array($role->id, $user_roles) ? 'checked' : '';
            
            $output .= '<div class="role-toggle-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f0f0f0;">
                <div>
                    <span style="font-size:15px; font-weight: 500;"><i class="fa fa-key text-yellow" style="margin-right:8px;"></i> ' . $role->name . '</span>
                </div>
                <div>
                    <label class="toggle-switch">
                        <input type="checkbox" class="role-switch" data-roleid="' . $role->id . '" ' . $checked . '>
                        <span class="toggle-slider round"></span>
                    </label>
                </div>
            </div>';
        }
        
        return $output;
    }

    public function addRole(Request $request)
    {
        $user_id = $request->input('user_id');
        $role_id = $request->input('role_id');
        
        $exists = tb_roleuser::where('user_id', $user_id)->where('role_id', $role_id)->first();
        if(!$exists && $user_id && $role_id) {
            tb_roleuser::create([
                'user_id' => $user_id,
                'role_id' => $role_id
            ]);
        }
        
        return response()->json(['status' => 'success']);
    }

    public function removeRole(Request $request)
    {
        $user_id = $request->input('user_id');
        $role_id = $request->input('role_id');
        
        tb_roleuser::where('user_id', $user_id)->where('role_id', $role_id)->delete();
        
        return response()->json(['status' => 'success']);
    }
}
