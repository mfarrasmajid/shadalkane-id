<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\YoutubeController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\ImageEditorController;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Redirect root to dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // YouTube Downloader
    Route::get('/youtube', [YoutubeController::class, 'index'])->name('youtube');
    Route::post('/youtube/info', [YoutubeController::class, 'getInfo'])->name('youtube.info');
    Route::post('/youtube/download', [YoutubeController::class, 'download'])->name('youtube.download');
    Route::get('/youtube/serve', [YoutubeController::class, 'serveFile'])->name('youtube.serve');

    // QR Code Generator
    Route::get('/qrcode', [QrCodeController::class, 'index'])->name('qrcode');
    Route::post('/qrcode/generate', [QrCodeController::class, 'generate'])->name('qrcode.generate');

    // Image Editor
    Route::get('/image-editor', [ImageEditorController::class, 'index'])->name('image-editor');
    Route::post('/image-editor/upload', [ImageEditorController::class, 'upload'])->name('image-editor.upload');
    Route::post('/image-editor/save', [ImageEditorController::class, 'save'])->name('image-editor.save');
});
