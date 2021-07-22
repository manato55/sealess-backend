<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DraftService;
use App\Services\RouteService;
use App\Http\Requests\RouteRegister;


class RoutedController extends Controller
{
    public function __construct(
        DraftService $draftSservice,
        RouteService $routeService
    )
    {
        $this->draftService = $draftSservice;
        $this->routeService = $routeService;
    }

    public function registerRoute(RouteRegister $request)
    {
        $ids = [];
        for($i=0;$i<=4;$i++) {
            if(isset($request->data['route'][$i])) {
                $ids[] = $request->data['route'][$i]['id'];
            } else {
                $ids[] = null;
            }
        }
        $this->routeService->createRoute($ids, $request->data['label']);
    }

    public function fetchRegisteredRoute()
    {
        return $this->routeService->registeredRoute();
    }

    public function removeRegisteredRoute(Request $request)
    {
        $this->routeService->removeRoute($request->id);
    }

    public function agentSetting(Request $request)
    {
        $this->routeService->createAgent($request->id);
    }

    public function agentStatus2False()
    {
        $this->routeService->toFalse();
    }

    public function agentStatus2True()
    {
        return $this->routeService->toTrue();
    }

    public function fetchAgentStatus()
    {
        return $this->routeService->agentStatus();
    }
}
