<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\web\AuthController;
use App\Http\Controllers\web\BookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Admin Login Routes
Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/login-save', [AuthController::class, 'loginAuth'])->name('login.save');

// Admin Authentication Middleware
Route::group(['middleware' => ['AdminAuthentication'], 'prefix' => 'admin'], function () {
    
    // Books resource routes
    Route::controller(BookController::class)->group(function () {
        Route::get('/books', 'index')->name('book.index');
        Route::get('/books/list', 'bookList')->name('book.list');
        Route::get('/books/create', 'create')->name('book.create'); 
        Route::post('/books/store', 'store')->name('book.store');
        Route::get('/books/edit/{id}', 'show')->name('book.show');
        Route::put('/books/{id}', 'update')->name('book.update');
        Route::delete('/books/delete/{id}', 'destroy')->name('book.destroy');
    });

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

});
