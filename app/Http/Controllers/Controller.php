<?php

namespace App\Http\Controllers;

use App\Models\ErrorsStore;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function errors($src, $desc)
    {
        try {
            $error = ErrorsStore::create([
                'src' => $src,
                'description' => $desc,
            ]);
            return response()->json(['success' => false, 'message' => 'Pleas contact supporting team!, error code: ' .  $error->id]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function sendResponse($message, $data = null, $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function sendErrorResponse($message, $data = null, $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
