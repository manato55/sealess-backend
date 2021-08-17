<?php

namespace Database\Factories;

use App\Models\EmailVerification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;


class EmailVerificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmailVerification::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email' => 'test@test.com',
            'token' => Str::random(50),
            'expired_at' => Carbon::now()->addHours(5),
            'name' => 'test',
            'department' => '経営企画部',
            'section' => '総務・労務課',
            'job_title' => '主任',
        ];
    }
}
