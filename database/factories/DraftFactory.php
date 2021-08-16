<?php

namespace Database\Factories;

use App\Models\Draft;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DraftFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Draft::class;

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
            'title' => $this->faker->sentence(),
            'content' => $this->faker->sentence(),
            'filename' => 'test.pdf',
            'route1' => 4,
            'approved' => $this->faker->randomElement([true, false]),
            'process' => 'route1',
            'is_agent' =>  false
        ];
    }
}
