<?php

namespace App\Http\Controllers;

use InfyOm\Generator\Utils\ResponseUtil;
use Response;

class AppBaseController extends Controller
{
    public function sendResponse($result, $message)
    {
        return response()->json(
            [
                'status' => true,
                'message' => $message,
                'data' => $result,
            ]
        );
    }

    public function sendError($error, $code = 404)
    {
        return response()->json(
            [
                'status' => false,
                'message' => $message,
                'data' => [],
            ]
        );
    }
}