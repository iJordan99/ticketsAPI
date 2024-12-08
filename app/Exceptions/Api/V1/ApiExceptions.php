<?php

namespace App\Exceptions\Api\V1;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiExceptions
{
    public static array $handlers = [
        AuthenticationException::class => 'handleAuthenticationException',
        ValidationException::class => 'handleValidationException',
        ModelNotFoundException::class => 'handleNotFoundException',
        NotFoundHttpException::class => 'handleNotFoundException',
        AccessDeniedHttpException::class => 'handleAccessDeniedException',
    ];

    public static function handleAuthenticationException(AuthenticationException $e, Request $request): JsonResponse
    {
        // log that sensitive stuff
        // should move this out to custom logger
        $source = 'Line: ' . $e->getLine() . ', File: ' . $e->getFile();
        Log::notice(basename(get_class($e)) . ' - ' . $e->getMessage() . ' - ' . $source);

        return response()->json([
            'error' => [
                'type' => class_basename($e),
                'status' => 401,
                'message' => $e->getMessage()
            ]
        ], 401);
    }

    public static function handleValidationException(ValidationException $e, Request $request): JsonResponse
    {
        foreach ($e->errors() as $key => $value)
            foreach ($value as $message) {
                $errors[] = [
                    'type' => class_basename($e),
                    'status' => 422,
                    'message' => $message,
                ];
            }

        return response()->json([
            'errors' => $errors
        ], 422);
    }

    public static function handleNotFoundException(ModelNotFoundException|NotFoundHttpException $e, Request $request): JsonResponse
    {
        return response()->json([
            'error' => [
                'type' => class_basename($e),
                'status' => 404,
                'message' => 'Not Found ' . $request->getRequestUri()
            ]
        ], 422);
    }

    public static function handleAccessDeniedException(AccessDeniedHttpException $e, Request $request): JsonResponse
    {
        return response()->json([
            'error' => [
                'type' => class_basename($e),
                'status' => 403,
                'message' => 'Forbidden'
            ]
        ], 403);
    }
}

