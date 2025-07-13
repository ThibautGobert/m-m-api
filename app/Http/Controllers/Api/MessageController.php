<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Enums\MessageType;
use Illuminate\Support\Facades\Gate;

class MessageController extends Controller
{
    public function index(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $messages = $conversation->messages()
            ->with(['sender', 'media', 'replies'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function store(Request $request, Conversation $conversation)
    {
        Gate::authorize('view', $conversation);

        $data = $request->validate([
            'content' => 'nullable|string',
            'replied_to_id' => 'nullable|exists:messages,id',
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => $request->user()->id,
            'content' => $data['content'] ?? null,
            'type' => MessageType::TEXT,
            'has_media' => false,
            'replied_to_id' => $data['replied_to_id'] ?? null,
        ]);

        $conversation->update([
            'last_message_id' => $message->id,
            'last_message_at' => now(),
        ]);

        return response()->json($message->load(['sender', 'replies']));
    }
}
