<?php

namespace App\Enums;

enum ConversationType: int
{
    case PRIVATE = 1;
    case GROUP = 2;
    case CHANNEL = 3;
    case SYSTEM = 4;
}
