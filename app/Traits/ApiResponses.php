<?php

namespace  App\Traits;

trait ApiResponses{

    protected function ok($message, $data = [])
    {
        return $this->success($message, $data, 200);
    }

    protected function success($message, $data = [], $statusCode = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => $message,
             'data' => $data,
            'status' => $statusCode
        ], $statusCode);
    }

    protected function error($message, $statusCode): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => $message,
            'status' => $statusCode
        ], $statusCode);
    }
}
