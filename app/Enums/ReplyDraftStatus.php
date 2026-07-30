<?php

namespace App\Enums;

enum ReplyDraftStatus: string
{
    case Draft = 'draft';
    case PendingApproval = 'pending_approval';
    case Approved = 'approved';
    case Sending = 'sending';
    case Sent = 'sent';
    case Failed = 'failed';
}
