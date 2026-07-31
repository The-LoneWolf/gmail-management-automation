<?php

namespace App\Enums;

enum AiFeature: string
{
    case EmailClassification = 'email_classification';
    case EmailExtraction = 'email_extraction';
    case AutomationCondition = 'automation_condition';
    case ReplyDraft = 'reply_draft';
    case Summarization = 'summarization';

    public function label(): string
    {
        return match ($this) {
            self::EmailClassification => 'Email classification',
            self::EmailExtraction => 'Email extraction',
            self::AutomationCondition => 'Automation condition',
            self::ReplyDraft => 'Reply draft',
            self::Summarization => 'Summarization',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $feature): array => [$feature->value => $feature->label()])
            ->all();
    }
}
