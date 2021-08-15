<?php

namespace Tests;

use Tests\TestCase;
use App\Models\User;

class AuthUser extends TestCase
{

    public function fetchUser()
    {
        $user = User::find(12);
        return $this->actingAs($user);
    }
}
