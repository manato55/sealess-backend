<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\AuthUser;
use App\Models\User;
use App\Models\Route;
use App\Models\AgentSetting;


class RouteControllerTest extends AuthUser
{
    // use DatabaseTransactions;

    public function test_registerRoute()
    {
        $request = [
            'data' => [
                'user_id' => Auth::user()->id,
                'label' => 'test',
                'route' => [
                    0 => [
                        'id' => 10
                    ],
                    1 => [
                        'id' => 11
                    ],
                    2 => [
                        'id' => 12
                    ],
                    3 => [
                        'id' => 13
                    ],
                    4 => [
                        'id' => 14
                    ],
                ]
            ]
        ];

        $response = $this->json('POST', '/api/route/register-route', $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_removeRegieteredRoute()
    {
        $route = Route::first();

        $request = [
            'id' => $route->id,
        ];

        $response = $this->json('POST', '/api/route/remove-registered-route', $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_agentSetting()
    {
        $request = [
            'id' => 1,
        ];

        $response = $this->json('POST', '/api/route/agent-setting', $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_agentSetting2False()
    {
        $exstingRoute = AgentSetting::where('user_id',Auth::user()->id)->first();
        if($exstingRoute === null) {
            AgentSetting::create([
                'user_id' => Auth::user()->id,
                'agent_user_id' => 5,
                'is_enabled' => true,
            ]);
        } else {
            $exstingRoute->is_enabled = true;
            $exstingRoute->save();
        }

        $response = $this->json('POST', '/api/route/agent-status-2false');
        $this->assertSame(false, $response->getData()->is_enabled);
    }

    public function test_agentSetting2True()
    {
        $exstingRoute = AgentSetting::where('user_id',Auth::user()->id)->first();
        if($exstingRoute === null) {
            AgentSetting::create([
                'user_id' => Auth::user()->id,
                'agent_user_id' => 5,
                'is_enabled' => false,
            ]);
        } else {
            $exstingRoute->is_enabled = false;
            $exstingRoute->save();
        }

        $response = $this->json('POST', '/api/route/agent-status-2true');
        $this->assertSame(true, $response->getData()->is_enabled);
    }

    public function test_fetchRegistered()
    {
        $response = $this->json('GET', '/api/route/fetch-registered');
        $this->assertSame(Auth::user()->id, $response->getData()[0]->user_id);
    }

    public function test_fetchAgentStatus()
    {
        $response = $this->json('GET', '/api/route/fetch-agent-status');
        $this->assertSame(Auth::user()->id, $response->getData()->user_id);
    }
}
