<?php

use App\Http\Controllers\Api\V1\EngineerController;
use App\Http\Controllers\Api\V1\EngineerTicketsController;
use App\Http\Controllers\Api\V1\TicketController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tickets', TicketController::class)->except(['update', 'destroy']);
    Route::put('tickets/{ticket}', [TicketController::class, 'replace']);
    Route::patch('tickets/{ticket}', [TicketController::class, 'update']);
    Route::delete('tickets/{ticket}', [TicketController::class, 'destroy']);

    Route::post('tickets/{ticket}/comment', [TicketController::class, 'comment']);

    Route::post('tickets/{ticket}/engineer', [TicketController::class, 'assign']);
    Route::delete('tickets/{ticket}/engineer/{engineer}', [TicketController::class, 'unassign']);

    Route::get('user', [UserController::class, 'me']);
    Route::apiResource('users', UserController::class)->except(['update']);
    Route::put('users/{user}', [UserController::class, 'replace']);
    Route::patch('users/{user}', [UserController::class, 'update']);

    Route::apiResource('engineers', EngineerController::class);

    Route::get('engineers/{engineer}', [EngineerController::class, 'show'])
        ->name('engineers.show');

    Route::get('engineer/tickets', [EngineerTicketsController::class, 'index'])
        ->name('engineer.tickets');

    Route::get('engineer/{engineer}/tickets', [EngineerTicketsController::class, 'show'])
        ->name('engineer.tickets.show');
});
