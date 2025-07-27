<?php

namespace App\Models\RelationShips;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait ConversationRelationShips
{
    /**
     * Relation vers le dernier message
     */
    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    /**
     * Tous les messages de la conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Participants (users)
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'conversation_user', 'conversation_id', 'user_id')
            ->withPivot(['joined_at', 'status', 'last_read_at', 'notifications_enabled'])
            ->withTimestamps();
    }
}
