<?php

namespace App\Models;

use App\Enums\ExportStatus;
use Database\Factories\GeneratedExportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['export_template_id', 'user_id', 'status', 'format', 'storage_disk', 'storage_path', 'row_count', 'error_message'])]
class GeneratedExport extends Model
{
    /** @use HasFactory<GeneratedExportFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['status' => ExportStatus::class];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ExportTemplate::class, 'export_template_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
