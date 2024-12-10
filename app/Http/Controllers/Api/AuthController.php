<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Models\User;
use App\Permissions\V1\Abilities;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponses;

    /**
     * Login
     *
     * Authenticates the user and returns an API token
     * @unauthenticated
     * @group Authentication
     * @response 200 {
     * "message": "authenticated",
     * "data": {
     * "token": "{YOUR_AUTH_KEY}"
     * },
     * "status": 200
     * }
     *
     */
    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all());

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Invalid credentials', 401);
        }

        $user = User::firstWhere('email', $request->email);

        return $this->ok(
            'authenticated',
            [
                'token' => $user->createToken(
                    'Api Token for ' . $user->email,
                    Abilities::getAbilities($user),
                    now()->addMonth()
                )->plainTextToken
            ]
        );

    }


    /**
     * Logout
     *
     * Signs out the user and destroys API token
     * @group Authentication
     * @response 200 {}
     *
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->ok('');
    }
}
