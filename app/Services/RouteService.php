<?php

namespace App\Services;

use App\Models\AgentSetting;
use Illuminate\Support\Facades\Auth;
use App\Models\Route;
use Illuminate\Support\Facades\DB;


class RouteService
{

    public function createRoute(array $route, $label)
    {
        return Route::create([
            'user_id' => Auth::user()->id,
            'label' => $label,
            'route1' =>$route[0],
            'route2' =>$route[1],
            'route3' =>$route[2],
            'route4' =>$route[3],
            'route5' =>$route[4],
        ]);
    }

    public function registeredRoute()
    {
        return Route::where('user_id',Auth::user()->id)
            ->with('route1User','route2User','route3User','route4User','route5User')
            ->get();
    }

    public function removeRoute($id)
    {
        return Route::destroy($id);
    }

    public function createAgent($id)
    {
        $existingAgentSetting = AgentSetting::where('user_id', Auth::user()->id)->first();
        // 既に代理設定をしているか
        if($existingAgentSetting === null) {
            AgentSetting::create([
                'user_id' => Auth::user()->id,
                'agent_user_id' => $id,
                'is_enabled' => true,
            ]);
        } else {
            $existingAgentSetting->agent_user_id = $id;
            $existingAgentSetting->save();
        }
    }

    public function toFalse()
    {
        $existingAgentSetting = AgentSetting::where('user_id', Auth::user()->id)->first();
        // 代理設定をせずにOFFにする場合
        if($existingAgentSetting === null) {
            return;
        }
        $existingAgentSetting->is_enabled = false;
        $existingAgentSetting->save();

        return $existingAgentSetting;
    }

    public function agentStatus()
    {
        return AgentSetting::where('user_id', Auth::user()->id)
            ->with('agent_user')
            ->first();
    }

    public function toTrue()
    {
        $existingAgentSetting = AgentSetting::where('user_id', Auth::user()->id)
            ->with('agent_user')
            ->first();
        // 代理設定をしていない場合
        if($existingAgentSetting === null) {
            return;
        }
        $existingAgentSetting->is_enabled = true;
        $existingAgentSetting->save();

        return $existingAgentSetting;
    }


}
