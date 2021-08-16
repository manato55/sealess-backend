<?php

namespace Database\Factories;

use App\Models\ReturnedTask;
use App\Models\User;
use App\Models\Draft;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReturnedTaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReturnedTask::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => function() {
                return User::factory()->create()->id;
            },
            'draft_id' => function() {
                return Draft::factory()->create()->id;
            },
            'comment' => $this->faker->sentence(),
        ];
    }
}
