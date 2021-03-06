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

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/posts', [PostController::class, 'index'])->name('post.index');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('post.show');
    Route::get('/post/create', [PostController::class, 'create'])->name('post.create');
    Route::post('/posts', [PostController::class, 'store'])->name('post.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('post.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('post.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('post.destroy');

    Route::get('/posts/{post}/like', [PostController::class, 'like'])->name('post.like');
});


require __DIR__ . '/auth.php';
