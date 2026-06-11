<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'slug' => Str::slug($title),
            'template' => 'default',
            'title' => [
                'de' => Str::title($title),
                'fr' => Str::title($title).' (FR)',
                'en' => Str::title($title).' (EN)',
            ],
            'blocks' => [
                'de' => [
                    [
                        'type' => 'rich_text',
                        'data' => ['content' => '<p>'.fake()->paragraph().'</p>'],
                    ],
                ],
            ],
            'seo_title' => null,
            'seo_description' => null,
            'is_published' => false,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'is_published' => true,
            'published_at' => now(),
        ]);
    }
}
