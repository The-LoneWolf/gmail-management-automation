<?php

namespace App\Enums;

enum EmailSentiment: string
{
    case Positive = 'positive';
    case Neutral = 'neutral';
    case Negative = 'negative';
    case Angry = 'angry';
    case Unknown = 'unknown';
}
