<?php

namespace Tests\Feature;

use Tests\AuthUser;
use App\Models\ReturnedTask;
use App\Models\Draft;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class ReturnedControllerTest extends AuthUser
{
    // use DatabaseTransactions;

    public function test_fertchReturedTask()
    {
        $response = $this->json('GET', '/api/returned/fetch-task');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_fertchReturedTaskDetail()
    {
        $draft = ReturnedTask::first();
        $response = $this->json('GET', "/api/returned/fetch-detail/{$draft->id}");
        if($response->getStatusCode() === 200 || $response->getStatusCode() === 404) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    public function test_removeTask()
    {
        $draft = Draft::first();
        $request = [
            'id' => $draft->id,
        ];
        $response = $this->json('POST', '/api/returned/remove-task',$request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_removeFile()
    {
        $draft = Draft::first();
        $request = [
            'id' => $draft->id,
            'filename' => $draft->filename,
        ];
        $response = $this->json('POST', '/api/returned/remove-file',$request);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
