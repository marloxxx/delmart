<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\RequestRoomController;

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
// redirect to login page
Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'do_login'])->name('login');
Route::group(['middleware' => ['role:admin']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Credits
    Route::resource('credits', CreditController::class);

    // Products
    Route::resource('products', ProductController::class);

    // Rooms
    Route::resource('rooms', RoomController::class);

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/process', [OrderController::class, 'process'])->name('orders.process');
    Route::get('/orders/{id}/deny', [OrderController::class, 'deny'])->name('orders.deny');

    // Request Rooms
    Route::get('/request-rooms', [RequestRoomController::class, 'index'])->name('request-rooms.index');
    Route::get('/request-rooms/{id}', [RequestRoomController::class, 'show'])->name('request-rooms.show');
    Route::get('/request-rooms/{id}/approve', [RequestRoomController::class, 'approve'])->name('request-rooms.approve');
    Route::get('/request-rooms/{id}/reject', [RequestRoomController::class, 'reject'])->name('request-rooms.reject');

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
