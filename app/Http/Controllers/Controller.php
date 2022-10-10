<?php

namespace App\Http\Controllers;

use App\Models\ErrorsStore;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

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

    public function is_date($val)
    {
        try {
            Carbon::parse($val);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function isPositiveInt($val)
    {
        return !is_int($val) ? false : (intval($val) >= 0 ? true : false);
    }
}
