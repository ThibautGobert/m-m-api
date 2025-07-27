<?php

namespace App\Models;

use App\Enums\MessageType;
use App\Models\RelationShips\MessageRelationShips;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes,
        MessageRelationShips;

    protected $fillable = [
        'type',
        'uuid',
        'conversation_id',
        'sender_id',
        'has_media',
        'content',
        'delivered_at',
        'read_at',
        'replied_to_id',
    ];

    protected $hidden = ['id'];

    protected $casts = [
        'type' => MessageType::class,
        'has_media' => 'boolean',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
    ];
}
