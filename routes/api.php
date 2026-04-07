<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\CommentController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::delete('/logout', [AuthController::class, 'logout'])->middleware('auth:api');

Route::post('/notes', [NoteController::class, 'store'])->middleware('auth:api');

Route::get('/notes', [NoteController::class, 'index'])->middleware('auth:api');

Route::get('/notes/{id}', [NoteController::class, 'show'])->middleware('auth:api');

Route::put('/notes/{id}', [NoteController::class, 'update'])->middleware('auth:api');

Route::delete('/notes/{id}', [NoteController::class, 'destroy'])->middleware('auth:api');

Route::post('/notes/{id}/comments', [CommentController::class, 'store'])->middleware('auth:api');

Route::get('/notes/{id}/comments', [CommentController::class, 'index'])->middleware('auth:api');

Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->middleware('auth:api');