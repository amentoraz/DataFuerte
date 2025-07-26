<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;  
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ElementController;
use App\Http\Controllers\ConfigurationController;

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


// Login
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login']);

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');


Route::middleware(['auth'])->group(function () {

    Route::get('/configuration', [ConfigurationController::class, 'index'])->name('configuration.index');
    Route::post('/configuration', [ConfigurationController::class, 'store'])->name('configuration.store');
    

    Route::middleware(['installation'])->group(function () {
        Route::get('/myaccount/elements/{uuid?}', [ElementController::class, 'index'])->name('account.elements');
        Route::post('/myaccount/elements/store', [ElementController::class, 'store'])->name('elements.store');
        Route::delete('/myaccount/elements/{id}', [ElementController::class, 'delete'])->name('elements.delete');
        Route::get('/myaccount/elements/get/{uuid}', [ElementController::class, 'get'])->name('elements.get')->middleware('throttle:get-element');
    });
});





// Return current user
//Route::get('/debug-user', function (Request $request) {
//    return $request->user() ?? 'No hay usuario autenticado';
//})->middleware('auth');




//require __DIR__.'/auth.php';
