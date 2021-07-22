<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\Draft;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use OutOfRangeException;
use PhpParser\Node\Stmt\Break_;

class DraftService
{

    public function createRecordInDraft(array $route, $request, $filename)
    {
        return Draft::create([
            'user_id' => Auth::user()->id,
            'title' => $request->title,
            'content' => $request->content,
            'filename' => $filename,
            'route1' =>$route[0],
            'route2' =>$route[1],
            'route3' =>$route[2],
            'route4' =>$route[3],
            'route5' =>$route[4],
        ]);
    }

    public function reSubmitDraft($id, $request, $filename, $route)
    {
        Draft::find($id)->fill([
            'title' => $request->title,
            'content' => $request->content,
            'filename' => $filename,
            'intercepted' => null,
            'route1' =>$route[0],
            'route2' =>$route[1],
            'route3' =>$route[2],
            'route4' =>$route[3],
            'route5' =>$route[4],
        ])
        ->save();
    }

    public function removeDraftById($id)
    {
        Draft::destroy($id);
    }

    public function completedTask($choice)
    {
        switch($choice) {
            case('dep'):
                return $this->completedTaskSortByDep();
                break;
            case('sec'):
                return $this->completedTaskSortBySec();
                break;
            default:
                return $this->completedTaskSortByIndividual();
        }
    }

    public function discardTaskById($id)
    {
        Draft::destroy($id);
    }

    private function completedTaskSortByIndividual()
    {
        return Draft::where('user_id',Auth::user()->id)
            ->where('approved',true)
            ->with('route1User','route2User','route3User','route4User','route5User')
            ->get();
    }

    private function completedTaskSortByDep()
    {
        return Draft::where('approved',true)
            ->whereHas('user', function($q) {
                $q->where('department', Auth::user()->department);
            })
            ->with('user','route1User','route2User','route3User','route4User','route5User')
            ->get();
    }

    private function completedTaskSortBySec()
    {
        return Draft::where('approved',true)
            ->whereHas('user', function($q) {
                $q->where('section', Auth::user()->section);
            })
            ->with('user','route1User','route2User','route3User','route4User','route5User')
            ->get();
    }

    public function unreachedTask()
    {
        // 自分がルートに入っている現在進行中の案件を取得
        $tasksInProgress = Draft::where(function($q) {
                for($i=1;$i<=config('const.ROUTE_NUM');$i++) {
                    $q->orWhere('route'.$i, Auth::user()->id);
                }
            })
            ->where('approved', false)
            ->where('intercepted', null)
            ->with('user','route1User','route2User','route3User','route4User','route5User')
            ->orderBy('updated_at','desc')
            ->get();

        $unreachedTasks = [];
        // 自分にまだ回付されてない案件を抽出
        foreach($tasksInProgress as $task) {
            for($i=1;$i<=config('const.ROUTE_NUM');$i++) {
                $route = 'route'.$i;
                $processNumber = substr($task->process, -1);
                if($task->{$route} === Auth::user()->id && $i > (int)$processNumber) {
                    $unreachedTasks[] = $task;
                }
            }
        }
        return $unreachedTasks;
    }

    public function selectedUnreachedTask($id)
    {
        // パラメータ直打ちの場合は返り値にNULLが入る
        return Draft::where('id',$id)
            ->where(function($q) {
                for($i=1;$i<=config('const.ROUTE_NUM');$i++) {
                    $q->orWhere('route'.$i, Auth::user()->id);
                }
            })
            ->with('route1User','route2User','route3User','route4User','route5User')
            ->first();
    }

}
