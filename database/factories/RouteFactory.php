<?php

namespace Database\Factories;

use App\Models\Route;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;


class RouteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Route::class;

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
            'label' => $this->faker->sentence(),
            'route1' => 1,
            'route2' => 2,
            'route3' => 3,
            'route4' => 4,
            'route5' => 5,
        ];
    }
}
