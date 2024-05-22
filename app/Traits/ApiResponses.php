<?php

namespace  App\Traits;

trait ApiResponses{

    protected function ok($message)
    {
        return $this->success($message, 200);
    }

    protected function success($message, $statusCode = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => $message,
            'status' => $statusCode
        ], $statusCode);
    }
}
