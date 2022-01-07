<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\EmailRegisteredUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthenticationApiController extends Controller
{
    public function register(Request $request)
    {
        // validate the email, it should be unique
        $validator = Validator::make($request->all(), [
            'email' => 'unique:users,email',
        ]);

        // return failed message
        if($validator->fails())
        {
            return response()->json([
                'message' => 'Email already taken.'
            ], 400);
        }

        // create the user
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        // send welcome email
        EmailRegisteredUser::dispatch($request->email);

        return response()->json([
            'message' => 'User successfully registered.'
        ], 201);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        // check email and password
        if (! $user || ! Hash::check($request->password, $user->password)) {

            // lockout logic
            if($this->hasTooManyLoginAttempts($request))
            {
                return response()->json([
                    'message' => 'Too many failed login attempts. Please try again in 5 minutes.'
                ], 401);
            }

            return response()->json([
                'message' => 'Invalid credentials.'
            ], 401);
        }

        // clear login attempt count
        $this->clearFailedLoginAttempts($request);

        return response()->json([
            'access_token' => $user->createToken('Dishtansya Token')->plainTextToken
        ], 201);
    }

    public function hasTooManyLoginAttempts(Request $request)
    {
        // initialize keys for failed attempt count and lockout
        $cacheKeyAttempt = "$request->email-loginAttempt";
        $cacheKeyLockout = "$request->email-lockout";

        // check if locked out
        if(Cache::has($cacheKeyLockout))
        {
            return true;
        }

        // increment attempt
        Cache::put($cacheKeyAttempt, Cache::get($cacheKeyAttempt) + 1);

        // condition to check if attempt is 5
        if(Cache::get($cacheKeyAttempt) == 5)
        {
            // initiate lockout
            Cache::put($cacheKeyLockout, 'locked', 300); //
            $this->clearFailedLoginAttempts($request);
        }

        return false;
    }

    public function clearFailedLoginAttempts(Request $request)
    {
        $cacheKeyAttempt = "$request->email-loginAttempt";
        Cache::forget($cacheKeyAttempt);
    }
}
