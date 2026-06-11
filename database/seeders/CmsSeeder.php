<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class CmsSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'staging', 'testing')) {
            return;
        }

        $home = $this->seedHomePage();
        $this->seedStaticPages();
        $this->seedMenus($home);

        $category = PostCategory::factory()->create([
            'slug' => 'news',
            'name' => ['de' => 'News', 'fr' => 'Actualités', 'en' => 'News'],
        ]);
        Post::factory(3)->published()->for($category, 'category')->create();

        Lead::factory(4)->create();
        Lead::factory(3)->vehicleInquiry()
            ->state(fn () => ['vehicle_id' => Vehicle::inRandomOrder()->value('id')])
            ->create();
    }

    private function seedHomePage(): Page
    {
        return Page::firstOrCreate(
            ['slug' => 'home'],
            [
                'template' => 'home',
                'title' => [
                    'de' => 'Willkommen bei Vira Car Lines',
                    'fr' => 'Bienvenue chez Vira Car Lines',
                    'en' => 'Welcome to Vira Car Lines',
                ],
                'blocks' => [
                    'de' => [
                        ['type' => 'hero', 'data' => [
                            'heading' => 'Ihr Partner für An- und Verkauf von Fahrzeugen',
                            'subheading' => 'Faire Preise. Geprüfte Qualität. Eigene Werkstatt.',
                            'cta_label' => 'Fahrzeuge ansehen',
                            'cta_url' => '/de/fahrzeuge',
                        ]],
                        ['type' => 'car_grid', 'data' => ['heading' => 'Aktuelle Fahrzeuge', 'limit' => 6, 'featured_only' => true]],
                        ['type' => 'services_grid', 'data' => [
                            'heading' => 'Unsere Leistungen',
                            'services' => [
                                ['icon' => 'heroicon-o-truck', 'title' => 'Ankauf', 'text' => 'Wir kaufen Ihr Auto zu fairen Konditionen.'],
                                ['icon' => 'heroicon-o-key', 'title' => 'Verkauf', 'text' => 'Geprüfte Occasionen mit Garantie.'],
                                ['icon' => 'heroicon-o-wrench-screwdriver', 'title' => 'Werkstatt', 'text' => 'Reparatur und Service aller Marken.'],
                            ],
                        ]],
                        ['type' => 'cta_banner', 'data' => [
                            'heading' => 'Auto zu verkaufen?',
                            'text' => 'Wir machen Ihnen ein Angebot innert 24 Stunden.',
                            'cta_label' => 'Jetzt anfragen',
                            'cta_url' => '/de/ankauf',
                        ]],
                    ],
                ],
                'is_published' => true,
                'published_at' => now(),
            ],
        );
    }

    private function seedStaticPages(): void
    {
        $pages = [
            'ankauf' => [
                'title' => ['de' => 'Wir kaufen Ihr Auto', 'fr' => 'Nous rachetons votre voiture', 'en' => 'We buy your car'],
                'blocks' => fn (string $title) => [
                    ['type' => 'contact_form', 'data' => [
                        'heading' => $title,
                        'intro' => 'Beschreiben Sie Ihr Fahrzeug und laden Sie Fotos hoch — wir melden uns innert 24 Stunden mit einem Angebot.',
                        'with_photos' => true,
                    ]],
                ],
            ],
            'werkstatt' => [
                'title' => ['de' => 'Werkstatt & Services', 'fr' => 'Atelier & services', 'en' => 'Workshop & services'],
                'blocks' => fn (string $title) => [
                    ['type' => 'services_grid', 'data' => [
                        'heading' => $title,
                        'services' => [
                            ['title' => 'Service & Wartung', 'text' => 'Herstellerkonforme Wartung für alle Marken.'],
                            ['title' => 'Reparaturen', 'text' => 'Mechanik, Elektronik und Karosserie.'],
                            ['title' => 'Reifenservice', 'text' => 'Wechsel, Einlagerung und Verkauf.'],
                        ],
                    ]],
                    ['type' => 'cta_banner', 'data' => ['heading' => 'Termin vereinbaren', 'cta_label' => 'Kontakt', 'cta_url' => '/de/kontakt']],
                ],
            ],
            'ueber-uns' => [
                'title' => ['de' => 'Über uns', 'fr' => 'À propos', 'en' => 'About us'],
                'blocks' => fn (string $title) => [
                    ['type' => 'rich_text', 'data' => ['content' => "<h1>{$title}</h1><p>Vira Car Lines AG kauft, verkauft und repariert Fahrzeuge in der Schweiz.</p>"]],
                ],
            ],
            'kontakt' => [
                'title' => ['de' => 'Kontakt', 'fr' => 'Contact', 'en' => 'Contact'],
                'blocks' => fn (string $title) => [
                    ['type' => 'contact_form', 'data' => ['heading' => $title]],
                ],
            ],
            'impressum' => [
                'title' => ['de' => 'Impressum', 'fr' => 'Mentions légales', 'en' => 'Imprint'],
                'blocks' => fn (string $title) => [
                    ['type' => 'rich_text', 'data' => ['content' => "<h1>{$title}</h1><p>Vira Car Lines AG — Angaben folgen.</p>"]],
                ],
            ],
            'datenschutz' => [
                'title' => ['de' => 'Datenschutzerklärung', 'fr' => 'Protection des données', 'en' => 'Privacy policy'],
                'blocks' => fn (string $title) => [
                    ['type' => 'rich_text', 'data' => ['content' => "<h1>{$title}</h1><p>Datenschutzerklärung nach revDSG — Inhalt folgt.</p>"]],
                ],
            ],
            'agb' => [
                'title' => ['de' => 'AGB', 'fr' => 'CG', 'en' => 'Terms'],
                'blocks' => fn (string $title) => [
                    ['type' => 'rich_text', 'data' => ['content' => "<h1>{$title}</h1><p>Allgemeine Geschäftsbedingungen — Inhalt folgt.</p>"]],
                ],
            ],
        ];

        foreach ($pages as $slug => $definition) {
            Page::firstOrCreate(
                ['slug' => $slug],
                [
                    'template' => 'default',
                    'title' => $definition['title'],
                    'blocks' => collect($definition['title'])
                        ->map(fn (string $title) => ($definition['blocks'])($title))
                        ->all(),
                    'is_published' => true,
                    'published_at' => now(),
                ],
            );
        }
    }

    private function seedMenus(Page $home): void
    {
        $header = Menu::firstOrCreate(['key' => 'header'], ['name' => 'Hauptnavigation']);

        if ($header->items()->doesntExist()) {
            $header->items()->createMany([
                ['label' => ['de' => 'Home', 'fr' => 'Accueil', 'en' => 'Home'], 'page_id' => $home->id, 'sort_order' => 1],
                ['label' => ['de' => 'Fahrzeuge', 'fr' => 'Véhicules', 'en' => 'Vehicles'], 'url' => '/fahrzeuge', 'sort_order' => 2],
                ['label' => ['de' => 'Ankauf', 'fr' => 'Rachat', 'en' => 'We buy'], 'url' => '/ankauf', 'sort_order' => 3],
                ['label' => ['de' => 'Werkstatt', 'fr' => 'Atelier', 'en' => 'Workshop'], 'url' => '/werkstatt', 'sort_order' => 4],
                ['label' => ['de' => 'Kontakt', 'fr' => 'Contact', 'en' => 'Contact'], 'url' => '/kontakt', 'sort_order' => 5],
            ]);
        }

        $footer = Menu::firstOrCreate(['key' => 'footer'], ['name' => 'Footer']);

        if ($footer->items()->doesntExist()) {
            $footer->items()->createMany([
                ['label' => ['de' => 'Impressum', 'fr' => 'Mentions légales', 'en' => 'Imprint'], 'url' => '/impressum', 'sort_order' => 1],
                ['label' => ['de' => 'Datenschutz', 'fr' => 'Protection des données', 'en' => 'Privacy'], 'url' => '/datenschutz', 'sort_order' => 2],
                ['label' => ['de' => 'AGB', 'fr' => 'CG', 'en' => 'Terms'], 'url' => '/agb', 'sort_order' => 3],
            ]);
        }
    }
}
