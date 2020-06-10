<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

// use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

        // $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }
    public function verify(Request $request, User $user){
        if(! URL::hasValidSignature($request)){
                return response()->json(["error" => [
                    "message" => "Invalid Verification Link"
                ]], 422);
        }

        if($user->hasVerifiedEmail()){
            return response()->json(["errors" => [
                "message" => "Email address already verified"
            ]], 422);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json(['message' => 'Email Succesfully verified'], 200);
    }
    public function resend(Request $request){

        $this->validate($request, [
            'email' => ['email','required']
        ]);

        $user = User::where('email', $request->email)->first();

        if(! $user){
            return response()->json(["errors" => [
                "email" => "No user could be found with this email address",
            ]]);
        }

        if($user->hasVerifiedEmail()){
            return response()->json(["error" => [
                "message" => "Email address already verified"
            ]], 422);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['status' => "verification link resent"]);
    }
}
