<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app' => 'CorchoAppApi',
        'status' => 'running'
    ]);
});

Route::get('/login', function () {
    return response()->json(
        [
            'message' => 'Unauthenticated.'
        ],
        401
    );
})->name('login');
