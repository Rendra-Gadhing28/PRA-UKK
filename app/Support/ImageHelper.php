<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class ImageHelper
{
    private static array $resolvedUrls = [];

    /**
     * Resolve image URL safely across Docker, Windows, and Linux environments.
     */
    public static function url(?string $path, ?string $fallback = null): string
    {
        $defaultFallback = $fallback ?? asset('logo/yalia-logos-trnsprnt.svg');

        if (blank($path)) {
            return $defaultFallback;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $cleanPath = ltrim($path, '/');
        $cacheKey = 'img_url_v1_' . md5($cleanPath);

        // 1. In-memory cache (fastest, per-request)
        if (isset(self::$resolvedUrls[$cacheKey])) {
            return self::$resolvedUrls[$cacheKey];
        }

        // 2. Application cache (Redis/File) to avoid slow file_exists on Docker volume
        $resolved = Cache::remember($cacheKey, now()->addDays(7), function () use ($cleanPath, $defaultFallback) {
            // 1. Physical file in public/storage (working symlink)
            if (file_exists(public_path('storage/' . $cleanPath))) {
                return asset('storage/' . $cleanPath);
            }

            // 2. Physical file in public/images/
            if (file_exists(public_path('images/' . $cleanPath))) {
                return asset('images/' . $cleanPath);
            }

            // 3. Alternate extensions in public/images/ (.jpg, .jpeg, .png, .webp, .svg)
            $pathWithoutExt = preg_replace('/\.[^.]+$/', '', $cleanPath);
            foreach (['jpg', 'jpeg', 'png', 'webp', 'svg'] as $ext) {
                $testPath = $pathWithoutExt . '.' . $ext;
                if (file_exists(public_path('images/' . $testPath))) {
                    return asset('images/' . $testPath);
                }
            }

            // 4. Fallback checking subpath in public/images/
            if (! str_starts_with($cleanPath, 'treatments/') && ! str_starts_with($cleanPath, 'beauticians/')) {
                foreach (['treatments', 'beauticians'] as $folder) {
                    $subPath = $folder . '/' . $cleanPath;
                    if (file_exists(public_path('images/' . $subPath))) {
                        return asset('images/' . $subPath);
                    }
                    $subWithoutExt = preg_replace('/\.[^.]+$/', '', $subPath);
                    foreach (['jpg', 'jpeg', 'png', 'webp', 'svg'] as $ext) {
                        if (file_exists(public_path('images/' . $subWithoutExt . '.' . $ext))) {
                            return asset('images/' . $subWithoutExt . '.' . $ext);
                        }
                    }
                }
            }

            // 5. Direct physical file in public/
            if (file_exists(public_path($cleanPath))) {
                return asset($cleanPath);
            }

            return $defaultFallback;
        });

        self::$resolvedUrls[$cacheKey] = $resolved;

        return $resolved;
    }
}
