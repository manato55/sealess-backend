<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Draft;
use App\Models\User;
use App\Services\DraftService;
use App\Services\ProgressService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ReturnCheck;




class ProgressController extends Controller
{

    public function __construct(
        DraftService $draftService,
        ProgressService $progressService
    )
    {
        $this->draftService = $draftService;
        $this->progressService = $progressService;
    }

    public function fetchTaskInProgress()
    {
        return $this->progressService->taskInProgress();
    }

    public function fetchPaginatedTaskInProgress($offset)
    {
        return $this->progressService->paginatedTask($offset);

    }

    public function fetchDetailTask($id)
    {
        return $this->progressService->detailTask($id);
    }

    public function fetchFile(Request $request)
    {
        $filepath = 'files/'.Auth::user()->id.'/'.$request->data['id'].'/'.$request->data['filename'];
        $file =  Storage::get($filepath);
        $mimeType = Storage::mimeType($filepath);
        $headers = array(
            'Content-type' => $mimeType,
            'Content-Disposition' => 'attachment; filename=' . $request->data['filename'],
            'Access-Control-Expose-Headers' => 'Content-Disposition',
        );

        return response()->make($file, 200, $headers);
    }

    public function actionInProgress(Request $request)
    {
        if($request->data['action'] === 'discard') {
            $task = $this->progressService->findTaskById($request->data['id']);
            $filenames = explode(',', $task->filename);
            // diskにあるファイルの削除
            foreach($filenames as $filename) {
                Storage::delete('files/'.Auth::user()->id.'/'.$filename);
            }
            $this->progressService->discardTask($request->data['id']);
        } else {
            try {
                DB::transaction(function () use ($request) {
                    $this->progressService->returnedByMyself($request->data['id']);
                    $this->progressService->retrieveTaskByMyself($request->data['id']);
                });
            } catch (Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['error' => '登録できませんでした。'], 500);
            }

        }
    }

    public function fetchRecievedTask()
    {
        return $this->progressService->recievedTask();
    }

    public function actionInEscalation(Request $request)
    {
        $task = $this->progressService->findTaskById($request->data['id']);

        $routeNumber = mb_substr($task->process, -1);
        $number = (int)$routeNumber+1;
        // 次のルート番号
        $nextRoute = 'route'.$number;
        // 次のルートに関与者がいるか判定
        // 次の承認者がnullの場合もしくは最後の関与者の場合
        if($task->{$nextRoute} === null || $nextRoute === 'route6') {
            $task->fill(['approved' => true])->save();
        } else {
            $task->fill(['process' => $nextRoute])->save();
        }
    }

    public function returnToDrafter(ReturnCheck $request)
    {
        $task = $this->progressService->findTaskById($request->id);

        try {
            DB::transaction(function () use ($request, $task) {
                $task->fill(['intercepted' => $task->process])->save();
                $this->progressService->createReturnedTaskRecord($request->id, $request->comment);
            });
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => '登録できませんでした。'], 500);
        }
    }

}
