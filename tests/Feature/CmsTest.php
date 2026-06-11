<?php

use App\Models\Lead;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Redirect;
use Spatie\Activitylog\Models\Activity;

it('stores page content per locale and resolves the current locale', function () {
    $page = Page::factory()->create([
        'title' => ['de' => 'Über uns', 'fr' => 'À propos', 'en' => 'About us'],
        'blocks' => [
            'de' => [['type' => 'rich_text', 'data' => ['content' => '<p>Hallo</p>']]],
            'fr' => [['type' => 'rich_text', 'data' => ['content' => '<p>Bonjour</p>']]],
        ],
    ]);

    expect($page->getTranslation('title', 'fr'))->toBe('À propos')
        ->and($page->getTranslation('title', 'de'))->toBe('Über uns')
        ->and($page->getTranslation('blocks', 'fr')[0]['data']['content'])->toBe('<p>Bonjour</p>');

    app()->setLocale('en');
    expect($page->title)->toBe('About us');
});

it('publishes a multilingual page end to end', function () {
    $page = Page::factory()->create(['is_published' => false]);

    $page->update(['is_published' => true, 'published_at' => now()]);

    expect(Page::where('is_published', true)->whereKey($page->id)->exists())->toBeTrue();
});

it('orders menu items by sort order including nesting', function () {
    $menu = Menu::create(['key' => 'header', 'name' => 'Header']);
    $second = $menu->items()->create(['label' => ['de' => 'Zwei'], 'sort_order' => 2]);
    $first = $menu->items()->create(['label' => ['de' => 'Eins'], 'sort_order' => 1]);
    $child = $menu->items()->create(['label' => ['de' => 'Kind'], 'parent_id' => $first->id, 'sort_order' => 1]);

    expect($menu->items->first()->id)->toBe($first->id)
        ->and($first->children->first()->id)->toBe($child->id)
        ->and($child->getTranslation('label', 'de'))->toBe('Kind');
});

it('associates posts with translatable categories', function () {
    $category = PostCategory::factory()->create(['name' => ['de' => 'News', 'fr' => 'Actualités']]);
    $post = Post::factory()->published()->for($category, 'category')->create();

    expect($post->category->getTranslation('name', 'fr'))->toBe('Actualités')
        ->and($category->posts)->toHaveCount(1);
});

it('tracks lead status transitions', function () {
    $lead = Lead::factory()->create();

    expect($lead->status->value)->toBe('new');

    $lead->update(['status' => 'contacted']);

    expect($lead->fresh()->status->value)->toBe('contacted');
});

it('stores redirects with status codes and audits them', function () {
    $redirect = Redirect::create([
        'from_path' => '/alte-seite',
        'to_path' => '/de/neue-seite',
        'status_code' => 301,
    ]);

    expect($redirect->is_active)->toBeTrue()
        ->and(Activity::where('subject_type', Redirect::class)->exists())->toBeTrue();
});
