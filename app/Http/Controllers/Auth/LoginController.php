<?php

namespace App\Http\Controllers\Auth;

use App\Action\GetUserSecurityData;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request, GetUserSecurityData $action)
    {
        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            $user = $this->guard()->user();

            return response()->json($action->withUser($user)->execute());
        }
        return $this->sendFailedLoginResponse($request);
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('api')->user();

        if ($user) {
            $user->token()->revoke();

            return response()->json(['data' => 'User logged out.'], 200);
        }

        return response()->json(['data' => 'User not logged.'], 404);
    }

    public function refresh_token(Request $request)
    {
        $this->validate($request, [
            'refresh_token' => 'required'
        ]);

        $action = new GetUserSecurityData();
        $newToken = $action->getNewToken($request->refresh_token);

        return response()->json($newToken);
    }
}
