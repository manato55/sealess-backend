<?php

namespace Database\Factories;

use App\Models\AgentSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgentSettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AgentSetting::class;

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
            'agent_user_id' => mt_rand(1,10),
            'is_enabled' => $this->faker->randomElement([true, false]),
        ];
    }
}
