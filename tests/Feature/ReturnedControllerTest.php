<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\AuthUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;



class ReturnedControllerTest extends AuthUser
{
    use DatabaseTransactions;

    public function test_fertchReturedTask()
    {
        $this->fetchUser();
        $responsePost = $this->json('GET', '/api/returned/fetch-task');
        $this->assertEquals(200, $responsePost->getStatusCode());
    }

    public function test_fertchReturedTaskDetail()
    {
        $this->fetchUser();
        $responsePost = $this->json('GET', '/api/returned/fetch-detail/34');
        $this->assertEquals(404, $responsePost->getStatusCode());
    }

    public function test_removeTask()
    {
        $this->fetchUser();
        $request = [
            'id' => 35,
        ];
        $responsePost = $this->json('POST', '/api/returned/remove-task',$request);
        $this->assertEquals(200, $responsePost->getStatusCode());
    }

    public function test_removeFile()
    {
        $this->fetchUser();
        $request = [
            'id' => 12,
            'filename' => 'test'
        ];
        $responsePost = $this->json('POST', '/api/returned/remove-file',$request);
        $this->assertEquals(200, $responsePost->getStatusCode());
    }
}
