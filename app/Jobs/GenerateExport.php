<?php

namespace App\Jobs;

use App\Models\ExportTemplate;
use App\Services\Exports\ExportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateExport implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $exportTemplateId) {}

    public function handle(ExportService $exports): void
    {
        $exports->generate(ExportTemplate::findOrFail($this->exportTemplateId));
    }
}
