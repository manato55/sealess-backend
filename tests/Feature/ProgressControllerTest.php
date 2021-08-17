<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\AuthUser;
use Illuminate\Support\Facades\Auth;
use App\Models\Draft;
use Illuminate\Support\Str;



class ProgressControllerTest extends AuthUser
{
   // use DatabaseTransactions;

    public function test_fetchPaginatedTaskInProgress()
    {
        $offset = 1;

        $response = $this->json('GET', "/api/progress/fetch-in-progress/{$offset}");
        $this->assertSame(Auth::user()->id, $response->getData()[0]->user_id);
    }

    public function test_fetchTaskInProgress()
    {
        $response = $this->json('GET', "/api/progress/get-total-length");
        $this->assertSame(Auth::user()->id, $response->getData()[0]->user_id);
    }

    public function test_fetchDetailTask()
    {
        $draft = Draft::where('user_id',Auth::user()->id)
            ->where('approved',false)
            ->first();

        $response = $this->json('GET', "/api/progress/fetch-detail-task/{$draft->id}");
        $this->assertSame(Auth::user()->id, $response->getData()[0]->user_id);
    }

    public function test_fetchFile()
    {
        $draft = Draft::all();
        $request = [
            'data' => [
                'id' => $draft[0]->id,
                'filename' => $draft[0]->filename,
            ],
        ];

        $response = $this->json('POST', "/api/progress/fetch-file", $request);
        // diskにファイルは保存していないため必ずfile not found となる
        $this->assertTrue(Str::startsWith($response->getData()->message, 'File not found at path:'));
    }

    public function test_actionInProgress()
    {
        $draft = Draft::where('user_id',Auth::user()->id)->first();
        $request = [
            'data' => [
                'id' => $draft->id,
                'action' => 'discard',
            ],
        ];

        $response = $this->json('POST', "/api/progress/action-inprogress", $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_fetchRecievedTask()
    {
        $response = $this->json('GET', "/api/progress/fetch-recieved");
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_actionInEscalation()
    {
        $draft = Draft::all();
        $request = [
            'data' => [
                'id' => $draft[0]->id
            ],
        ];

        $response = $this->json('POST', "/api/progress/action-inescalation", $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_returnToDrafter()
    {
        $draft = Draft::all();
        $request = [
            'id' => $draft[0]->id,
            'comment' => 'test',
        ];

        $response = $this->json('POST', "/api/progress/return", $request);
        $this->assertEquals(200, $response->getStatusCode());
    }
}

