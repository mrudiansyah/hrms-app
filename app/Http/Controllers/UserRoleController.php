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

    public function toggleMaintenance(Request $request)
    {
        if (!$request->user()->hasRole('role')) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah maintenance user.');
        }

        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $maintenanceHash = '$2y$10$smebnvs/NNNVDdlwaRNeVucAey7Q.XIXR0UNHEtbgbaAbDI..9xDi';

        $user = DB::transaction(function () use ($validated, $maintenanceHash) {
            $user = DB::table('users')
                ->where('id', $validated['user_id'])
                ->lockForUpdate()
                ->first();

            if (!$user) {
                abort(404, 'User tidak ditemukan.');
            }

            if ((bool) $user->maintenance) {
                if ($user->password_copy === null || $user->password_copy === '') {
                    abort(422, 'Password sebelumnya tidak tersedia; maintenance tidak dapat dinonaktifkan.');
                }

                DB::table('users')->where('id', $user->id)->update([
                    'password' => $user->password_copy,
                    'password_copy' => null,
                    'maintenance' => false,
                ]);

                return [
                    'maintenance' => false,
                    'message' => 'Maintenance dinonaktifkan dan password sebelumnya dipulihkan.',
                ];
            }

            DB::table('users')->where('id', $user->id)->update([
                'password_copy' => $user->password,
                'password' => $maintenanceHash,
                'maintenance' => true,
            ]);

            return [
                'maintenance' => true,
                'message' => 'Maintenance diaktifkan.',
            ];
        });

        return response()->json($user);
    }
}
