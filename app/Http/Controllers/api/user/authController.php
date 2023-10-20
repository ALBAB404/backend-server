<?php

namespace App\Http\Controllers\api\user;

use App\Http\Controllers\Controller;
use App\Http\Requests\user\loginRequest;
use App\Http\Requests\user\registerRequest;
use App\Http\Resources\user\authresource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class authController extends Controller
{
    public function login(loginRequest $request)
    {
        $user = User::where('phone', $request->phone)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $this->makeToken($user);
    }
    public function register(registerRequest $request)
    {
        $user = User::create($request->all());
        return $this->makeToken($user);
    }


    public function makeToken($user)
    {
        $token = $user->createToken('__Token')->plainTextToken;

        // return authresource::make($user);

        return (new authresource($user))
                ->additional(['meta' => [
                    'token' => $token,
                    'token_type' => "bear_Token",
                ]]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return send_ms('admin logout', true, 200);
    }
    public function user(Request $request)
    {
        return authresource::make($request->user());
    }

}