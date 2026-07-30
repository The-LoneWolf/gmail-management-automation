<?php

namespace App\Services\EmailIntelligence;

use App\Enums\EmailPriority;
use App\Enums\EmailSentiment;
use App\Models\EmailMessage;
use App\Models\State;
use App\Models\Topic;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class KeywordEmailIntelligenceService implements EmailIntelligenceService
{
    public function classify(EmailMessage $message, Collection $topics, Collection $states): EmailClassificationResult
    {
        $content = Str::lower(trim(($message->subject ?? '').' '.($message->text_body ?? '').' '.($message->snippet ?? '')));
        $matchedTopics = $topics
            ->map(fn (Topic $topic): ?array => $this->scoreTopic($topic, $content))
            ->filter()
            ->values()
            ->all();

        $priority = $this->priority($content);
        $sentiment = $this->sentiment($content);
        $requiresReply = $this->requiresReply($content);
        $suggestedState = $this->suggestedState($states, $requiresReply, $matchedTopics);
        $requiresHumanReview = collect($matchedTopics)->contains(fn (array $topic): bool => (bool) $topic['requires_human_review'])
            || $priority === EmailPriority::Urgent;

        $summary = Str::limit($message->text_body ?: $message->snippet ?: $message->subject ?: 'No email content imported.', 240, '');

        return new EmailClassificationResult(
            summary: $summary,
            topics: $matchedTopics,
            suggestedStateId: $suggestedState?->id,
            stateConfidence: $suggestedState ? 0.82 : 0,
            priority: $priority,
            sentiment: $sentiment,
            language: 'en',
            requiresReply: $requiresReply,
            requiresHumanReview: $requiresHumanReview,
            suggestedActions: $requiresReply ? ['Review the message and prepare a manual reply.'] : ['Review and file the message.'],
            rawResult: [
                'strategy' => 'keyword',
                'matched_topic_count' => count($matchedTopics),
            ],
            model: 'local-keyword-v1',
            promptVersion: 'phase-4-keyword-v1',
        );
    }

    private function scoreTopic(Topic $topic, string $content): ?array
    {
        $keywords = collect($topic->keywords ?: [$topic->name])
            ->map(fn (string $keyword): string => Str::lower($keyword))
            ->filter()
            ->values();

        $hits = $keywords->filter(fn (string $keyword): bool => Str::contains($content, $keyword))->count();

        if ($hits === 0) {
            return null;
        }

        $confidence = min(0.99, 0.70 + ($hits * 0.10));

        return [
            'topic_id' => $topic->id,
            'confidence' => $confidence,
            'reason' => 'Matched '.Str::plural('keyword', $hits).' for '.$topic->name.'.',
            'requires_human_review' => $topic->requires_human_review,
        ];
    }

    private function priority(string $content): EmailPriority
    {
        if (Str::contains($content, ['urgent', 'asap', 'immediately', 'security alert'])) {
            return EmailPriority::Urgent;
        }

        if (Str::contains($content, ['complaint', 'charged twice', 'overdue', 'legal'])) {
            return EmailPriority::High;
        }

        if (Str::contains($content, ['newsletter', 'digest', 'unsubscribe'])) {
            return EmailPriority::Low;
        }

        return EmailPriority::Normal;
    }

    private function sentiment(string $content): EmailSentiment
    {
        if (Str::contains($content, ['angry', 'furious', 'unacceptable'])) {
            return EmailSentiment::Angry;
        }

        if (Str::contains($content, ['complaint', 'problem', 'charged twice', 'failed'])) {
            return EmailSentiment::Negative;
        }

        if (Str::contains($content, ['thank you', 'great', 'excellent'])) {
            return EmailSentiment::Positive;
        }

        return EmailSentiment::Neutral;
    }

    private function requiresReply(string $content): bool
    {
        return Str::contains($content, ['?', 'please reply', 'can you', 'could you', 'need help', 'complaint']);
    }

    private function suggestedState(Collection $states, bool $requiresReply, array $matchedTopics): ?State
    {
        if ($requiresReply) {
            return $states->first(fn (State $state): bool => $state->slug === 'action-required')
                ?? $states->first(fn (State $state): bool => $state->slug === 'needs-review');
        }

        if ($matchedTopics !== []) {
            return $states->first(fn (State $state): bool => $state->slug === 'needs-review')
                ?? $states->first(fn (State $state): bool => $state->is_initial);
        }

        return $states->first(fn (State $state): bool => $state->is_initial);
    }
}
