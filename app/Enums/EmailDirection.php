<?php

namespace App\Enums;

enum EmailDirection: string
{
    case Incoming = 'incoming';
    case Outgoing = 'outgoing';
    case Draft = 'draft';
    case Unknown = 'unknown';
}
