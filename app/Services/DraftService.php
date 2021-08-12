<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\Draft;
use App\Models\AgentSetting;
use App\Models\AgentStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DraftService
{

    private function getAgentStatus(array $route)
    {
        return AgentSetting::where(function($q) use($route) {
            for($i=0;$i<config('const.ROUTE_NUM');$i++) {
                    $q->orWhere('user_id', $route[$i]);
                }
            })
            ->where('is_enabled', true)
            ->get();
    }

    public function createRecordInDraft(array $route, $request, $filename)
    {
        $agent = $this->getAgentStatus($route);

        $tmpAgentStatus = [];
        if(count($agent) > 0) {
            $isAgent = true;
            foreach($agent as $v) {
                for($i=0;$i<config('const.ROUTE_NUM');$i++) {
                    if($v->user_id == $route[$i]) {
                        $routeNum = $i+1;
                        $route[$i] = $v->agent_user_id;
                        $tmpAgentStatus[] = [
                            'original_user' => $v->user_id,
                            'agent_user' =>  $route[$i],
                            'route' => 'route'.$routeNum,
                        ];
                    }
                }
            }
        } else {
            $isAgent = false;
        }

        $newDraft = Draft::create([
            'user_id' => Auth::user()->id,
            'title' => $request->title,
            'content' => $request->content,
            'filename' => $filename,
            'route1' =>$route[0],
            'route2' =>$route[1],
            'route3' =>$route[2],
            'route4' =>$route[3],
            'route5' =>$route[4],
            'is_agent' => $isAgent,
        ]);

        if(count($tmpAgentStatus) > 0) {
            foreach($tmpAgentStatus as $agent) {
                $agent['draft_id'] = $newDraft->id;
                AgentStatus::create($agent);
            }
        }

        return $newDraft;
    }

    public function reSubmitDraft($id, $request, $filename, $route)
    {
        // 一度代理人設定の情報を消去する
        AgentStatus::where('draft_id',$id)->delete();

        $agent = $this->getAgentStatus($route);

        if(count($agent) > 0) {
            $isAgent = true;
            foreach($agent as $v) {
                for($i=0;$i<config('const.ROUTE_NUM');$i++) {
                    if($v->user_id == $route[$i]) {
                        $routeNum = $i+1;
                        $route[$i] = $v->agent_user_id;
                        AgentStatus::create([
                            'draft_id' => $id,
                            'original_user' => $v->user_id,
                            'agent_user' =>  $route[$i],
                            'route' => 'route'.$routeNum,
                        ]);
                    }
                }
            }
        } else {
            $isAgent = false;
        }

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
            'is_agent' => $isAgent,
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

    public function completedTaskDetail($id)
    {
        // 同じ部門に所属していれば閲覧可
        return Draft::where('id',$id)
            ->whereHas('user',function($q) {
                $q->where('department', Auth::user()->department);
            })
            ->with('route1User','route2User','route3User','route4User','route5User','agent_statuses.user')
            ->get();
    }

    public function discardTaskById($id)
    {
        Draft::destroy($id);
    }

    private function completedTaskSortByIndividual()
    {
        return Draft::where('user_id',Auth::user()->id)
            ->where('approved',true)
            ->with('user','route1User','route2User','route3User','route4User','route5User')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    private function completedTaskSortByDep()
    {
        return Draft::where('approved',true)
            ->whereHas('user', function($q) {
                $q->where('department', Auth::user()->department);
            })
            ->with('user','route1User','route2User','route3User','route4User','route5User')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    private function completedTaskSortBySec()
    {
        return Draft::where('approved',true)
            ->whereHas('user', function($q) {
                $q->where('section', Auth::user()->section);
            })
            ->with('user','route1User','route2User','route3User','route4User','route5User')
            ->orderBy('updated_at', 'desc')
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
            ->with('route1User','route2User','route3User','route4User','route5User','agent_statuses.user')
            ->first();
    }

    public function search($request, $startYear, $endYear)
    {
        $editedOffset = $request->offset*3 - 3;

        // 個別ページ用のレスポンス
        $results = Draft::where(function($q) use($request,$startYear,$endYear) {
                if($request->data['name'] !== null) {
                    $q->whereHas('user', function($qq) use($request) {
                        $qq->where('name', $request->data['name']);
                    });
                }
                if($request->data['task'] !== null) {
                    $q->where('title',$request->data['task']);
                }
                if(isset($request->data['year'])) {
                    $q->where('updated_at','>=', new Carbon($startYear))
                    ->where('updated_at','<=', new Carbon($endYear));
                }
            })
            // 同じ部に属してい人の案件のみ取得可
            ->whereHas('user', function($q) {
                $q->where('department',Auth::user()->department);
            })
            ->where('approved',true)
            ->limit(3)
            ->offset($editedOffset)
            ->orderBy('updated_at', 'desc')
            ->with('user')
            ->get();

        // 全件取得
        $length = Draft::where(function($q) use($request,$startYear,$endYear) {
                if($request->data['name'] !== null) {
                    $q->whereHas('user', function($qq) use($request) {
                        $qq->where('name', $request->data['name']);
                    });
                }
                if($request->data['task'] !== null) {
                    $q->where('title',$request->data['task']);
                }
                if(isset($request->data['year'])) {
                    $q->where('updated_at','>=', new Carbon($startYear))
                    ->where('updated_at','<=', new Carbon($endYear));
                }
            })
            // 同じ部に属してい人の案件のみ取得可
            ->whereHas('user', function($q) {
                $q->where('department',Auth::user()->department);
            })
            ->where('approved',true)
            ->get();

        return [$results,$length];
    }

}
