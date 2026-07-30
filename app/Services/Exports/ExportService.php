<?php

namespace App\Services\Exports;

use App\Enums\ExportStatus;
use App\Models\EmailMessage;
use App\Models\ExportTemplate;
use App\Models\GeneratedExport;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExportService
{
    public function generate(ExportTemplate $template): GeneratedExport
    {
        $export = GeneratedExport::create([
            'export_template_id' => $template->id,
            'user_id' => $template->user_id,
            'status' => ExportStatus::Processing,
            'format' => $template->format,
            'storage_disk' => 'local',
        ]);

        try {
            $rows = $this->rows($template);
            $path = 'exports/'.$export->id.'-'.Str::slug($template->name).'.'.$template->format;
            Storage::disk('local')->put($path, $this->render($template->format, $rows));

            $export->update([
                'status' => ExportStatus::Completed,
                'storage_path' => $path,
                'row_count' => count($rows),
            ]);
        } catch (\Throwable $throwable) {
            $export->update([
                'status' => ExportStatus::Failed,
                'error_message' => $throwable->getMessage(),
            ]);
        }

        return $export;
    }

    private function rows(ExportTemplate $template): array
    {
        $query = EmailMessage::query()
            ->whereRelation('gmailAccount', 'user_id', $template->user_id)
            ->with(['gmailAccount', 'thread', 'topics', 'extractions']);

        if ($sender = Arr::get($template->filters ?? [], 'sender')) {
            $query->where('sender_email', $sender);
        }

        if ($priority = Arr::get($template->filters ?? [], 'priority')) {
            $query->whereRelation('thread', 'priority', $priority);
        }

        return $query->latest('received_at')->get()
            ->map(fn (EmailMessage $message): array => $this->row($message, $template->columns))
            ->all();
    }

    private function row(EmailMessage $message, array $columns): array
    {
        $row = [];

        foreach ($columns as $column) {
            $label = $column['label'] ?? $column['source'];
            $row[$label] = data_get([
                'email' => $message->toArray(),
                'thread' => $message->thread?->toArray(),
            ], $column['source']);
        }

        return $row;
    }

    private function render(string $format, array $rows): string
    {
        if ($format === 'json') {
            return json_encode($rows, JSON_PRETTY_PRINT) ?: '[]';
        }

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, array_keys($rows[0] ?? []));

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);

        return stream_get_contents($handle) ?: '';
    }
}
