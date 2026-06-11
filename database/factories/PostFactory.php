<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(5);

        return [
            'post_category_id' => null,
            'slug' => Str::slug($title),
            'title' => ['de' => $title, 'fr' => $title, 'en' => $title],
            'excerpt' => ['de' => fake()->sentence(12)],
            'body' => ['de' => '<p>'.implode('</p><p>', fake()->paragraphs(3)).'</p>'],
            'is_published' => false,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'is_published' => true,
            'published_at' => fake()->dateTimeBetween('-3 months'),
        ]);
    }
}
