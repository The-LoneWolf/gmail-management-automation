<?php

namespace App\Enums;

enum GmailAccountStatus: string
{
    case Connected = 'connected';
    case Syncing = 'syncing';
    case NeedsReconnect = 'needs_reconnect';
    case Failed = 'failed';
    case Disabled = 'disabled';
}
