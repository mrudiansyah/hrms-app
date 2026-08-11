<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Services\HcmisService;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        $login = request()->input('email');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'nik';
        request()->merge([$field => $login]);
        return $field;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $hcmis;

    public function __construct(HcmisService $hcmis)
    {
        $this->middleware('guest')->except('logout');
        $this->hcmis = $hcmis;
    }

    /**
     * After user is authenticated, attempt HCMIS login and store token/status in session.
     * This must not block the main authentication flow.
     */
    public function authenticated(Request $request, $user)
    {
        try {
            $email = $request->input('email') ?? $user->email;
            $password = $request->input('password') ?? config('hcmis.password');

            $resp = $this->hcmis->login($email, $password);

            if (is_array($resp) && isset($resp['access_token'])) {
                // store per-user key for reference
                Cache::put('hcmis_token:'.$user->id, $resp['access_token'], now()->addHours(2));
                // also set service-level token key used by HcmisService
                $this->hcmis->setToken($resp['access_token']);
                $request->session()->put('hcmis_login', [
                    'success' => true,
                    'token' => $resp['access_token']
                ]);
            } else {
                $request->session()->put('hcmis_login', [
                    'success' => false,
                    'error' => $resp
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('HCMIS login failed: '.$e->getMessage());
            $request->session()->put('hcmis_login', [
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
