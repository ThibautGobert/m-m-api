<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $conversations = $user->conversations()
            ->with(['lastMessage', 'participants'])
            ->orderByDesc('last_message_at')
            ->get();

        return response()->json($conversations);
    }

    public function show(Request $request, Conversation $conversation)
    {
        Gate::authorize('view', $conversation);

        $conversation->load(['participants', 'lastMessage']);

        return response()->json($conversation);
    }
}
