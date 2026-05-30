<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseApiController extends Controller
{
    public function sendResponse($data=[], $message = 'Success', $statusCode = 200)
    {
        return response()->json([
            'success'=>true,
            'message'=>$message,
            'data'=>$data,
        ], $statusCode);
    }

    public function sendError($message = 'Error', $errors=[],$statusCode= 400)
    {
        return response()->json([
            'success'=>false,
            'message'=>$message,
            'errors'=>$errors,
        ], $statusCode);
    }
}
