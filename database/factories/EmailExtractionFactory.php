<?php

namespace Database\Factories;

use App\Enums\ExtractionStatus;
use App\Models\EmailExtraction;
use App\Models\EmailMessage;
use App\Models\ExtractionTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmailExtraction>
 */
class EmailExtractionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email_message_id' => EmailMessage::factory(),
            'extraction_template_id' => ExtractionTemplate::factory(),
            'extracted_data' => ['amount' => '99.00'],
            'confidence' => 0.76,
            'model' => 'local-extractor-v1',
            'prompt_version' => 'factory-v1',
            'status' => ExtractionStatus::Completed,
        ];
    }
}
