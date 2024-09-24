<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ArticleController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\SourceController;
use App\Http\Controllers\API\UserPreferencesController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});


// Route::post('get-articles',[ArticleController::class, 'index']);
// Route::post('get-source',[SourceController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user',[ArticleController::class, 'user']);
    Route::post('/get-articles',[ArticleController::class, 'index']);
    Route::post('/get-categories',[ArticleController::class, 'categories']);
    Route::post('/get-authors',[ArticleController::class, 'authors']);
    Route::post('/get-source',[SourceController::class, 'index']);
    Route::get('/preferences', [UserPreferencesController::class, 'getPreferences']);
    Route::post('/preferences', [UserPreferencesController::class, 'setPreferences']);
});