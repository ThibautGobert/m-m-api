<?php

namespace App\Models;

use App\Enums\ConversationType;
use App\Models\RelationShips\ConversationRelationShips;
use App\Policies\ConversationPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UsePolicy(ConversationPolicy::class)]
class Conversation extends Model
{
    use SoftDeletes,
        ConversationRelationShips;

    protected $fillable = [
        'type',
        'last_message_id',
        'last_message_at',
    ];

    protected $casts = [
        'type' => ConversationType::class,
        'last_message_at' => 'datetime',
    ];
}
