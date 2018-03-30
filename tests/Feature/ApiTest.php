<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Model\Users;
use App\Model\Material;
use App\Model\Tags;
use Illuminate\Support\Facades\DB;

class ApiTest extends TestCase
{
    // use RefreshDatabase;
    /**
     *
     * @return void
     */
    public function testMaterialCreate()
    {
        // test no token
        $response = $this->json('POST', '/api/material', [
            'body' => 'test_body',
            'public' => false,
            'tags' => ['a', 'b', 'c'],
        ]);
        $response
            ->assertJson([
                "status" => false,
                "msg" => "need_token",
            ]);
        // test no tags
        $response = $this->json('POST', '/api/material', [
            'body' => 'test_body',
            'public' => false,
            'token' => 'test_token',
        ]);
        $response
            ->assertJson([
                "status" => false,
                "msg" => "need_tags",
            ]);
        // test login failed
        $response = $this->json('POST', '/api/material', [
            'body' => 'test_body',
            'public' => false,
            'tags' => ['a', 'b', 'c'],
            'token' => 'test',
        ]);
        $response
            ->assertJson([
                "status" => false,
                "msg" => "need_login",
            ]);

        // test right data
        $user = factory(Users::class)->create();
        $response = $this->json('POST', '/api/material', [
            'body' => 'test_body',
            'public' => false,
            'tags' => ['a', 'b', 'c'],
            'token' => $user->token,
        ]);
        $results = DB::select(
            'select * from material where body = :body and user_id = :user_id limit 1',
            [
                'body' => 'test_body',
                'user_id' => $user->id,
            ]
        );
        $response
            ->assertJson([
                "status" => true,
                "msg" => "success",
                "data" => $results ? $results[0]->id : null,
            ]);
        // remove test data
        if (isset($results[0])) {
            $mId = $results[0]->id;
            $materialTags = DB::select(
                'select * from material_tag where material_id = :m_id',
                [
                    'm_id' => $mId,
                ]
            );
            if ($materialTags) {
                foreach($materialTags as $v) {
                    DB::delete('delete from tags where id = :id',
                        [ 'id' => $v->tag_id ]);
                    DB::delete('delete from material_tag where id = :id',
                        [ 'id' => $v->id ]);
                }
            }
            DB::delete('delete from material where id = :id',
                [ 'id' => $mId ]);
        }
        DB::delete('delete from users where id = :id',
            [ 'id' => $user->id ]);
    }

    public function testMaterialIndex() {
        // init data 
        $materials = Material::all();
        $tags = $materials[0]->tags;
        $user = $materials[0]->user;
        // test general list
        $response = $this->json('GET', '/api/materials', [
            'tag_id' => $tags[0]->id,
        ]);
        $response->assertJson([
            "status" => true,
            "msg" => "get_data",
            'data' => [
                'current_page' => 1,
                'data' => [
                    [
                        'id' => $materials[0]->id,
                        'user_id' => $materials[0]->id,
                        'title' => $materials[0]->title,
                        'body' => $materials[0]->body,
                        'public' => $materials[0]->public,
                        'status' => $materials[0]->status,
                    ],
                ],
                'first_page_url' => 'http://localhost/api/materials?page=1',
                'from' => 1,
                'last_page' => 1,
                'last_page_url' => 'http://localhost/api/materials?page=1',
                'next_page_url' => null,
                'path' => 'http://localhost/api/materials',
                'per_page' => 15,
                'prev_page_url' => null,
                'to' => 1,
                'total' => 1,
            ],
        ]);
        // test user list
        $response = $this->json('GET', '/api/materials', [
            'token' => $user->token,
        ]);
        $response->assertJson([
            "status" => true,
            "msg" => "get_data",
            'data' => [
                'current_page' => 1,
                'data' => [
                    [
                        'id' => $materials[0]->id,
                        'user_id' => $materials[0]->id,
                        'title' => $materials[0]->title,
                        'body' => $materials[0]->body,
                        'public' => $materials[0]->public,
                        'status' => $materials[0]->status,
                    ],
                ],
                'first_page_url' => 'http://localhost/api/materials?page=1',
                'from' => 1,
                'last_page' => 1,
                'last_page_url' => 'http://localhost/api/materials?page=1',
                'next_page_url' => null,
                'path' => 'http://localhost/api/materials',
                'per_page' => 15,
                'prev_page_url' => null,
                'to' => 1,
                'total' => 1,
            ],
        ]);
    }

    public function testMaterialDetail() {
        // init data
        $users = Users::all();
        $privateMaterial = [];
        $publicMaterial = [];
        foreach($users as $user) {
            $materials = $user->materials;
            if ($materials[0]->public == false) {
                $privateMaterial['user'] = $user;
                $privateMaterial['material'] = $materials[0];
            } else {
                $publicMaterial['user'] = $user;
                $publicMaterial['material'] = $materials[0];
            }
        }
        // test public material detail
        $response = $this->json('GET', '/api/material/detail', [
            'm_id' => $publicMaterial['material']->id,
        ]);
        $response->assertJson([
            "status" => true,
            "msg" => "success",
            'data' => $publicMaterial['material']->toArray(),
        ]);
        // test private material detail - no auth
        $response = $this->json('GET', '/api/material/detail', [
            'm_id' => $privateMaterial['material']->id,
        ]);
        $response->assertJson([
            "status" => false,
            "msg" => "no_auth",
        ]);
        // test private material detail - success
        $response = $this->json('GET', '/api/material/detail', [
            'm_id' => $privateMaterial['material']->id,
            'token' => $privateMaterial['user']->token,
        ]);
        $response->assertJson([
            "status" => true,
            "msg" => "success",
            "data" => $privateMaterial['material']->toArray(),
        ]);
    }
}
