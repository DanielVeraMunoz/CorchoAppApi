<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\CommentController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');

//AUTH

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::delete('/logout', [AuthController::class, 'logout'])->middleware('auth:api');

//NOTES

Route::post('/notes', [NoteController::class, 'store'])->middleware('auth:api');

Route::get('/notes', [NoteController::class, 'index'])->middleware('auth:api');

Route::get('/notes/{id}', [NoteController::class, 'show'])->middleware('auth:api');

Route::put('/notes/{id}', [NoteController::class, 'update'])->middleware('auth:api');

Route::delete('/notes/{id}', [NoteController::class, 'destroy'])->middleware('auth:api');

//COMMENTS

Route::post('/notes/{id}/comments', [CommentController::class, 'store'])->middleware('auth:api');

Route::get('/notes/{id}/comments', [CommentController::class, 'index'])->middleware('auth:api');

Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->middleware('auth:api');

Route::put('/comments/{id}', [CommentController::class, 'update'])->middleware('auth:api');

//THANKS

Route::post('/users/{id}/thanks', [\App\Http\Controllers\Api\ThankController::class, 'store'])->middleware('auth:api');

Route::get('/users/{id}/thanks', [\App\Http\Controllers\Api\ThankController::class, 'index'])->middleware('auth:api');

Route::delete('/thanks/{id}', [\App\Http\Controllers\Api\ThankController::class, 'destroy'])->middleware('auth:api');


//USERS

Route::get('/users', [\App\Http\Controllers\Api\UserController::class, 'index'])->middleware('auth:api');

Route::get('/users/{id}', [\App\Http\Controllers\Api\UserController::class, 'show'])->middleware('auth:api');

Route::put('/users/{id}', [\App\Http\Controllers\Api\UserController::class, 'update'])->middleware('auth:api');

Route::delete('/users/{id}', [\App\Http\Controllers\Api\UserController::class, 'destroy'])->middleware('auth:api');

//STATS

Route::get('/stats/community', [\App\Http\Controllers\Api\StatsController::class, 'communityStats'])->middleware('auth:api');

Route::get('/stats/top-helpers', [\App\Http\Controllers\Api\StatsController::class, 'topHelpers'])->middleware('auth:api');