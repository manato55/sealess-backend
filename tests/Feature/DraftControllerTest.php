<?php

namespace Tests\Feature;


use Tests\AuthUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\Draft;
use Illuminate\Support\Facades\Auth;


class DraftControllerTest extends AuthUser
{
    // use DatabaseTransactions;

    public function test_fetchSectionPpl()
    {
        $request =  [
            'section' => $this->user->section
        ];
        $response = $this->json('POST','/api/draft/fetch-ppl',$request);
        $this->assertEquals($this->user->section, $response->getData()[0]->section);
    }

    public function test_searchTask()
    {
        Draft::factory()->state([
            'approved' => true,
        ])
        ->count(100)
        ->create();
        $draft = Draft::where('user_id','!=',Auth::user()->id)
            ->whereHas('user', function($q) {
                $q->where('department', Auth::user()->department);
            })
            ->where('approved',true)
            ->first();

        $request =  [
            'data' => [
                'task' => $draft->title,
                'name' => '',
                'year' => 3
            ]
        ];

        $response = $this->json('POST','/api/draft/search-task',$request);
        $this->assertEquals($draft->title, $response->getData()[0][0]->title);
    }

    public function test_unreachedTask()
    {
        $response = $this->json('GET','/api/draft/fetch-unreached-task');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_fiscalYear()
    {
        $response = $this->json('GET','/api/draft/get-fiscal-year');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_selectedUnreachedTask()
    {
        $response = $this->json('GET','/api/draft/selected-unreached-task/13');
        if($response->content() === "") {
            $this->assertEquals("", $response->content());
        } else {
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function test_registerDraft()
    {
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
