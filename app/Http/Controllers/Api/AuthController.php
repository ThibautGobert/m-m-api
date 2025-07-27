<?php

namespace App\Http\Controllers\Api;

use App\Events\Auth\EmailVerified;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\EmailVerificationRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'uuid' => $this->getUniqueUuid(),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        $token = $user->createToken('auth')->plainTextToken;

        event(new Registered($user));

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    private function getUniqueUuid(): string
    {
        do {
            $uuid = Str::uuid();
        } while(User::where('uuid', $uuid)->exists());
        return $uuid;
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les informations sont incorrectes.'],
            ]);
        }

        $token = $user->createToken('auth')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnecté']);
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();

        EmailVerified::dispatch($request->user());
       // event(new EmailVerified(auth()->user()->id));

        $token = auth()->user()->createToken('auth')->plainTextToken;
        return response()->json([
            'user' => auth()->user(),
            'token' => $token,
        ]);
    }

    public function resendVerificationEmail(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
    }

    public function user()
    {
        return response()->json(auth()->user());
    }
}
