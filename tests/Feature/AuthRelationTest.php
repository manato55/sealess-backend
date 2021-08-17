<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Tests\AuthUser;
use Illuminate\Support\Facades\Hash;


class AuthRelationTest extends AuthUser
{
    public function test_logout()
    {
        $response = $this->json('POST', "/api/logout");
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_me()
    {
        $response = $this->json('GET', '/api/me');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_registerDepAdmin()
    {
        $request = [
            'name' => 'tets',
            'email' => 'test@test.com',
            'password' => Hash::make('testtest'),
            'department' => '経営企画部',
            'user_type' => 1,
        ];

        $response = $this->json('POST', "/api/register-dep-admin", $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_sendRegisterEmail()
    {
        $request = [
            'email' => 'test@test.com',
            'name' => 'test',
            'department' => '経営企画部',
            'section' => '総務・労務課',
            'jobTitle' => '主任',
        ];

        $response = $this->json('POST', "/api/send-register-email", $request);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
