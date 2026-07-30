<?php

namespace Tests\Feature;

use App\Enums\ExportStatus;
use App\Enums\ExtractionStatus;
use App\Models\EmailMessage;
use App\Models\ExportTemplate;
use App\Models\ExtractionTemplate;
use App\Services\Exports\ExportService;
use App\Services\Extraction\EmailExtractionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExtractionExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_local_extractor_persists_template_data(): void
    {
        $message = EmailMessage::factory()->create([
            'subject' => 'Invoice #INV-1001',
            'text_body' => 'Please pay invoice #INV-1001 for $199.00. Contact billing@example.com.',
        ]);
        $template = ExtractionTemplate::factory()->create([
            'user_id' => $message->gmailAccount->user_id,
        ]);

        $extraction = app(EmailExtractionService::class)->extract($message, $template);

        $this->assertSame(ExtractionStatus::Completed, $extraction->status);
        $this->assertSame('INV-1001', $extraction->extracted_data['invoice_number']);
        $this->assertSame('199.00', $extraction->extracted_data['amount']);
    }

    public function test_export_service_writes_csv_file(): void
    {
        Storage::fake('local');

        $message = EmailMessage::factory()->create(['subject' => 'Export me']);
        $template = ExportTemplate::factory()->create([
            'user_id' => $message->gmailAccount->user_id,
            'format' => 'csv',
        ]);

        $export = app(ExportService::class)->generate($template);

        $this->assertSame(ExportStatus::Completed, $export->status);
        $this->assertSame(1, $export->row_count);
        Storage::disk('local')->assertExists($export->storage_path);
    }
}
