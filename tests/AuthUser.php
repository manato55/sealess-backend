<?php

namespace Tests;

use Tests\TestCase;
use App\Models\User;
use App\Models\Route;
use App\Models\Draft;
use App\Models\ReturnedTask;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthUser extends TestCase
{
    // use RefreshDatabase;

    protected $user;

    public function setUp() :void
    {
        parent::setUp();

        $this->user = User::factory()
            ->has(Draft::factory()->count(3)->has(ReturnedTask::factory()->count(1)))
            ->create();

        Route::factory()->count(5)->create();

        $this->actingAs($this->user);
    }
}
