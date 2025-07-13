<?php

namespace App\Models\RelationShips;

use App\Models\Message;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait MessageMediaRelationShips
{
    /**
     * Message associé
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}
