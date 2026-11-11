<?php

namespace App\Models;

use App\Exceptions\PasswordResetDeliveryException;
use App\Http\Controllers\WuzapiController;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendPasswordResetNotification($token)
    {
        $phone = DB::table('tb_emails')
            ->join('tb_employee_detail', 'tb_employee_detail.id_employee', '=', 'tb_emails.id_employee')
            ->where('tb_emails.email_address', $this->email)
            ->value('tb_employee_detail.nomor_telepon');

        if (!$phone) {
            throw new PasswordResetDeliveryException(
                'Could not send the reset link via WhatsApp. Please contact HR support or try again later.'
            );
        }

        $resetPath = URL::route('password.reset', [
            'token' => $token,
            'email' => $this->email,
        ], false);
        $resetUrl = rtrim(config('app.password_reset_url'), '/') . $resetPath;
        $expiresIn = config('auth.passwords.users.expire', 60);
        $result = WuzapiController::sendInternalMessage(
            $phone,
            "Halo, gunakan tautan ini untuk mengatur ulang kata sandi HRMS Anda:\n"
                . "{$resetUrl}\n\nBerlaku selama {$expiresIn} menit. Jangan bagikan tautan ini. "
                . 'Abaikan pesan ini jika Anda tidak meminta reset kata sandi.'
        );

        if (!is_array($result) || ($result['success'] ?? false) !== true) {
            throw new PasswordResetDeliveryException(
                'Could not send the reset link via WhatsApp. Please contact HR support or try again later.'
            );
        }
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_users');
    } 
    public function hasRole($role) 
    {
      return $this->roles()->where('name', $role)->count() == 1;
    }    
}
