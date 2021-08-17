<?php

namespace Tests\Feature;

use Tests\AuthUser;
use App\Models\Draft;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;



class CompletedControllerTest extends AuthUser
{
    // use DatabaseTransactions;

    public function test_completedTask()
    {
        $draft = Draft::all();
        foreach($draft as $v) {
            $ids[] = $v->id;
        }
        $id = $ids[array_rand($ids)];

        $request = [
            'id' => $id,
        ];

        $response = $this->json('POST', '/api/completed/discard-task',$request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_fetchCompletedTask()
    {
        $choice = ['dep','sec'];

        // 部を選択
        $response = $this->json('GET', "/api/completed/fetch-task/{$choice[0]}");
        $this->assertEquals(true, $response->getData()[0]->approved);
        // 課を選択
        $response = $this->json('GET', "/api/completed/fetch-task/{$choice[1]}");
        $this->assertEquals(true, $response->getData()[0]->approved);
    }

    public function test_fetchCompletedTaskDetail()
    {
        $draft = Draft::where('approved',true)
            ->whereHas('user',function($q) {
                $q->where('department', Auth::user()->department);
            })
            ->first();

        $response = $this->json('GET', "/api/completed/fetch-detail-task/{$draft->id}");
        $this->assertEquals(true, $response->getData()[0]->approved);
    }
}
