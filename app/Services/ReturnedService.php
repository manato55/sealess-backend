<?php

namespace App\Services;

use App\Http\Requests\ReturnCheck;
use Illuminate\Support\Facades\Auth;
use App\Models\Draft;
use App\Models\ReturnedTask;

class ReturnedService
{

    public function returnedTask()
    {
        return Draft::where('user_id', Auth::user()->id)
            ->where('intercepted','!=', null)
            ->with('returnedTask', 'returnedTask.user')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function returnedDetail($id)
    {
        $taskDetail = Draft::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->with(
                'returnedTask',
                'route1User',
                'route1User.department',
                'route1User.section',
                'route2User',
                'route2User.department',
                'route2User.section',
                'route3User',
                'route3User.department',
                'route3User.section',
                'route4User',
                'route4User.department',
                'route4User.section',
                'route5User',
                'route5User.department',
                'route5User.section',
                'agent_statuses.user',
                'agent_statuses.user.department',
                'agent_statuses.user.section'
                )
            ->first();

        if($taskDetail === null) {
            return response()->json('error', 404);
        } else {
            return $taskDetail;
        }
    }

    public function removeFileFromRecord($request)
    {
        $existingDraft = Draft::find($request->id);
        $filenames = explode(',',$existingDraft->filename);
        $key = array_search($request->filename, $filenames);
        unset($filenames[$key]);
        $upadtedFilenames = array_values($filenames);

        $newFilename = '';
        foreach($upadtedFilenames as $file) {
            $newFilename .= $file.',';
        }
        $newFilename = rtrim($newFilename, ',');

        $existingDraft->fill(['filename' => $newFilename])->save();
    }

    public function removeReturnedTaskById($id)
    {
        ReturnedTask::where('draft_id',$id)->delete();
    }

}
