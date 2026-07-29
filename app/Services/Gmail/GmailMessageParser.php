<?php

namespace App\Services\Gmail;

use App\Enums\EmailDirection;
use Carbon\CarbonImmutable;
use Google\Service\Gmail\Message;
use Google\Service\Gmail\MessagePart;
use Illuminate\Support\Str;

class GmailMessageParser
{
    public function parse(Message $message): array
    {
        $payload = $message->getPayload();
        $headers = $this->headers($payload?->getHeaders() ?? []);
        $labels = $message->getLabelIds() ?? [];
        $bodies = $this->extractBodies($payload);
        $attachments = $this->extractAttachments($payload);
        $sender = $this->parseAddress($headers['from'] ?? '');

        return [
            'gmail_message_id' => (string) $message->getId(),
            'gmail_thread_id' => (string) $message->getThreadId(),
            'history_id' => $message->getHistoryId() ? (string) $message->getHistoryId() : null,
            'message_id_header' => $headers['message-id'] ?? null,
            'in_reply_to_header' => $headers['in-reply-to'] ?? null,
            'references_header' => $headers['references'] ?? null,
            'sender_name' => $sender['name'],
            'sender_email' => $sender['email'] ?: 'unknown@example.invalid',
            'reply_to_email' => $this->parseAddress($headers['reply-to'] ?? '')['email'] ?: null,
            'to_addresses' => $this->parseAddressList($headers['to'] ?? ''),
            'cc_addresses' => $this->parseAddressList($headers['cc'] ?? ''),
            'bcc_addresses' => $this->parseAddressList($headers['bcc'] ?? ''),
            'subject' => $headers['subject'] ?? null,
            'snippet' => $message->getSnippet(),
            'text_body' => $bodies['text'],
            'html_body' => $bodies['html'],
            'sanitized_html_body' => $this->sanitizeHtml($bodies['html']),
            'received_at' => $this->receivedAt($headers, $message),
            'internal_date' => $this->internalDate($message),
            'labels' => $labels,
            'is_read' => ! in_array('UNREAD', $labels, true),
            'is_starred' => in_array('STARRED', $labels, true),
            'is_archived' => ! in_array('INBOX', $labels, true),
            'has_attachments' => $attachments !== [],
            'direction' => $this->direction($labels),
            'attachments' => $attachments,
        ];
    }

    private function headers(array $headers): array
    {
        $mapped = [];

        foreach ($headers as $header) {
            $mapped[Str::lower((string) $header->getName())] = (string) $header->getValue();
        }

        return $mapped;
    }

    private function extractBodies(?MessagePart $part): array
    {
        $bodies = ['text' => null, 'html' => null];

        if (! $part) {
            return $bodies;
        }

        $this->walkParts($part, function (MessagePart $part) use (&$bodies): void {
            $mimeType = Str::lower((string) $part->getMimeType());
            $data = $part->getBody()?->getData();

            if (! $data) {
                return;
            }

            if ($mimeType === 'text/plain' && $bodies['text'] === null) {
                $bodies['text'] = $this->base64UrlDecode($data);
            }

            if ($mimeType === 'text/html' && $bodies['html'] === null) {
                $bodies['html'] = $this->base64UrlDecode($data);
            }
        });

        return $bodies;
    }

    private function extractAttachments(?MessagePart $part): array
    {
        $attachments = [];

        if (! $part) {
            return $attachments;
        }

        $this->walkParts($part, function (MessagePart $part) use (&$attachments): void {
            $attachmentId = $part->getBody()?->getAttachmentId();

            if (! $attachmentId) {
                return;
            }

            $attachments[] = [
                'gmail_attachment_id' => $attachmentId,
                'filename' => $part->getFilename() ?: 'attachment',
                'mime_type' => $part->getMimeType(),
                'size_bytes' => (int) ($part->getBody()?->getSize() ?? 0),
            ];
        });

        return $attachments;
    }

    private function walkParts(MessagePart $part, callable $callback): void
    {
        $callback($part);

        foreach ($part->getParts() ?? [] as $child) {
            $this->walkParts($child, $callback);
        }
    }

    private function parseAddressList(string $value): array
    {
        if ($value === '') {
            return [];
        }

        return collect(explode(',', $value))
            ->map(fn (string $address): array => $this->parseAddress($address))
            ->filter(fn (array $address): bool => $address['email'] !== null)
            ->values()
            ->all();
    }

    private function parseAddress(string $value): array
    {
        $value = trim($value);

        if (preg_match('/^(?:"?([^"<]*)"?)?\s*<([^>]+)>$/', $value, $matches) === 1) {
            return [
                'name' => trim($matches[1]) ?: null,
                'email' => Str::lower(trim($matches[2])),
            ];
        }

        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return ['name' => null, 'email' => Str::lower($value)];
        }

        return ['name' => $value ?: null, 'email' => null];
    }

    private function sanitizeHtml(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html) ?? $html;

        return preg_replace('/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
    }

    private function receivedAt(array $headers, Message $message): ?CarbonImmutable
    {
        if (isset($headers['date'])) {
            try {
                return CarbonImmutable::parse($headers['date']);
            } catch (\Throwable) {
                return $this->internalDate($message);
            }
        }

        return $this->internalDate($message);
    }

    private function internalDate(Message $message): ?CarbonImmutable
    {
        $internalDate = $message->getInternalDate();

        return $internalDate ? CarbonImmutable::createFromTimestampMs((int) $internalDate) : null;
    }

    private function direction(array $labels): EmailDirection
    {
        if (in_array('SENT', $labels, true)) {
            return EmailDirection::Outgoing;
        }

        if (in_array('DRAFT', $labels, true)) {
            return EmailDirection::Draft;
        }

        if (in_array('INBOX', $labels, true)) {
            return EmailDirection::Incoming;
        }

        return EmailDirection::Unknown;
    }

    private function base64UrlDecode(string $value): string
    {
        return (string) base64_decode(strtr($value, '-_', '+/'));
    }
}
