<?php

namespace App\Http\Resources;

use App\Enums\ConversationUserStatusType;
use App\Models\Conversation;
use App\Models\ConversationUser;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'conversation_id' => auth()->user() ? Conversation::where('type', \App\Enums\ConversationType::PRIVATE)
                ->whereHas('participants', function ($q) {
                    $q->where('user_id', auth()->user()->id);
                })
                ->whereHas('participants', function ($q) {
                    $q->where('user_id', $this->id);
                })
                ->whereDoesntHave('participants', function ($q) {
                    $q->whereNotIn('user_id', [auth()->user()->id, $this->id]);
                })
                ->first()?->id : null
        ];
    }
}
