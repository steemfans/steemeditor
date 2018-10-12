<?php

namespace App\Http\Controllers\Api;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Users;
use App\Model\PostQueue;
use Illuminate\Database\QueryException;

class PostQueueController extends Controller
{
    public function create(Request $request) {
        $title = $request->input('title');
        $username = $request->input('username');
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
        
        $user = Users::where('token', $token)->first();

        if ($user && $user->username == $username) {
            $queue = new PostQueue;
            $queue->username = $username;
            $queue->title = $title;
            $queue->save();
            $result['status'] = true;
            $result['msg'] = 'success';
            $result['data'] = $queue->id;
            return response()->json($result);
        } else {
            $result['status'] = false;
            $result['msg'] = 'need_login';
            return response()->json($result);
        }
    }
}
