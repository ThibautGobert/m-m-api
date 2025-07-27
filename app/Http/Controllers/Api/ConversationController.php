<?php

namespace App\Http\Controllers\Api;

use App\Enums\ConversationType;
use App\Enums\ConversationUserStatusType;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

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

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'participant_ids' => 'required|array|min:1',
            'participant_ids.*' => 'exists:users,id',
        ]);

        // Crée une conversation "private" par défaut
        $conversation = Conversation::create([
            'type' => ConversationType::PRIVATE,
            'last_message_at' => null,
        ]);

        // Ajoute l'utilisateur actuel
        $conversation->participants()->attach($user->id, [
            'status' => ConversationUserStatusType::ACTIVE,
            'joined_at' => now(),
        ]);

        // Ajoute les autres participants
        foreach ($data['participant_ids'] as $participantId) {
            $conversation->participants()->attach($participantId, [
                'status' => ConversationUserStatusType::ACTIVE,
                'joined_at' => now(),
            ]);
        }

        return response()->json($conversation->load('participants'));
    }

    public function startPrivate(Request $request, string $uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        $existingConversation = Conversation::where('type', ConversationType::PRIVATE)
            ->whereHas('participants', function ($q) {
                $q->where('user_id', auth()->user()->id);
            })
            ->whereHas('participants', function ($q) use($user) {
                $q->where('user_id', $user->id);
            })
            ->whereDoesntHave('participants', function ($q) use($user) {
                $q->whereNotIn('user_id', [auth()->user()->id, $user->id]);
            })
            ->first();

        if ($existingConversation) {
            return response()->json($existingConversation->load('participants'));
        }

        $conversation = Conversation::create([
            'uuid' => Str::uuid(),
            'type' => ConversationType::PRIVATE,
            'last_message_at' => null,
        ]);

        $conversation->participants()->attach(auth()->user()->id, [
            'status' => ConversationUserStatusType::ACTIVE,
            'joined_at' => now(),
        ]);

        $conversation->participants()->attach($user->id, [
            'status' => ConversationUserStatusType::ACTIVE,
            'joined_at' => now(),
        ]);

        return response()->json($conversation->load('participants'));
    }
}
