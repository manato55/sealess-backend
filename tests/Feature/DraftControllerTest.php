<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\AuthUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


class DraftControllerTest extends AuthUser
{
    use DatabaseTransactions;

    public function test_fertchSectionPpl()
    {
        $this->fetchUser();
        $request =  [
            'section' => '新ソリューション推進課２'
        ];
        $response = $this->json('POST','/api/draft/fetch-ppl',$request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_searchTask()
    {
        $this->fetchUser();
        $request =  [
            'data' => [
                'task' => 'tetwte',
                'name' => '',
                'year' => 3
            ]
        ];
        $response = $this->json('POST','/api/draft/search-task',$request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_unreachedTask()
    {
        $this->fetchUser();
        $response = $this->json('GET','/api/draft/fetch-unreached-task');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_fiscalYear()
    {
        $this->fetchUser();
        $response = $this->json('GET','/api/draft/get-fiscal-year');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_registerDraft()
    {
        $this->fetchUser();
        Storage::fake('file');
        $file = UploadedFile::fake()->image('avatar.pdf');

        $arr = [
            'id' => 7,
            'name' => 't5',
            'email' => 't5@t.com',
        ];

        $request =  [
            'title' => 'test',
            'content' => 'test',
            'route' => [
                0 => json_encode($arr)
            ],
            'file0' => $file
        ];

        Storage::disk('file')->assertMissing($file->hashName());
        $response = $this->json('POST','/api/draft/register-draft',$request);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
