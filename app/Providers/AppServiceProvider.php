<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Content::saved(function ($content): void {
            if ($content->wasChanged('image') || $content->wasRecentlyCreated) {
                if ($url = $content->imageUrl()) \App\Support\ResponsiveImage::generate($url);
            }
        });
        \App\Models\ClinicSetting::saved(function ($settings): void {
            foreach (['logo', 'popup_image'] as $field) {
                if (($settings->wasChanged($field) || $settings->wasRecentlyCreated) && $settings->$field) {
                    \App\Support\ResponsiveImage::generate('/storage/'.$settings->$field);
                }
            }
            if ($settings->wasChanged('site_images') || $settings->wasRecentlyCreated) {
                foreach ($settings->site_images ?? [] as $path) {
                    if ($path) \App\Support\ResponsiveImage::generate('/storage/'.$path);
                }
            }
        });
    }
}
