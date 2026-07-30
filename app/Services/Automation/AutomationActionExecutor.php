<?php

namespace App\Services\Automation;

use App\Enums\AutomationExecutionStatus;
use App\Models\AutomationExecution;
use App\Models\NotificationChannel;

class AutomationActionExecutor
{
    private const RESTRICTED = ['send_email', 'delete_email', 'forward_email', 'archive_email', 'webhook_sensitive'];

    public function execute(AutomationExecution $execution, array $actions): void
    {
        $executed = [];

        foreach ($actions as $action) {
            $type = $action['type'] ?? null;

            if (in_array($type, self::RESTRICTED, true)) {
                $execution->update([
                    'status' => AutomationExecutionStatus::RequiresApproval,
                    'requires_approval' => true,
                    'executed_actions' => $executed,
                ]);

                return;
            }

            if ($type === 'set_state' && isset($action['state_id'])) {
                $execution->emailThread?->update(['current_state_id' => $action['state_id']]);
            }

            if ($type === 'notify' && isset($action['channel_id'])) {
                NotificationChannel::find($action['channel_id'])?->update(['last_tested_at' => now()]);
            }

            $executed[] = $action;
        }

        $execution->update([
            'status' => AutomationExecutionStatus::Executed,
            'executed_actions' => $executed,
        ]);
    }
}
