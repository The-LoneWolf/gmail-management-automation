<?php

namespace Database\Factories;

use App\Enums\ExportStatus;
use App\Models\ExportTemplate;
use App\Models\GeneratedExport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GeneratedExport>
 */
class GeneratedExportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'export_template_id' => ExportTemplate::factory(),
            'user_id' => User::factory(),
            'status' => ExportStatus::Completed,
            'format' => 'csv',
            'storage_disk' => 'local',
            'storage_path' => 'exports/example.csv',
            'row_count' => 1,
        ];
    }
}
