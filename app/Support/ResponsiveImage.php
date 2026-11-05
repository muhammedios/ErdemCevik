<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/** Original uploads remain editable; derived files are disposable display copies. */
class ResponsiveImage
{
    private const WIDTHS = [320, 640, 960, 1440];

    private static function source(string $url): ?string
    {
        $path = rawurldecode(parse_url($url, PHP_URL_PATH) ?: '');
        if (! str_starts_with($path, '/images/') && ! str_starts_with($path, '/storage/')) {
            return null;
        }
        $isUpload = str_starts_with($path, '/storage/');
        $root = $isUpload ? realpath(Storage::disk('public')->path('')) : realpath(public_path('images'));
        $file = realpath($isUpload ? Storage::disk('public')->path(substr($path, 9)) : public_path(ltrim($path, '/')));
        if (! $root || ! $file || ! str_starts_with($file, $root.DIRECTORY_SEPARATOR) || ! is_file($file)) {
            return null;
        }

        return $file;
    }

    private static function key(string $file): string
    {
        return substr(hash('sha256', 'v2|'.$file.'|'.filemtime($file).'|'.filesize($file)), 0, 24);
    }

    public static function generate(string $url): void
    {
        if (! function_exists('imagewebp') || ! ($file = self::source($url))) {
            return;
        }
        $info = @getimagesize($file);
        if (! $info || ! in_array($info['mime'], ['image/jpeg', 'image/png', 'image/webp'], true) || $info[0] * $info[1] > 12000000) {
            return;
        }
        $orientation = $info['mime'] === 'image/jpeg' && function_exists('exif_read_data') ? (@exif_read_data($file)['Orientation'] ?? 1) : 1;
        $displayWidth = in_array($orientation, [5, 6, 7, 8], true) ? $info[1] : $info[0];
        $key = self::key($file);
        $disk = Storage::disk('public');
        $image = null;
        try {
            foreach (self::WIDTHS as $width) {
                $width = min($width, $displayWidth);
                $path = 'optimized/'.$key.'-'.$width.'.webp';
                if ($disk->exists($path)) {
                    continue;
                }
                if (! $image) {
                    $image = match ($info['mime']) {
                        'image/jpeg' => @imagecreatefromjpeg($file),
                        'image/png' => @imagecreatefrompng($file),
                        'image/webp' => @imagecreatefromwebp($file),
                    };
                    if (! $image) {
                        return;
                    }
                    // Camera orientation must match the original photograph.
                    if ($info['mime'] === 'image/jpeg') {
                        if (in_array($orientation, [2, 4, 5, 7], true)) {
                            imageflip($image, IMG_FLIP_HORIZONTAL);
                        }
                        $angle = match ($orientation) {
                            3,4 => 180,5,6 => -90,7,8 => 90,default => 0
                        };
                        if ($angle) {
                            $image = imagerotate($image, $angle, 0);
                        }
                    }
                }
                $targetWidth = min($width, imagesx($image));
                $height = max(1, (int) round(imagesy($image) * $targetWidth / imagesx($image)));
                $resized = imagecreatetruecolor($targetWidth, $height);
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $targetWidth, $height, imagesx($image), imagesy($image));
                ob_start();
                imagewebp($resized, null, 80);
                $bytes = ob_get_clean();
                if ($bytes) {
                    $disk->put($path, $bytes);
                }
                imagedestroy($resized);
            }
        } catch (\Throwable $exception) {
            Log::warning('Image display copies could not be generated', ['exception' => $exception->getMessage()]);
        } finally {
            if ($image) {
                imagedestroy($image);
            }
        }
    }

    public static function attributes(string $url): array
    {
        $fallback = ['src' => $url, 'srcset' => null, 'width' => null, 'height' => null];
        if (! ($file = self::source($url))) {
            return $fallback;
        }
        $info = @getimagesize($file);
        if (! $info) {
            return $fallback;
        }
        $orientation = $info['mime'] === 'image/jpeg' && function_exists('exif_read_data') ? (@exif_read_data($file)['Orientation'] ?? 1) : 1;
        if (in_array($orientation, [5, 6, 7, 8], true)) {
            [$info[0],$info[1]] = [$info[1], $info[0]];
        }
        $fallback['width'] = $info[0];
        $fallback['height'] = $info[1];
        $key = self::key($file);
        $sources = [];
        foreach (self::WIDTHS as $width) {
            $width = min($width, $info[0]);
            $path = 'optimized/'.$key.'-'.$width.'.webp';
            if (Storage::disk('public')->exists($path)) {
                $sources[$width] = '/storage/'.$path;
            }
        }
        if (! $sources) {
            return $fallback;
        }

        return ['src' => end($sources), 'srcset' => implode(', ', array_map(fn ($width, $src) => $src.' '.$width.'w', array_keys($sources), array_values($sources))), 'width' => $info[0], 'height' => $info[1]];
    }
}
