<?php

namespace App\Models\RelationShips;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageMedia;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait MessageRelationShips
{
    /**
     * Conversation à laquelle appartient le message
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * User qui a envoyé le message
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Message auquel celui-ci répond (si reply)
     */
    public function repliedTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'replied_to_id');
    }

    /**
     * Messages qui répondent à celui-ci
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Message::class, 'replied_to_id');
    }

    /**
     * Médias liés à ce message
     */
    public function media(): HasMany
    {
        return $this->hasMany(MessageMedia::class);
    }
}
