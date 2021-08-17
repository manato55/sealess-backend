<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DraftService;
use App\Services\RouteService;
use App\Http\Requests\RouteRegister;


class RoutedController extends Controller
{
    const ROUTE_NUMBER = 5;

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
        for($i=0;$i<self::ROUTE_NUMBER;$i++) {
            $ids[] = isset($request->data['route'][$i]) ? $request->data['route'][$i]['id']: null;
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
        return $this->routeService->toFalse();
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
