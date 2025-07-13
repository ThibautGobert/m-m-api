<?php

namespace App\Enums;

enum MessageMediaType: int
{
    case IMAGE = 1;
    case VIDEO = 2;
    case AUDIO = 3;
    case FILE = 4;
}
