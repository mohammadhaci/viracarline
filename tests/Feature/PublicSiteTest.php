<?php

use App\Models\Lead;
use App\Models\Page;
use App\Models\Post;
use App\Models\Redirect;
use App\Models\Setting;
use App\Models\Vehicle;
use Database\Seeders\CmsSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(CmsSeeder::class);
});

it('redirects the root url to the default locale', function () {
    $this->get('/')->assertRedirect('/de');
});

it('renders the home page in every supported locale', function (string $locale) {
    $this->get("/{$locale}")
        ->assertOk()
        ->assertSee('Vira Car Lines');
})->with(['de', 'fr', 'en']);

it('returns 404 for unsupported locales', function () {
    $this->get('/it')->assertNotFound();
});

it('renders hreflang alternates on public pages', function () {
    $this->get('/de')
        ->assertOk()
        ->assertSee('hreflang="de"', false)
        ->assertSee('hreflang="fr"', false)
        ->assertSee('hreflang="en"', false);
});

it('renders a published CMS page with its blocks', function () {
    $this->get('/de/ueber-uns')
        ->assertOk()
        ->assertSee('Über uns');
});

it('returns 404 for unpublished pages', function () {
    Page::factory()->create(['slug' => 'entwurf', 'is_published' => false]);

    $this->get('/de/entwurf')->assertNotFound();
});

it('lists published vehicles with filters', function () {
    Vehicle::factory()->listed()->create(['brand' => 'BMW', 'title' => ['de' => 'BMW Testwagen']]);
    Vehicle::factory()->listed()->create(['brand' => 'Audi', 'title' => ['de' => 'Audi Testwagen']]);

    $this->get('/de/fahrzeuge')->assertOk()->assertSee('BMW Testwagen')->assertSee('Audi Testwagen');
    $this->get('/de/fahrzeuge?brand=BMW')->assertOk()->assertSee('BMW Testwagen')->assertDontSee('Audi Testwagen');
});

it('hides unpublished vehicles from the public listing', function () {
    Vehicle::factory()->create(['title' => ['de' => 'Geheimer Wagen']]);

    $this->get('/de/fahrzeuge')->assertOk()->assertDontSee('Geheimer Wagen');
});

it('shows a vehicle detail page with price or price on request', function () {
    $vehicle = Vehicle::factory()->listed()->create([
        'asking_price' => '25900.00',
        'show_price' => true,
    ]);

    $this->get("/de/fahrzeuge/{$vehicle->slug}")
        ->assertOk()
        ->assertSee("CHF 25'900.00");

    $vehicle->update(['show_price' => false]);

    $this->get("/de/fahrzeuge/{$vehicle->slug}")
        ->assertOk()
        ->assertSee('Preis auf Anfrage');
});

it('embeds schema.org Car data on the vehicle detail page', function () {
    $vehicle = Vehicle::factory()->listed()->create();

    $this->get("/de/fahrzeuge/{$vehicle->slug}")
        ->assertOk()
        ->assertSee('"@type":"Car"', false);
});

it('stores a vehicle inquiry as a lead', function () {
    $vehicle = Vehicle::factory()->listed()->create();

    $this->post('/de/anfrage', [
        'type' => 'vehicle_inquiry',
        'vehicle_id' => $vehicle->id,
        'name' => 'Hans Muster',
        'email' => 'hans@example.ch',
        'message' => 'Ist das Fahrzeug noch verfügbar?',
    ])->assertRedirect();

    expect(Lead::where('email', 'hans@example.ch')->where('vehicle_id', $vehicle->id)->exists())->toBeTrue();
});

it('silently drops honeypot submissions', function () {
    $this->post('/de/anfrage', [
        'type' => 'contact',
        'name' => 'Bot',
        'email' => 'bot@example.com',
        'message' => 'spam',
        'website' => 'https://spam.example',
    ])->assertRedirect();

    expect(Lead::count())->toBe(7); // only the seeded leads
});

it('stores ankauf photos on the private disk', function () {
    Storage::fake('local');

    $this->post('/de/anfrage', [
        'type' => 'contact',
        'name' => 'Verkäufer',
        'email' => 'seller@example.ch',
        'message' => 'Mein Auto zu verkaufen',
        'photos' => [UploadedFile::fake()->image('auto.jpg')],
    ])->assertRedirect();

    $lead = Lead::where('email', 'seller@example.ch')->first();

    expect($lead->getMedia('photos'))->toHaveCount(1)
        ->and($lead->getFirstMedia('photos')->disk)->toBe('local');
});

it('applies active redirects with their status code', function () {
    Redirect::create(['from_path' => '/alte-url', 'to_path' => '/de/ueber-uns', 'status_code' => 301]);

    $this->get('/alte-url')->assertRedirect('/de/ueber-uns')->assertStatus(301);
});

it('ignores inactive redirects', function () {
    Redirect::create(['from_path' => '/de', 'to_path' => '/en', 'is_active' => false]);

    $this->get('/de')->assertOk();
});

it('serves the public site in maintenance mode only when disabled', function () {
    Setting::set('site_maintenance_mode', true);

    $this->get('/de')->assertStatus(503);

    Setting::set('site_maintenance_mode', false);

    $this->get('/de')->assertOk();
});

it('keeps the admin panel reachable during maintenance mode', function () {
    Setting::set('site_maintenance_mode', true);

    $this->get('/admin/login')->assertOk();
});

it('renders the blog index and detail pages', function () {
    $post = Post::where('is_published', true)->first();

    $this->get('/de/blog')->assertOk()->assertSee(e($post->getTranslation('title', 'de')), false);
    $this->get("/de/blog/{$post->slug}")->assertOk();
});

it('generates a sitemap covering pages, vehicles, and posts in all locales', function () {
    $vehicle = Vehicle::factory()->listed()->create();

    $response = $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml');

    foreach (['de', 'fr', 'en'] as $locale) {
        $response->assertSee(url("{$locale}/fahrzeuge/{$vehicle->slug}"), false);
        $response->assertSee(url($locale), false);
    }
});
