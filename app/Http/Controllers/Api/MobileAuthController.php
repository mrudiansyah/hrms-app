<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MobileApiToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MobileAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->merge([
            'login' => $request->input('login', $request->input('email')),
        ]);
        $credentials = $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['login'])->first();
        if (!$user) {
            $employeeEmail = DB::table('tb_emails')
                ->join('tb_employees', 'tb_employees.id', '=', 'tb_emails.id_employee')
                ->where('tb_employees.NIK', $credentials['login'])
                ->value('tb_emails.email_address');
            if ($employeeEmail) {
                $user = User::where('email', $employeeEmail)->first();
            }
        }

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Email/NIK atau password salah.'], 401);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email akun HRMS belum diverifikasi.'], 403);
        }

        if ((int) $user->locked_status === 1) {
            return response()->json(['message' => 'Akun HRMS dikunci. Hubungi administrator.'], 403);
        }

        $passwordExpiryEnabled = DB::table('tb_utilities')
            ->where('atribut', 'expired_password')
            ->where('status', '1')
            ->exists();

        if ($passwordExpiryEnabled && $user->expired_date && $user->expired_date < now()->toDateString()) {
            return response()->json([
                'message' => 'Password HRMS sudah kedaluwarsa. Silakan ubah melalui HRMS web.',
            ], 403);
        }

        $plainTextToken = Str::random(80);
        $expiresAt = now()->addMinutes(30);

        MobileApiToken::create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $plainTextToken),
            'expires_at' => $expiresAt,
        ]);

        return response()->json([
            'token' => $plainTextToken,
            'expires_at' => $expiresAt->toIso8601String(),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        $employee = DB::table('tb_emails')
            ->leftJoin('tb_employees', 'tb_employees.id', '=', 'tb_emails.id_employee')
            ->leftJoin('tb_departments', 'tb_departments.id', '=', 'tb_employees.dept_id')
            ->leftJoin('tb_positions', 'tb_positions.id', '=', 'tb_employees.position_id')
            ->leftJoin('tb_employee_detail', 'tb_employee_detail.id_employee', '=', 'tb_employees.id')
            ->where('tb_emails.email_address', $user->email)
            ->select([
                'tb_employees.id as id_employee',
                'tb_employees.employee_name',
                'tb_employees.NIK',
                'tb_employees.gender',
                'tb_departments.dept_name',
                'tb_positions.position_name',
                'tb_employee_detail.nomor_telepon',
            ])
            ->first();

        $photo = $employee
            ? DB::table('tb_photos')
                ->where('id_employee', $employee->id_employee)
                ->orderByDesc('id')
                ->value('nama_photo')
            : null;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'employee' => $employee ? [
                'name' => $employee->employee_name,
                'nik' => $employee->NIK,
                'gender' => $employee->gender,
                'department' => $employee->dept_name,
                'position' => $employee->position_name,
                'phone' => $employee->nomor_telepon,
                'photo' => $photo,
            ] : null,
        ]);
    }

    public function logout(Request $request)
    {
        $request->attributes->get('mobile_api_token')->delete();

        return response()->json(['message' => 'Berhasil logout.']);
    }

    public function activity()
    {
        return response()->json(['active' => true]);
    }
}
