<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function upload(Request $request) {
        try {
            $file = $request->file('editormd-image-file');
            $path = $file->store('public/images');
            $fullPath = asset(Storage::url($path));
            $result = [
                'success' => 1,
                'message' => 'success',
                'url' => $fullPath,
            ];
            return response()->json($result);
        } catch (\Exception $e) {
            $result = [
                'success' => 0,
                'message' => $e->getMessage(),
                'url' => '',
            ];
            return response()->json($result);
        }
    }
}
