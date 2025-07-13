<?php

namespace App\Models\Attributes;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

trait MessageMediaAttributes
{
    public function url(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Storage::disk(config('filesystems.default'))->temporaryUrl(
                $this->media_path,
                now()->addMinutes(5)
            )
        );
    }
}
