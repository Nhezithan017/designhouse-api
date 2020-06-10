<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    public function attemptLogin(Request  $request){
        $token = $this->guard()->attempt($this->credentials($request));

        if(! $token){
            return false;
        }

        $user = $this->guard()->user();

        //Get the authenticated user
        if($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()){
            return false;
        }

        //Set the user's token
        $this->guard()->setToken($token);

        return true;

    }

    protected function sendLoginResponse(Request $request){
        $this->clearLoginAttempts($request);

        //get the token from aunthentication guard (JWT)
        $token = (string)$this->guard()->getToken();

        //extract the expiry date of the token
        $expiration = $this->guard()->getPayload()->get('exp');

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expire_in' => $expiration
        ]);
    }

    protected function sendFailedLoginResponse(){

        $user = $this->guard()->user();

        if($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()){
            return response()->json(["errors" => [
                "verification" => "You need to verify your email account"
            ]]);
        }

        throw ValidationException::withMessages([
            $this->username() => "Invalid Credentials"
        ]);
    }

    public function logout(){

        $this->guard()->logout();

        return response()->json(['message' => 'Logged out Succesfully.']);
    }
}
