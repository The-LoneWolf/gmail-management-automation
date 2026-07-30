<?php

namespace App\Enums;

enum TopicMatchSource: string
{
    case Ai = 'ai';
    case Rule = 'rule';
    case Manual = 'manual';
    case GmailLabel = 'gmail_label';
}
