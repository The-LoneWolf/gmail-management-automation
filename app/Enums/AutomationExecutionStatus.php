<?php

namespace App\Enums;

enum AutomationExecutionStatus: string
{
    case Matched = 'matched';
    case Executed = 'executed';
    case RequiresApproval = 'requires_approval';
    case Failed = 'failed';
    case Skipped = 'skipped';
}
