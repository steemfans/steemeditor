<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Users;
use App\Model\Material;
use App\Model\MaterialTags;

class MaterialController extends Controller
{
    public function create(Request $request) {
        $title = $request->input('title');
        $body = $request->input('body');
        $public = $request->input('public');
        $tags = $request->input('tags');
        $token = $request->input('token');

        $result = [
            'status' => false,
            'msg' => null,
        ];

        if (!$token) {
            $result['status'] = false;
            $result['msg'] = 'need_token';
            return response()->json($result);
        }
        
        if (!$tags) {
            $result['status'] = false;
            $result['msg'] = 'need_tags';
            return response()->json($result);
        }
        
        $user = Users::where('token', $token)->first();

        if ($user) {
            $materialModel = new Material;
            $materialModel->user_id = $user->id;
            $materialModel->title = $title ? $title : null;
            $materialModel->body = $body;
            $materialModel->public = $public;
            $materialModel->status = true;
            $materialModel->save();
            foreach($tags as $tag) {
                $tagModel = new MaterialTags;
                $tagModel->material_id = $materialModel->id;
                $tagModel->user_id = $user->id;
                $tagModel->tags = $tag;
                $tagModel->save();
            }
            $result['status'] = true;
            $result['msg'] = 'success';
            $result['data'] = $materialModel->id;
            return response()->json($result);
        } else {
            $result['status'] = false;
            $result['msg'] = 'need_login';
            return response()->json($result);
        }
    }

    public function index(Request $request) {
        $tag_id = $request->input('tag_id');
        $page = $request->input('page');
        $user_id = $request->input('user_id');
        dump($request);die();
    }

    public function detail(Request $request) {
        $m_id = $request->input('m_id');
        dump($request);die();
    }

    public function remove(Request $request) {
        $m_id = $request->input('m_id');
        dump($request);die();
    }

    public function update(Request $request) {
        $m_id = $request->input('m_id');
        dump($request);die();
    }

    public function tags(Request $request) {
        $m_id = $request->input('m_id');
        dump($request);die();
    }
}
