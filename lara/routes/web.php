<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::any('/dashboard', [DashboardController::class, 'show'])->middleware(['auth'])->name('dashboard');
Route::any('/charge', [DashboardController::class, 'charge'])->middleware(['auth'])->name('charge');
Route::any('/seeall', [DashboardController::class, 'seeall'])->middleware(['auth'])->name('seeall');
Route::get('/config', [DashboardController::class, 'config'])->middleware(['auth'])->name('config');
Route::get('/qrconfig', [DashboardController::class, 'qrconfig'])->middleware(['auth'])->name('qrconfig');
Route::get('/transactionstatus', [DashboardController::class, 'transactionstatus'])->middleware(['auth'])->name('transactionstatus');


//Route::get('/dashboard', function () {
//    return view('dashboard');
//})->middleware(['auth'])->name('dashboard');


require __DIR__.'/auth.php';
