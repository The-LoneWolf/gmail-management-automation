<?php

namespace App\Services\EmailIntelligence;

use App\Enums\ClassificationStatus;
use App\Enums\EmailProcessingStatus;
use App\Enums\TopicMatchSource;
use App\Models\EmailClassification;
use App\Models\EmailMessage;
use App\Models\Topic;

class EmailClassificationPersister
{
    public function persist(EmailMessage $message, EmailClassificationResult $result): EmailClassification
    {
        $classification = EmailClassification::create([
            'email_message_id' => $message->id,
            'summary' => $result->summary,
            'suggested_state_id' => $result->suggestedStateId,
            'state_confidence' => $result->stateConfidence,
            'priority' => $result->priority,
            'sentiment' => $result->sentiment,
            'language' => $result->language,
            'requires_reply' => $result->requiresReply,
            'requires_human_review' => $result->requiresHumanReview,
            'suggested_actions' => $result->suggestedActions,
            'raw_result' => $result->rawResult,
            'model' => $result->model,
            'prompt_version' => $result->promptVersion,
            'status' => $result->requiresHumanReview ? ClassificationStatus::NeedsReview : ClassificationStatus::Completed,
        ]);

        foreach ($result->topics as $topicResult) {
            $topic = Topic::find($topicResult['topic_id']);

            if (! $topic) {
                continue;
            }

            $message->topics()->syncWithoutDetaching([
                $topic->id => [
                    'confidence' => $topicResult['confidence'],
                    'matched_by' => TopicMatchSource::Ai->value,
                    'reasoning' => $topicResult['reason'] ?? null,
                ],
            ]);
        }

        $message->update([
            'processing_status' => $result->requiresHumanReview
                ? EmailProcessingStatus::NeedsReview
                : EmailProcessingStatus::Completed,
            'ai_processed_at' => now(),
        ]);

        $message->thread()->update([
            'ai_summary' => $result->summary,
            'current_state_id' => $result->suggestedStateId,
            'priority' => $result->priority->value,
            'requires_reply' => $result->requiresReply,
            'requires_human_review' => $result->requiresHumanReview,
        ]);

        return $classification;
    }
}
