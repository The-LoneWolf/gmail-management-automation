<?php

namespace App\Services\Extraction;

use App\Enums\ExtractionStatus;
use App\Models\EmailExtraction;
use App\Models\EmailMessage;
use App\Models\ExtractionTemplate;
use Illuminate\Support\Str;

class EmailExtractionService
{
    public function extract(EmailMessage $message, ExtractionTemplate $template): EmailExtraction
    {
        $content = trim(($message->subject ?? '')."\n".($message->text_body ?? '')."\n".($message->snippet ?? ''));
        $data = [];

        foreach ($template->schema['fields'] ?? [] as $field => $definition) {
            $data[$field] = $this->extractField((string) $field, (array) $definition, $content);
        }

        return EmailExtraction::updateOrCreate(
            [
                'email_message_id' => $message->id,
                'extraction_template_id' => $template->id,
            ],
            [
                'extracted_data' => $data,
                'confidence' => $data === [] ? 0 : 0.76,
                'model' => 'local-extractor-v1',
                'prompt_version' => 'phase-5-local-v1',
                'status' => $data === [] ? ExtractionStatus::NeedsReview : ExtractionStatus::Completed,
                'error_message' => null,
            ],
        );
    }

    private function extractField(string $field, array $definition, string $content): mixed
    {
        if (isset($definition['pattern']) && preg_match('/'.$definition['pattern'].'/i', $content, $matches) === 1) {
            return $matches[1] ?? $matches[0];
        }

        return match ($definition['type'] ?? null) {
            'email' => $this->match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $content),
            'money' => $this->match('/(?:USD|\$)\s?([0-9]+(?:\.[0-9]{2})?)/i', $content),
            'date' => $this->match('/\b([0-9]{4}-[0-9]{2}-[0-9]{2})\b/', $content),
            default => Str::contains(Str::lower($content), Str::lower($field)) ? Str::limit($content, 120, '') : null,
        };
    }

    private function match(string $pattern, string $content): ?string
    {
        if (preg_match($pattern, $content, $matches) !== 1) {
            return null;
        }

        return $matches[1] ?? $matches[0];
    }
}
