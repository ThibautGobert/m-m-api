<?php

namespace App\Enums;

enum ConversationUserStatusType: int
{
    case ACTIVE = 1;
    case MUTED = 2;
    case BANNED = 3;
    case PENDING = 4;
}
