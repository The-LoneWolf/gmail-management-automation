<?php

namespace Database\Factories;

use App\Models\ExtractionTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExtractionTemplate>
 */
class ExtractionTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => 'Invoice extraction',
            'slug' => 'invoice-extraction-'.fake()->unique()->numberBetween(100, 999),
            'description' => fake()->sentence(),
            'schema' => [
                'fields' => [
                    'invoice_number' => ['type' => 'string', 'pattern' => 'invoice\\s*#?\\s*([A-Z0-9-]+)'],
                    'amount' => ['type' => 'money'],
                    'sender_email' => ['type' => 'email'],
                ],
            ],
            'instructions' => 'Extract invoice data from the message.',
            'output_format' => 'json',
            'is_active' => true,
        ];
    }
}
