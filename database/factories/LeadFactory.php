<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'source' => $this->faker->word,
            'owner' => User::factory()->create()->id,
            'created_by' => User::factory()->create()->id,
        ];
    }
}
