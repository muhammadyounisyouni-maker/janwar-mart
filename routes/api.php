<?php

use App\Http\Controllers\Api\CatalogController;
use Illuminate\Support\Facades\Route;

// Public, read-only JSON API (only admin-approved ads and verified doctors are ever returned)
Route::prefix('v1')->middleware('throttle:api')->group(function () {
    Route::get('/listings', [CatalogController::class, 'listings']);
    Route::get('/listings/{ref}', [CatalogController::class, 'listing']);
    Route::get('/doctors', [CatalogController::class, 'doctors']);
    Route::get('/categories', [CatalogController::class, 'categories']);
});
