<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Notifications\SendNotificationController;

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

Route::get('/', [SendNotificationController::class, 'index'])->name('notifications.index');
Route::post('save-token-firebase', [SendNotificationController::class, 'saveToken'])->name('notifications.save.token');
Route::post('send-notification', [SendNotificationController::class, 'sendNotification'])->name('notifications.send');
Route::get('test-click-action', [SendNotificationController::class, 'testClickAction'])->name('notifications.test');