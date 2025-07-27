<?php

namespace App\Models;

use App\Enums\MessageMediaType;
use App\Models\Attributes\MessageMediaAttributes;
use App\Models\RelationShips\MessageMediaRelationShips;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageMedia extends Model
{
    use SoftDeletes,
        MessageMediaRelationShips,
        MessageMediaAttributes;

    protected $table = 'message_media';

    protected $fillable = [
        'uuid',
        'type',
        'message_id',
        'media_path',
        'order',
    ];

    protected $casts = [
        'type' => MessageMediaType::class,
    ];
}
