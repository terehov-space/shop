<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [\App\Http\Controllers\Frontend\IndexController::class, 'index'])->name('index');

Route::group(['prefix' => 'catalog'], function () {
    Route::get('/', [\App\Http\Controllers\Frontend\CatalogController::class, 'index'])->name('catalog');
    Route::get('/{sectionCode}', [\App\Http\Controllers\Frontend\CatalogController::class, 'sectionPage'])->name('section');
});

Route::get('/product/{productCode}', [\App\Http\Controllers\Frontend\CatalogController::class, 'productPage'])->name('page');

Route::get('/search', [\App\Http\Controllers\Frontend\CatalogController::class, 'search']);

Route::get('/digital', [\App\Http\Controllers\Frontend\DigitalController::class, 'index']);

Route::get('/basket', [\App\Http\Controllers\Frontend\BasketController::class, 'basketPage']);

Route::post('/basket', [\App\Http\Controllers\Frontend\BasketController::class, 'modifyBasket']);

Route::get('/new-order', [\App\Http\Controllers\Frontend\BasketController::class, 'newOrder']);

Route::post('/new-order', [\App\Http\Controllers\Frontend\BasketController::class, 'storeOrder']);

Route::get('/orders', [\App\Http\Controllers\Frontend\BasketController::class, 'getOrder']);

Route::post('/form', [\App\Http\Controllers\Frontend\FormController::class, 'store']);

Route::group(['prefix' => 'admin', 'middleware' => 'auth.basic'], function () {
//    Route::get('/', function () {
//        return 'Восстановление: ' . round((1 - (strtotime('today  19 hours') - strtotime('now')) / 36000) * 100, 1) . '%';
//    });

    Route::post('/upload/image', [\App\Http\Controllers\Admin\UploadController::class, 'image']);
    Route::post('/upload/string', [\App\Http\Controllers\Admin\UploadController::class, 'noModel']);
    Route::post('/upload/file', [\App\Http\Controllers\Admin\UploadController::class, 'file']);

    Route::group(['prefix' => 'catalog'], function () {
        Route::group(['prefix' => 'sections'], function () {
            Route::get('/', [\App\Http\Controllers\Admin\SectionController::class, 'getList']);

            Route::get('/{sectionId}', [\App\Http\Controllers\Admin\SectionController::class, 'getListBySection']);

            Route::get('/{sectionId}/edit', [\App\Http\Controllers\Admin\SectionController::class, 'editPage']);

            Route::post('/{sectionId}/edit', [\App\Http\Controllers\Admin\SectionController::class, 'store']);

            Route::get('/{sectionId}/props', [\App\Http\Controllers\Admin\PropertyController::class, 'getList']);

            Route::post('/{sectionId}/props', [\App\Http\Controllers\Admin\PropertyController::class, 'store']);

            Route::get('/{sectionId}/props/{propertyId}', [\App\Http\Controllers\Admin\PropertyController::class, 'editPage']);

            Route::post('/{sectionId}/props/{propertyId}', [\App\Http\Controllers\Admin\PropertyController::class, 'storeOption']);

            Route::delete('/{sectionId}/props/{propertyId}', [\App\Http\Controllers\Admin\PropertyController::class, 'deleteProperty']);

            Route::delete('/{sectionId}/props/{propertyId}/{optionId}', [\App\Http\Controllers\Admin\PropertyController::class, 'deleteOption']);
        });

        Route::group(['prefix' => 'products'], function () {
            Route::get('/', [\App\Http\Controllers\Admin\ProductController::class, 'getList']);

            Route::get('/{productId}', [\App\Http\Controllers\Admin\ProductController::class, 'editPage']);

            Route::post('/{productId}', [\App\Http\Controllers\Admin\ProductController::class, 'store']);

            Route::delete('/{productId}', [\App\Http\Controllers\Admin\ProductController::class, 'delete']);
        });

        Route::group(['prefix' => 'group'], function () {
            Route::post('/sections', [\App\Http\Controllers\Admin\GroupMovingController::class, 'storeSections']);

            Route::post('/vendors', [\App\Http\Controllers\Admin\GroupMovingController::class, 'storeVendors']);
        });
    });

    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', [\App\Http\Controllers\Admin\OrderController::class, 'getList']);

        Route::get('/{pageId}', [\App\Http\Controllers\Admin\OrderController::class, 'editPage']);

        Route::post('/{carouselId}', [\App\Http\Controllers\Admin\OrderController::class, 'store']);
    });

    Route::group(['prefix' => 'forms'], function () {
        Route::get('/', [\App\Http\Controllers\Admin\FormController::class, 'getList']);

        Route::get('/{formId}', [\App\Http\Controllers\Admin\FormController::class, 'editPage']);
    });

    Route::group(['prefix' => 'content'], function () {
        Route::group(['prefix' => 'vendors'], function () {
            Route::get('/', [\App\Http\Controllers\Admin\VendorController::class, 'getList']);

            Route::get('/{vendorId}', [\App\Http\Controllers\Admin\VendorController::class, 'editPage']);

            Route::post('/{vendorId}', [\App\Http\Controllers\Admin\VendorController::class, 'store']);

            Route::delete('/{vendorId}', [\App\Http\Controllers\Admin\VendorController::class, 'deleteItem']);
        });

        Route::group(['prefix' => 'carousels'], function () {
            Route::get('/', [\App\Http\Controllers\Admin\CarouselController::class, 'getList']);

            Route::get('/{carouselId}', [\App\Http\Controllers\Admin\CarouselController::class, 'editPage']);

            Route::post('/{carouselId}', [\App\Http\Controllers\Admin\CarouselController::class, 'store']);

            Route::delete('/{carouselId}', [\App\Http\Controllers\Admin\CarouselController::class, 'deleteItem']);
        });

        Route::group(['prefix' => 'digitals'], function () {
            Route::get('/', [\App\Http\Controllers\Admin\DigitalController::class, 'getList']);

            Route::get('/{carouselId}', [\App\Http\Controllers\Admin\DigitalController::class, 'editPage']);

            Route::post('/{carouselId}', [\App\Http\Controllers\Admin\DigitalController::class, 'store']);

            Route::delete('/{carouselId}', [\App\Http\Controllers\Admin\DigitalController::class, 'deleteItem']);
        });

        Route::group(['prefix' => 'pages'], function () {
            Route::get('/', [\App\Http\Controllers\Admin\PageController::class, 'getList']);

            Route::get('/{pageId}', [\App\Http\Controllers\Admin\PageController::class, 'editPage']);

            Route::post('/{carouselId}', [\App\Http\Controllers\Admin\PageController::class, 'store']);
        });
    });

    Route::group(['prefix' => 'settings'], function () {
        Route::get('/', [\App\Http\Controllers\Admin\SettingController::class, 'getItem']);

        Route::post('/', [\App\Http\Controllers\Admin\SettingController::class, 'storeItem']);
    });

    Route::get('/', function () {
        return redirect('/admin/catalog/sections');
    });

    Route::get('/{any}', function () {
        return redirect('/admin/catalog/sections');
    })
        ->where('any', '.*');
});

Route::get('/sitemap.xml', function () {
    $products = \App\Models\Product::orderBy('updated_at', 'desc')->first();
    $sections = \App\Models\Section::orderBy('updated_at', 'desc')->first();

    return response()
        ->view('sitemap', compact('products', 'sections'))
        ->header('Content-Type', 'text/xml');
});

Route::get('/product.xml', function () {
    $products = \App\Models\Product::whereNotNull('sectionId')->orderBy('updated_at', 'desc')->get();

    return response()
        ->view('product', compact('products'))
        ->header('Content-Type', 'text/xml');
});

Route::get('/sections.xml', function () {
    $sections = \App\Models\Section::productable()->orderBy('updated_at', 'desc')->get();

    return response()
        ->view('sections', compact('sections'))
        ->header('Content-Type', 'text/xml');
});

Route::get('/{any}', [\App\Http\Controllers\Frontend\PageController::class, 'pageByCode'])
    ->where('any', '.*');
