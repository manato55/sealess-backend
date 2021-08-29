<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\Draft;
use App\Models\ReturnedTask;
use Illuminate\Support\Facades\DB;


class ProgressService
{

    public function taskInProgress()
    {
        return Draft::where('user_id', Auth::user()->id)
            ->where('approved', false)
            ->where('intercepted', null)
            ->with('route1User','route2User','route3User','route4User','route5User')
            ->get();
    }

    public function detailTask($id)
    {
        return Draft::where('id',$id)
            ->where('user_id',Auth::user()->id)
            ->with(
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
            ->get();
    }

    public function discardTask($id)
    {
        return Draft::destroy($id);
    }

    public function retrieveTaskByMyself($id)
    {
        return Draft::find($id)->fill([
                'intercepted' => 'route0'
            ])->save();
    }

    public function findTaskById($id)
    {
        return Draft::find($id);
    }

    public function recievedTask()
    {
        $tasks = Draft::where(function($q) {
                for($i=1;$i<=config('const.ROUTE_NUM');$i++) {
                    $q->orWhere('route'.$i, Auth::user()->id);
                }
            })
            ->where('approved', false)
            ->where('intercepted', null)
            ->with('user','route1User','route2User','route3User','route4User','route5User','agent_statuses.user')
            ->orderBy('updated_at','desc')
            ->get();

        $extractedTasks = [];

        foreach($tasks as $task) {
            $process = $task->process;
            if($task->{$process} === Auth::user()->id) {
                $extractedTasks[] = $task;
            }
        }

        return $extractedTasks;
    }

    public function createReturnedTaskRecord($id, $comment)
    {
        return ReturnedTask::create([
                'user_id' => Auth::user()->id,
                'draft_id' => $id,
                'comment' => $comment,
            ]);
    }

    public function returnedByMyself($id)
    {
        ReturnedTask::create([
            'user_id' => Auth::user()->id,
            'draft_id' => $id,
        ]);
    }

    public function paginatedTask($offset)
    {
        $editedOffset = $offset*3 - 3;

        return Draft::offset($editedOffset)
            ->limit(3)
            ->orderBy('created_at', 'desc')
            ->where('approved', false)
            ->where('user_id', Auth::user()->id)
            ->where('intercepted', null)
            ->with('route1User','route2User','route3User','route4User','route5User')
            ->get();

    }

}
