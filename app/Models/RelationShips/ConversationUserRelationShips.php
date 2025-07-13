<?php

namespace App\Models\RelationShips;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait ConversationUserRelationShips
{
    /**
     * Conversation associée
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * User associé
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
