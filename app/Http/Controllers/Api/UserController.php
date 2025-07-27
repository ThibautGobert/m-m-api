<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCardResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function cards(Request $request)
    {
        Auth::shouldUse('sanctum');
        Auth::guard('sanctum')->user();

        return response()->json(UserCardResource::collection(User::all()));
    }

    public function get(Request $request, string $uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        return response()->json(new UserCardResource($user));
    }
}
