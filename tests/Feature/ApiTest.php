<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Model\Users;
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
        // create test data
        $users = $this->createTestData();
        // init data 
        $tag = DB::select('select * from tags limit 1');
        $materialTag = DB::select(
            'select * from material_tag where tag_id = :tag_id limit 1',
            [
                'tag_id' => $tag[0]->id,
            ]
        );
        $user = DB::select(
            'select * from users where id = :id limit 1',
            [
                'id' => $materialTag[0]->user_id,
            ]
        );
        $material = DB::select(
            'select * from material where id = :id limit 1',
            [
                'id' => $materialTag[0]->material_id,
            ]
        );
        // test general list
        $response = $this->json('GET', '/api/materials', [
            'tag_id' => $tag[0]->id,
        ]);
        $response->assertJson([
            "status" => true,
            "msg" => "get_data",
            'data' => [
                'current_page' => 1,
                'data' => [
                    [
                        'id' => $material[0]->id,
                        'user_id' => $user[0]->id,
                        'title' => $material[0]->title,
                        'body' => $material[0]->body,
                        'public' => $material[0]->public,
                        'status' => $material[0]->status,
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
            'token' => $user[0]->token,
        ]);
        $response->assertJson([
            "status" => true,
            "msg" => "get_data",
            'data' => [
                'current_page' => 1,
                'data' => [
                    [
                        'id' => $material[0]->id,
                        'user_id' => $user[0]->id,
                        'title' => $material[0]->title,
                        'body' => $material[0]->body,
                        'public' => $material[0]->public,
                        'status' => $material[0]->status,
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
        // remove test data
        $this->removeTestData($users);
    }

    private function createTestData() {
        $users = factory(Users::class, 2)->create();
        $response = [];
        foreach($users as $user) {
            $tmpUserId = $user->id;
            $response[$tmpUserId] = $this->json('POST', '/api/material', [
                'body' => 'test_body_'.$tmpUserId,
                'public' => true,
                'tags' => ['a_'.$tmpUserId, 'b_'.$tmpUserId, 'c_'.$tmpUserId],
                'token' => $user->token,
            ]);
        }
        return $users;
    }

    private function removeTestData($users) {
        foreach($users as $user) {
            $tmpUserId = $user->id;
            $results = DB::select(
                'select * from material where body = :body and user_id = :user_id limit 1',
                [
                    'body' => 'test_body_'.$tmpUserId,
                    'user_id' => $user->id,
                ]
            );
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
    }
}
