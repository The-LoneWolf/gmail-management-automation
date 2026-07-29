<?php

namespace App\Enums;

enum EmailProcessingStatus: string
{
    case Pending = 'pending';
    case Parsing = 'parsing';
    case Classifying = 'classifying';
    case Extracting = 'extracting';
    case Completed = 'completed';
    case Failed = 'failed';
    case NeedsReview = 'needs_review';
    case Skipped = 'skipped';
}
