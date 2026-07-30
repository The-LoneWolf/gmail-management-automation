<?php

namespace App\Services\EmailIntelligence;

use App\Enums\EmailPriority;
use App\Enums\EmailSentiment;

class EmailClassificationResult
{
    public function __construct(
        public readonly string $summary,
        public readonly array $topics,
        public readonly ?int $suggestedStateId,
        public readonly float $stateConfidence,
        public readonly EmailPriority $priority,
        public readonly EmailSentiment $sentiment,
        public readonly ?string $language,
        public readonly bool $requiresReply,
        public readonly bool $requiresHumanReview,
        public readonly array $suggestedActions,
        public readonly array $rawResult,
        public readonly string $model,
        public readonly string $promptVersion,
    ) {}
}
