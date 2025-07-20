<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;  
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\LoginController;

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

/*
Route::get('/', function () {
    return ['Laravel' => app()->version()];
});
*/

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login']);

Route::middleware(['auth'])->group(function () {
    Route::get('/myaccount/passwords', [AccountController::class, 'passwords'])->name('account.passwords');
    Route::post('/myaccount/passwords/store', [AccountController::class, 'storePassword'])->name('passwords.store');
    Route::delete('/myaccount/passwords/{id}', [AccountController::class, 'deletePassword'])->name('passwords.delete');

    Route::get('/myaccount/texts', [AccountController::class, 'texts'])->name('account.texts');
    Route::post('/myaccount/texts/store', [AccountController::class, 'storeText'])->name('texts.store');
    Route::delete('/myaccount/texts/{id}', [AccountController::class, 'deleteText'])->name('texts.delete');
});





// Return current user
Route::get('/debug-user', function (Request $request) {
    return $request->user() ?? 'No hay usuario autenticado';
})->middleware('auth');




require __DIR__.'/auth.php';
