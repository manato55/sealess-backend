<?php

namespace Tests\Feature;

use App\Models\EmailVerification;
use App\Models\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class MethodsBeforeAuthTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() :void
    {
        parent::setUp();
        EmailVerification::factory()->create();
        PasswordReset::factory()->create();
    }

    public function test_login()
    {
        $user = User::factory()->state([
            'password' => Hash::make('testtest')
        ])->create();

        $request = [
            'email' => $user->email,
            'password' => 'testtest',
        ];

        $response = $this->json('POST', "/api/login", $request);
        $this->assertSame($user->email, $response->getData()->user->email);
    }

    public function test_officialRegistryOrdinaryUser()
    {
        $records = EmailVerification::all();

        $request = [
            'token' => $records[0]->token,
            'password' => 'testtest',
        ];

        $response = $this->json('POST', "/api/official-registry", $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_passwordTokenCheck()
    {
        $records = PasswordReset::all();

        $response = $this->json('GET', "/api/password-token-check/{$records[0]->token}");
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_tokenCheck()
    {
        $records = EmailVerification::all();

        $response = $this->json('GET', "/api/token-check/{$records[0]->token}");
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_passwordReRegister()
    {
        $records = PasswordReset::all();
        $request = [
            'token' => $records[0]->token,
            'password' => 'testtest',
        ];

        $response = $this->json('POST', "/api/re-register-password", $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_linkIssuance()
    {
        $records = User::all();
        $request = [
            'email' => $records[0]->email,
        ];

        $response = $this->json('POST', "/api/re-password", $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_register()
    {
        $request = [
            'name' => 'test',
            'email' => 'testtest@test.com',
            'department' => 'test',
            'section' => 'test',
            'jobTitle' => 'test',
            'password' => 'testtest',
        ];

        $response = $this->json('POST', "/api/register", $request);
        $this->assertEquals(201, $response->getStatusCode());
    }
}
