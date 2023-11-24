<?php

namespace App\Enums;

enum ModerationLogStatus: string
{
    case APPROVED = 'approved';
    case DENIED   = 'denied';
}
