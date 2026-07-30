<?php

namespace App\Enums;

enum ClassificationStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';
    case NeedsReview = 'needs_review';
}
