<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponse
{
    public function successResponse($data, $statusCode = Response::HTTP_OK) : Response
    {
        return response($data, $statusCode)
            ->header('Content-Type', 'application/json');
    }

    public function errorResponse($errorMessage, $statusCode) : JsonResponse
    {
        return response()->json([
            'error' => $errorMessage,
            'error_code' => $statusCode
        ], $statusCode);
    }

    public function errorMessage($errorMessage, $statusCode) : Response
    {
        return response($errorMessage, $statusCode)
            ->header('Content-Type', 'application/json');
    }
}
