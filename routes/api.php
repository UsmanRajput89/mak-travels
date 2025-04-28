<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DealController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:api')->group(function () {
    Route::get('/deals', [DealController::class, 'allDeals']);
    Route::get('/deals/{id} ', [DealController::class, 'getDealById']);

    Route::post('/deals/{dealId}/bookmark', [DealController::class, 'bookmarkDeal']);
    Route::post('/deals/{dealId}/unbookmark', [DealController::class, 'unbookmarkDeal']);
    Route::get('/bookmarked-deals', [DealController::class, 'getBookmarkedDeals']);
    
});
