<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\NoteController;

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

Route::get('/', [IndexController::class, 'index']);

Route::middleware('auth')->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
    
    Route::controller(TaskController::class)->group(function () {
        Route::get('/', 'index')->name('task');
        Route::get('/task', 'index')->name('task');
        Route::post('/task/add-task', 'addTask')->name('add-task');
        Route::get('/task/show-task', 'showTask')->name('show-task');
        Route::delete('/task/delete-task/{id}', 'deleteTask')->name('delete-task');
        Route::get('/tasks/{id}/edit', 'editTask')->name('edit-task');
        Route::put('/tasks/{id}', 'updateTask')->name('update-task');
    });

});

require __DIR__.'/auth.php';
