<?php

use App\Http\Controllers\PostController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/posts/', function () {
    return view('post.show');
})->middleware(['auth'])->name('post.show');

Route::get('/posts/create', function () {
    return view('post.create');
})->middleware(['auth'])->name('post.create');

Route::post('/posts', [PostController::class, 'store'])
    ->middleware(['auth'])->name('post.store');

require __DIR__ . '/auth.php';
