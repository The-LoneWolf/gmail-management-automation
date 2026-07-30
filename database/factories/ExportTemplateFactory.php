<?php

namespace Database\Factories;

use App\Models\ExportTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExportTemplate>
 */
class ExportTemplateFactory extends Factory
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
            'name' => 'Inbox CSV',
            'format' => 'csv',
            'filters' => [],
            'columns' => [
                ['label' => 'Received At', 'source' => 'email.received_at'],
                ['label' => 'Sender', 'source' => 'email.sender_email'],
                ['label' => 'Subject', 'source' => 'email.subject'],
            ],
            'is_active' => true,
        ];
    }
}
