<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Draft;
use Illuminate\Support\Facades\Auth;
use App\Services\DraftService;
use App\Services\ReturnedService;
use App\Http\Requests\DraftRegisterCheck;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;


class DraftController extends Controller
{

    const MAXIMUM_ROUTE_NUM = 5;

    public function __construct(
        DraftService $draftSservice,
        ReturnedService $returnedService
    )
    {
        $this->draftService = $draftSservice;
        $this->returnedService = $returnedService;
    }

    public function fetchSectionPpl(Request $request)
    {
        return User::where('section', $request->section)
            ->where('id','!=', Auth::user()->id)
            ->get();
    }

    protected function extractFilename($request, $path = null)
    {
        $filenames2DB = '';
        $cnt = 0;
        while(true) {
            $file = 'file'.$cnt;
            if (!isset($request->{$file})) {
                break;
            }
            $filename = $request->{$file}->getClientOriginalName();
            if($path !== null) {
                $request->{$file}->storeAs($path, $filename);
            }
            $filenames2DB .= $filename.',';
            $cnt++;
        }
        $filenames2DB = rtrim($filenames2DB, ',');

        return $filenames2DB;
    }

    public function registerDraft(DraftRegisterCheck $request)
    {
        for($i=0;$i<$this::MAXIMUM_ROUTE_NUM;$i++) {
            if(!isset($request->route[$i])) {
                $eachRoute[$i] = null;
            } else {
                $decoded = json_decode($request->route[$i]);
                $eachRoute[$i] = $decoded->id;
            }
        }

        // 添付ファイルがない場合を想定
        if($request->file0 !== null) {
            $filenames2DB = $this->extractFilename($request);
        } else {
            $filenames2DB = null;
        }

        // タスクの初回提出時もしくは修正提出時かのフラグにactionプロパティで判定
        if($request->action === null) {
            $newDraftRecord = $this->draftService->createRecordInDraft($eachRoute, $request, $filenames2DB);
            $path = 'files/'.Auth::user()->id.'/'.$newDraftRecord->id.'/';
            $this->extractFilename($request,$path);
        } else {
            $existingDraft = Draft::find($request->id);
            if($existingDraft->filename !== null) {
                $modifiedFilenames2DB = $existingDraft->filename.','.$filenames2DB;
            } else {
                $modifiedFilenames2DB = null;
            }

            // 修正内容をDBへ登録、及びreturned_taskテーブルから対象のレコードを削除
            try {
                DB::transaction(function () use ($request, $modifiedFilenames2DB, $eachRoute) {
                    $this->draftService->reSubmitDraft($request->id, $request, $modifiedFilenames2DB, $eachRoute);
                    $this->returnedService->removeReturnedTaskById($request->id);
                });
            } catch (Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['error' => '登録できませんでした。'], 500);
            }

            $path = 'files/'.Auth::user()->id.'/'.$request->id.'/';
            $this->extractFilename($request,$path);
        }
    }

    public function fetchUnreachedTask()
    {
        return $this->draftService->unreachedTask();
    }

    public function fetchSelectedUnreachedTask($id)
    {
        return $this->draftService->selectedUnreachedTask($id);
    }
}
