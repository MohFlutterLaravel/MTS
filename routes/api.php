<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CaisseController;
use App\Http\Controllers\PayemodeController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\VirementController;
use App\Http\Controllers\PayedetteController;
use App\Http\Controllers\AchatController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\ChargeController;
use App\Http\Controllers\TypechargeController;


Route::post('login', [UserController::class, "login"]);
Route::post('register', [UserController::class, "register"]);
Route::group(['prefix' => 'employee', 'middleware' => ['assign.guard:employee','jwt.auth']], function(){
    Route::post('check', [UserController::class, "isValidToken"]);
    Route::post('me', [UserController::class, "me"]);
    
    // routes categories
    Route::get('categories-select',[CategoryController::class, 'selectData']);
    Route::apiResource('categories', CategoryController::class);

    // routes clients
    Route::get('clients-filter',[ClientController::class, 'filterData']);
    Route::apiResource('clients', ClientController::class);

    // routes types produits 
    Route::get('types-select',[TypeController::class, 'selectData']);
    Route::apiResource('types', TypeController::class);

    // routes produits
    Route::get('produits-filter',[ProductController::class, 'filterData']);
    Route::apiResource('products', ProductController::class);

    // routes caisses 
    Route::post('encaisser',[CaisseController::class, 'encaisser']);
    Route::post('decaisser',[CaisseController::class, 'decaisser']);
    Route::get('caisses-filter',[CaisseController::class, 'filterData']);
    Route::apiResource('caisses', CaisseController::class);

    // routes payemodes
    Route::get('payemode-filter',[PayemodeController::class, 'filterData']);
    Route::apiResource('payemodes', PayemodeController::class);

    // routes sources
    Route::get('sources-filter',[SourceController::class, 'filterData']);
    Route::apiResource('sources', SourceController::class);

    // routes virements
    Route::apiResource('virements', VirementController::class);

    // routes payedettes
    Route::apiResource('payedettes', PayedetteController::class);

    // routes achats
    Route::apiResource('achats', AchatController::class);

    // routes ventes
    Route::apiResource('ventes', VenteController::class);
    
    // routes operations
    Route::post('result-filter-operations',[OperationController::class, 'resultFilter']);
    Route::get('operations-filter',[OperationController::class, 'filterData']);
    Route::apiResource('operations', OperationController::class);
    
    // routes charges
    Route::post('charges-filter',[ChargeController::class, 'filterData']);
    Route::apiResource('charges', ChargeController::class);

    // routes typecharges
    Route::apiResource('typecharges', TypechargeController::class);
});

