<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Draft;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\DraftService;
use App\Services\ReturnedService;
use App\Http\Requests\DraftRegisterCheck;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Enums\FiscalYear;


class DraftController extends Controller
{
    const MAXIMUM_ROUTE_NUM = 5;
    const LAUNCHED_JAP_YEAR = 1;
    const YEAR_BEFORE_NEW_ERA = 2018;
    const SEARCH_INDEX = ['task','name'];

    public function __construct(
        DraftService $draftSservice,
        ReturnedService $returnedService
    )
    {
        $this->draftService = $draftSservice;
        $this->returnedService = $returnedService;
    }

    public function fetchSectionPpl($id)
    {
        return $this->draftService->sectionPpl($id);
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
        $filenames2DB = $request->file0 !== null ? $this->extractFilename($request): null;

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
        return $path;
    }

    public function fetchUnreachedTask()
    {
        return $this->draftService->unreachedTask();
    }

    public function fetchSelectedUnreachedTask($id)
    {
        return $this->draftService->selectedUnreachedTask($id);
    }

    public function getFiscalYear()
    {
        $now = substr(Carbon::now(),0,7);
        $rep = str_replace('-','',$now);
        $maxYear = FiscalYear::getJapCalender($rep);

        for($i=self::LAUNCHED_JAP_YEAR;$i<=$maxYear;$i++) {
            $japYearRange[] = $i;
        }

        return $japYearRange;
    }

    public function searchTask(Request $request)
    {
        $cnt = 0;
        for($i=0;$i<count(self::SEARCH_INDEX);$i++) {
            $index = self::SEARCH_INDEX[$i];
            if($request->data[$index] === null) {
                $cnt++;
            }
        }
        if(!isset($request->data['year'])) {
            $cnt++;
        }
        if(count(self::SEARCH_INDEX)+1 === $cnt) {
            return response()->json([
                'error' => '最低一つの項目は入力してください。'
            ], 422);
        }
        if(isset($request->data['year'])) {
            $year = $request->data['year'] + self::YEAR_BEFORE_NEW_ERA;
            $startYear = $year.'-04-01';
            $endMonth = $year + 1;
            $endYear =  $endMonth.'-04-01';
        } else {
            $startYear = null;
            $endYear = null;
        }

        return $this->draftService->search($request,$startYear,$endYear);
    }
}
