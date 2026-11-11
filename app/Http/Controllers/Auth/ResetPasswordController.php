<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('throttle:5,1')->only('reset');
    }

    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords {
        setUserPassword as protected setDefaultUserPassword;
    }

    protected function setUserPassword($user, $password)
    {
        $this->setDefaultUserPassword($user, $password);

        $today = now();
        $user->reset_date = $today->toDateString();
        $user->expired_date = $today->copy()->addMonthsNoOverflow(3)->toDateString();
    }

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
}
