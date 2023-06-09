<?php

use App\Http\Controllers\VideoController;
use App\Models\Video;
use Illuminate\Support\Facades\Http;
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

// Route::get('/', [VideoController::class, 'show']);



Route::post('clearVideos', [VideoController::class, 'clear'])->name('videos.clear');


Route::resource('videos', VideoController::class);


Route::get('/captions/example.vtt', function () {
    $response = Http::get('http://ffmpegvideojs.test/captions/example.vtt');

    return $response->body();
});
Route::get('/video-conversion-progress', [VideoController::class, 'getVideoConversionProgress'])
    ->name('video-conversion-progress');
