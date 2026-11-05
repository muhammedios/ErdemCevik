<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
Route::get('/robots.txt', [\App\Http\Controllers\SeoController::class, 'robots']);
Route::get('/sitemap.xml', [\App\Http\Controllers\SeoController::class, 'sitemap']);
Route::redirect('/', '/tr', 301);
Route::get('/{locale}/{section?}/{slug?}', [SiteController::class, 'show'])
 ->where('locale', 'tr|en')->name('site');
