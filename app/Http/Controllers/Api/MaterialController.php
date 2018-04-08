<?php

namespace App\Http\Controllers\Api;

use DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use App\Model\Users;
use App\Model\Material;
use App\Model\MaterialTag;
use App\Model\Tags;
use Illuminate\Database\QueryException;

class MaterialController extends Controller
{
    public function create(Request $request) {
        $title = $request->input('title');
        $body = html_entity_decode($request->input('body'));
        $public = $request->input('material_type');
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
        
        /* temp commented
        if (!$tags) {
            $result['status'] = false;
            $result['msg'] = 'need_tags';
            return response()->json($result);
        }
        */
        
        $user = Users::where('token', $token)->first();

        if ($user) {
            DB::beginTransaction();
            try {
                $materialModel = new Material;
                $materialModel->user_id = $user->id;
                $materialModel->title = $title ? $title : null;
                $materialModel->body = $body;
                $materialModel->public = $public;
                $materialModel->status = true;
                $materialModel->save();
                /* temp commented
                foreach ($tags as $tag) {
                    $tagModel = Tags::where('tag_content', $tag)->first();
                    if (!$tagModel) {
                        $tagModel = new Tags;
                        $tagModel->tag_content = $tag;
                        $tagModel->save();
                    }
                    $materialModel->tags()->attach($tagModel->id, ['user_id' => $user->id]);
                }
                */
                $result['status'] = true;
                $result['msg'] = 'success';
                $result['data'] = $materialModel->id;
                DB::commit();
                return response()->json($result);
            } catch (QueryException $ex) {
                DB::rollback();
                $result['status'] = false;
                $result['msg'] = $ex->getMessage();
                return response()->json($result);
            }
        } else {
            $result['status'] = false;
            $result['msg'] = 'need_login';
            return response()->json($result);
        }
    }

    public function index(Request $request) {
        $tag_id = $request->input('tag_id');
        $token = $request->input('token');
        $userId = $request->input('userid');
        // 0 for private or 1 for public
        $public = (int)$request->input('type', 1);
        $limit = 15;
        // $limit = 1;

        $where = [];
        $where['public'] = $public;
        $result = [
            'status' => false,
            'msg' => null,
            'data' => [],
        ];
        if ($userId) {
            $user = Users::find($userId);
            if ($user) {
                $where['user_id'] = $user->id;
            }
        } else {
            $user = null;
        }
        if ($public === 0) {
            if ($user && $user->token === $token) {
                $where['public'] = 0; 
            } else {
                $result['status'] = false;
                $result['msg'] = 'no_auth';
                return response()->json($result);
            }
        }

        $dao = Material::where($where)->orderBy('id', 'DESC');

        if ($tag_id) {
            $tagDao = Tags::find($tag_id);
            if ($tagDao) {
                $dao = $dao->whereHas('tags', function ($query) use($tag_id) {
                    $query->where('tag_id', $tag_id);
                });
            }
        }

        $materials = $dao->paginate($limit);
        $result['status'] = true;
        $result['msg'] = 'get_data';
        $result['data'] = $materials;
        return response()->json($result);
    }

    public function detail(Request $request) {
        $m_id = $request->input('m_id');
        $token = $request->input('token');
        $material = Material::with(['user', 'tags'])->find($m_id);
        if ($material) {
            if ($material->public == 1) {
                return response()->json([
                    'status' => true,
                    'msg' => 'success',
                    'data' => $material,
                ]);
            } else {
                $user = Users::where('token', $token)->first();
                if ($user && $material->user_id === $user->id) {
                    return response()->json([
                        'status' => true,
                        'msg' => 'success',
                        'data' => $material,
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'msg' => 'no_auth',
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'not_exist',
            ]);
        }
    }

    public function remove(Request $request) {
        $result = [
            'status' => false,
            'msg' => null,
            'data' => [],
        ];
        $m_id = $request->input('m_id');
        $token = $request->input('token');

        if (!$token) {
            $result['status'] = false;
            $result['msg'] = 'need_login';
            return response()->json($result);
        }

        $user = Users::where('token', $token)->first();
        if (!$user) {
            $result['status'] = false;
            $result['msg'] = 'not_find_user';
            return response()->json($result);
        }

        $material = Material::where([
                'id' => $m_id,
                'user_id' => $user->id,
            ])->first();
        if ($material) {
            // remove associations
            $material->tags()->detach();
            if ($material->delete()) {
                $result['status'] = true;
                $result['msg'] = 'remove_success';
            } else {
                $result['status'] = false;
                $result['msg'] = 'remove_failed';
            }
        } else {
            $result['status'] = false;
            $result['msg'] = 'no_data';
        }
        return response()->json($result);
    }

    public function update(Request $request) {
        $result = [
            'status' => false,
            'msg' => null,
            'data' => [],
        ];
        $m_id = $request->input('m_id');
        $token = $request->input('token');
        $title = $request->input('title');
        $body = html_entity_decode($request->input('body'));
        $public = (int)$request->input('type');
        $tags = $request->input('tags');

        if (!$token) {
            $result['status'] = false;
            $result['msg'] = 'need_login';
            return response()->json($result);
        }
        $user = Users::where('token', $token)->first();
        if (!$user) {
            $result['status'] = false;
            $result['msg'] = 'not_find_user';
            return response()->json($result);
        }

        $material = Material::where([
                'id' => $m_id,
                'user_id' => $user->id,
            ])->first();

        if ($material) {
            DB::beginTransaction();
            try {
                if ($title) {
                    $material->title = $title;
                }
                if ($body) {
                    $material->body = $body;
                }
                $material->public = $public;
                $material->save();

                if ($tags) {
                    // remove previous tags
                    $material->tags()->detach();
                    // add new tags
                    foreach ($tags as $tag) {
                        $tagModel = Tags::where('tag_content', $tag)->first();
                        if (!$tagModel) {
                            $tagModel = new Tags;
                            $tagModel->tag_content = $tag;
                            $tagModel->save();
                        }
                        $material->tags()->attach($tagModel->id, ['user_id' => $user->id]);
                    }
                }
                DB::commit();
                $result['status'] = true;
                $result['msg'] = 'success';
                $result['data'] = $material->id;
                return response()->json($result);
            } catch (QueryException $ex) {
                DB::rollback();
                $result['status'] = false;
                $result['msg'] = $ex->get_messages();
                return response()->json($result);
            }
        } else {
            $result['status'] = false;
            $result['msg'] = 'not_found_material';
        }
        return response()->json($result);
    }

    public function tags(Request $request) {
        $result = [
            'status' => false,
            'msg' => null,
            'data' => [],
        ];
        $token = $request->input('token');
        $g = $request->input('g');

        $tags = null;
        // get public tags
        if ($g) {
            $tags = Tags::where('public', true)->get();
        }
        
        // get personal tags
        if ($tags === null && $token) {
            $user = Users::where('token', $token)->first();
            if ($user) {
                $tags = Tags::whereHas('materials', function ($query) use ($user) {
                    $query->where('material.user_id', $user->id);
                })->get();
            }
        }

        // get public materials' tags
        if ($tags === null) {
            $tags = Tags::whereHas('materials', function ($query) {
                $query->where('material.public', true);
            })->get();
        }
        $result['status'] = true;
        $result['msg'] = 'get_data';
        $result['data'] = $tags;
        return response()->json($result);
    }
}
