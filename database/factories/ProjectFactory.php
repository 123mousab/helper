<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    // data  'name', 'description', 'status_id', 'owner_id', 'ticket_prefix',
    //        'status_type', 'type'
    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'status_id' => 1,
            'owner_id' => 1,
            'ticket_prefix' => $this->faker->word,
            'status_type' => 'Pein',
            'type' => 'project',
        ];
    }
}
