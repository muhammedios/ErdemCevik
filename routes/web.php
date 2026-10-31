<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
Route::redirect('/', '/tr');
Route::get('/{locale}/{section?}/{slug?}', [SiteController::class, 'show'])
 ->where('locale', 'tr|en')->name('site');
