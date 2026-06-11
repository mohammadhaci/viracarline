<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return $this->render('home');
    }

    public function show(string $locale, string $slug): View
    {
        return $this->render($slug);
    }

    private function render(string $slug): View
    {
        $page = Page::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('pages.show', [
            'page' => $page,
            'blocks' => $page->getTranslation('blocks', app()->getLocale()) ?? [],
            'seoTitle' => $page->getTranslation('seo_title', app()->getLocale()) ?: $page->title,
            'seoDescription' => $page->getTranslation('seo_description', app()->getLocale()),
        ]);
    }
}
