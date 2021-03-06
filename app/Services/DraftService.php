<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\Draft;
use App\Models\AgentSetting;
use App\Models\AgentStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;


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

    public function sectionPpl($id)
    {
        return User::where('section_id', $id)
            ->where('id','!=', Auth::user()->id)
            ->with('section','department')
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
        // ??????,???????????????????????????????????????
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

        $draft = Draft::find($id);
        // ?????????????????????????????????????????????????????????process?????????????????????????????????
        $process = $request->action === 'reSubmit' ? 'route1': $draft->process;

        $draft->fill([
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
            'process' => $process,
        ])
        ->save();
    }

    public function removeDraftById($id)
    {
        $draft = Draft::where('user_id',Auth::user()->id)
            ->where('approved', true)
            ->where('id', $id);

        return $draft->delete();
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
        // ?????????????????????????????????????????????
        return Draft::where('id',$id)
            ->whereHas('user',function($q) {
                $q->where('department_id', Auth::user()->department_id);
            })
            ->withTrashed()
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

    public function discardTaskById($id)
    {
        return Draft::destroy($id);
    }

    private function completedTaskSortByIndividual()
    {
        return Draft::where('user_id',Auth::user()->id)
            ->where('approved',true)
            ->with(
                'user',
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
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    private function completedTaskSortByDep()
    {
        return Draft::where('approved',true)
            ->whereHas('user', function($q) {
                $q->where('department_id', Auth::user()->department_id);
            })
            ->withTrashed()
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
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    private function completedTaskSortBySec()
    {
        return Draft::where('approved',true)
            ->whereHas('user', function($q) {
                $q->where('section_id', Auth::user()->section_id);
            })
            ->withTrashed()
            ->with(
                'user',
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
                )
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function unreachedTask()
    {
        // ?????????????????????????????????????????????????????????????????????
        $tasksInProgress = Draft::where(function($q) {
                for($i=1;$i<=config('const.ROUTE_NUM');$i++) {
                    $q->orWhere('route'.$i, Auth::user()->id);
                }
            })
            ->where('approved', false)
            ->where('intercepted', null)
            ->with(
                'user',
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
            ->orderBy('updated_at','desc')
            ->get();

        $unreachedTasks = [];
        // ???????????????????????????????????????????????????
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
        // ????????????????????????????????????????????????NULL?????????
        return Draft::where('id',$id)
            ->where(function($q) {
                for($i=1;$i<=config('const.ROUTE_NUM');$i++) {
                    $q->orWhere('route'.$i, Auth::user()->id);
                }
            })
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
            ->first();
    }

    public function search($request, $startYear, $endYear)
    {
        $editedOffset = $request->offset*3 - 3;

        // ????????????????????????????????????
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
            // ???????????????????????????????????????????????????
            ->whereHas('user', function($q) {
                $q->where('department_id',Auth::user()->department_id);
            })
            ->where('approved',true)
            ->limit(3)
            ->offset($editedOffset)
            ->orderBy('updated_at', 'desc')
            ->with('user')
            ->get();

        // ????????????
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
            // ???????????????????????????????????????????????????
            ->whereHas('user', function($q) {
                $q->where('department_id',Auth::user()->department_id);
            })
            ->where('approved',true)
            ->get();

        return [$results,$length];
    }

}
