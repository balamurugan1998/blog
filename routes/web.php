<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/post_dashboard', [PostController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('post_dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('http_request')->group(function () {
        Route::resource('posts', PostController::class);

        Route::controller(PostController::class)->group(function(){
            Route::any('post_datatable', 'post_datatable')->name('post_datatable');
            Route::any('post_multi_delete', 'post_multi_delete')->name('post_multi_delete');
            Route::any('checkduplicate', 'checkduplicate')->name('checkduplicate');
        });
    });
});

require __DIR__.'/auth.php';
