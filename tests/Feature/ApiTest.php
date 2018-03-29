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
     * A basic test example.
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
}
