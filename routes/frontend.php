<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'index'], function () {
    Route::get('section', [\App\Http\Controllers\Frontend\IndexController::class, 'section']);

    Route::get('product', [\App\Http\Controllers\Frontend\IndexController::class, 'product']);

    Route::get('carousel', [\App\Http\Controllers\Frontend\IndexController::class, 'carousel']);
});

Route::group(['prefix' => 'catalog'], function () {
    Route::get('sections', [\App\Http\Controllers\Frontend\CatalogController::class, 'sections']);

    Route::get('sections/{sectionCode}', [\App\Http\Controllers\Frontend\CatalogController::class, 'sectionByCode']);

    Route::get('products/{productCode}', [\App\Http\Controllers\Frontend\CatalogController::class, 'productByCode']);
});

Route::group(['prefix' => 'digital'], function () {
    Route::get('/', [\App\Http\Controllers\Frontend\DigitalController::class, 'digitals']);
});

Route::group(['prefix' => 'vendor'], function () {
    Route::get('/', [\App\Http\Controllers\Frontend\DigitalController::class, 'venrods']);
});

Route::get('/page/{pageCode}', [\App\Http\Controllers\Frontend\PageController::class, 'pageByCode']);
