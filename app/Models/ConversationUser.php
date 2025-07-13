<?php

namespace App\Models;

use App\Enums\ConversationUserStatusType;
use App\Models\RelationShips\ConversationUserRelationShips;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConversationUser extends Model
{
    use SoftDeletes,
        ConversationUserRelationShips;

    protected $table = 'conversation_user';

    protected $fillable = [
        'conversation_id',
        'user_id',
        'status',
        'joined_at',
        'last_read_at',
        'notifications_enabled',
    ];

    protected $casts = [
        'status' => ConversationUserStatusType::class,
        'joined_at' => 'datetime',
        'last_read_at' => 'datetime',
        'notifications_enabled' => 'boolean',
    ];
}
