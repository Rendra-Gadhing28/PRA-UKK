<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    private static array $resolvedUrls = [];

    /**
     * Clear cached URL resolution for a specific path or all cached images.
     */
    public static function clearCache(?string $path = null): void
    {
        if ($path) {
            $cleanPath = ltrim($path, '/');
            $cacheKey = 'img_url_v2_'.md5($cleanPath);
            unset(self::$resolvedUrls[$cacheKey]);
            Cache::forget($cacheKey);

            $pathWithoutFolder = basename($cleanPath);
            $subKey = 'img_url_v2_'.md5($pathWithoutFolder);
            unset(self::$resolvedUrls[$subKey]);
            Cache::forget($subKey);
        } else {
            self::$resolvedUrls = [];
        }
    }

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
        $cacheKey = 'img_url_v2_'.md5($cleanPath);

        // 1. In-memory cache (fastest, per-request)
        if (isset(self::$resolvedUrls[$cacheKey])) {
            return self::$resolvedUrls[$cacheKey];
        }

        // 2. Resolve image path
        $resolved = self::resolvePhysicalPath($cleanPath);

        if ($resolved) {
            self::$resolvedUrls[$cacheKey] = $resolved;
            // Cache found files for 7 days
            Cache::put($cacheKey, $resolved, now()->addDays(7));

            return $resolved;
        }

        return $defaultFallback;
    }

    /**
     * Resolve physical file location in storage/app/public, public/storage, or public/images.
     */
    private static function resolvePhysicalPath(string $cleanPath): ?string
    {
        // 1. Storage disk (Storage fake/real, storage/app/public/, or public/storage/)
        if (Storage::disk('public')->exists($cleanPath) || file_exists(storage_path('app/public/'.$cleanPath)) || file_exists(public_path('storage/'.$cleanPath))) {
            return asset('storage/'.$cleanPath);
        }

        // 2. Physical file in public/images/
        if (file_exists(public_path('images/'.$cleanPath))) {
            return asset('images/'.$cleanPath);
        }

        // 3. Alternate extensions in public/images/ (.jpg, .jpeg, .png, .webp, .svg)
        $pathWithoutExt = preg_replace('/\.[^.]+$/', '', $cleanPath);
        foreach (['jpg', 'jpeg', 'png', 'webp', 'svg'] as $ext) {
            $testPath = $pathWithoutExt.'.'.$ext;
            if (Storage::disk('public')->exists($testPath)) {
                return asset('storage/'.$testPath);
            }
            if (file_exists(public_path('images/'.$testPath))) {
                return asset('images/'.$testPath);
            }
            if (file_exists(storage_path('app/public/'.$testPath))) {
                return asset('storage/'.$testPath);
            }
        }

        // 4. Fallback checking subpath in public/images/ or storage
        if (! str_starts_with($cleanPath, 'treatments/') && ! str_starts_with($cleanPath, 'beauticians/')) {
            foreach (['treatments', 'beauticians', 'avatars'] as $folder) {
                $subPath = $folder.'/'.$cleanPath;
                if (Storage::disk('public')->exists($subPath) || file_exists(storage_path('app/public/'.$subPath)) || file_exists(public_path('storage/'.$subPath))) {
                    return asset('storage/'.$subPath);
                }
                if (file_exists(public_path('images/'.$subPath))) {
                    return asset('images/'.$subPath);
                }
                $subWithoutExt = preg_replace('/\.[^.]+$/', '', $subPath);
                foreach (['jpg', 'jpeg', 'png', 'webp', 'svg'] as $ext) {
                    if (Storage::disk('public')->exists($subWithoutExt.'.'.$ext) || file_exists(storage_path('app/public/'.$subWithoutExt.'.'.$ext))) {
                        return asset('storage/'.$subWithoutExt.'.'.$ext);
                    }
                    if (file_exists(public_path('images/'.$subWithoutExt.'.'.$ext))) {
                        return asset('images/'.$subWithoutExt.'.'.$ext);
                    }
                }
            }
        }

        // 5. Direct physical file in public/
        if (file_exists(public_path($cleanPath))) {
            return asset($cleanPath);
        }

        return null;
    }
}
