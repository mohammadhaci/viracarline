<?php

namespace App\Http\Controllers;

use App\Enums\VehicleStatus;
use App\Models\Page;
use App\Models\Post;
use App\Models\Vehicle;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $locales = config('locales.supported');
        $urls = [];

        foreach (Page::where('is_published', true)->get() as $page) {
            foreach ($locales as $locale) {
                $urls[] = [
                    'loc' => $page->slug === 'home' ? url($locale) : url("{$locale}/{$page->slug}"),
                    'lastmod' => $page->updated_at->toAtomString(),
                ];
            }
        }

        foreach ($locales as $locale) {
            $urls[] = ['loc' => url("{$locale}/fahrzeuge"), 'lastmod' => now()->toAtomString()];
            $urls[] = ['loc' => url("{$locale}/blog"), 'lastmod' => now()->toAtomString()];
        }

        foreach (Vehicle::where('is_published', true)->whereIn('status', [VehicleStatus::Listed, VehicleStatus::Reserved])->get() as $vehicle) {
            foreach ($locales as $locale) {
                $urls[] = [
                    'loc' => url("{$locale}/fahrzeuge/{$vehicle->slug}"),
                    'lastmod' => $vehicle->updated_at->toAtomString(),
                ];
            }
        }

        foreach (Post::where('is_published', true)->get() as $post) {
            foreach ($locales as $locale) {
                $urls[] = [
                    'loc' => url("{$locale}/blog/{$post->slug}"),
                    'lastmod' => $post->updated_at->toAtomString(),
                ];
            }
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
