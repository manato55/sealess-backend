<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DraftService;
use App\Services\ReturnedService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;


class ReturnedController extends Controller
{
    public function __construct(
        DraftService $draftService,
        ReturnedService $returnedService
    )
    {
        $this->draftService = $draftService;
        $this->returnedService = $returnedService;
    }

    public function fetchReturnedTask()
    {
        return $this->returnedService->returnedTask();
    }

    public function fetchReturnedDetail($id)
    {
        return $this->returnedService->returnedDetail($id);
    }

    public function removeFile(Request $request)
    {
        // ローカルdiskにあるfileの削除
        Storage::delete('files/'.Auth::user()->id.'/'.$request->id.'/'.$request->filename);
        $this->returnedService->removeFileFromRecord($request);
    }

    public function removeTask(Request $request)
    {
        $this->draftService->removeDraftById($request->id);
    }

}
