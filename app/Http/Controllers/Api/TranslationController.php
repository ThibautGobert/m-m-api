<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class TranslationController extends Controller
{
    public function get(Request $request, string $locale, string $concept)
    {
        $lastUpdated = $this->getLastUpdated($locale, $concept);
        $cacheKey = "translations:{$locale}:{$concept}:{$lastUpdated}";
        $langDir = lang_path($locale);
        $conceptPath = "{$langDir}/{$concept}.php";
        $partials = config('app.lang_partials');

        $translations = Cache::remember($cacheKey, 3600, function () use ($conceptPath, $partials, $langDir) {
            $merged = require $conceptPath;

            foreach ($partials as $partial) {
                $partialPath = "{$langDir}/{$partial}.php";
                if (File::exists($partialPath)) {
                    $merged = array_merge_recursive($merged, require $partialPath);
                }
            }

            return $merged;
        });

        return response()->json([
            'translations' => $translations,
            'updated_at' => $lastUpdated,
        ])->header('Cache-Control', 'public, max-age=600');
    }

    public function version(string $locale, string $concept)
    {
        $lastUpdated = $this->getLastUpdated($locale, $concept);

        return response()->json([
            'updated_at' => $lastUpdated,
        ]);
    }

    private function getLastUpdated(string $locale, string $concept): string
    {
        if (!preg_match('/^[a-z0-9_\-]+$/i', $locale) || !preg_match('/^[a-z0-9_\-]+$/i', $concept)) {
            return response()->json(['error' => 'Invalid input'], 400);
        }

        $langDir = lang_path($locale);
        $conceptPath = "{$langDir}/{$concept}.php";
        $partials = config('app.lang_partials');

        if (!File::exists($conceptPath)) {
            return response()->json(['error' => 'Concept not found'], 404);
        }

        $allPaths = [$conceptPath];
        foreach ($partials as $partial) {
            $partialPath = "{$langDir}/{$partial}.php";
            if (File::exists($partialPath)) {
                $allPaths[] = $partialPath;
            }
        }

        return collect($allPaths)->map(fn ($p) => File::lastModified($p))->max();
    }

    public function status(string $locale)
    {
        $path = lang_path($locale);

        if (!is_dir($path)) {
            abort(404, "Lang directory not found for locale {$locale}");
        }

        $concepts = collect(File::files($path))
            ->filter(fn($file) => str_starts_with($file->getFilename(), 'page_'))
            ->mapWithKeys(function ($file) {
                $conceptKey = str_replace('.php', '', $file->getFilename());
                return [$conceptKey => filemtime($file->getRealPath())];
            });

        return response()->json($concepts);
    }
}
