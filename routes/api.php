<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;

Route::get('/video/{id}/{quality}',[VideoController::class, 'show']);
Route::post('/video.upload',[VideoController::class, 'store']);
Route::post('/video.delete/{id}',[VideoController::class, 'destroy']);