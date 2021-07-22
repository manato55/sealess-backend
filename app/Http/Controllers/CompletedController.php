<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DraftService;
use App\Services\ReturnedService;


class CompletedController extends Controller
{

    public function __construct(
        DraftService $draftSservice,
        ReturnedService $returnedService
    )
    {
        $this->draftService = $draftSservice;
        $this->returnedService = $returnedService;
    }

    public function fetchCompletedTask($choice)
    {
        return $this->draftService->completedTask($choice);
    }

    public function discardTask(Request $request)
    {
        $this->draftService->discardTaskById($request->id);
    }
}
