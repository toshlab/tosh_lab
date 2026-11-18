<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'slug' => fake()->unique()->slug(3),
            'category' => null,
            'tags' => null,
            'excerpt' => null,
            'body' => fake()->paragraphs(2, true),
            'published_at' => now()->subDay(),
        ];
    }

    /** content/posts/ のファイルから取り込んだ記事 */
    public function fromFile(?string $path = null): static
    {
        return $this->state(fn (array $attributes) => [
            'source' => Post::SOURCE_FILE,
            'source_path' => $path ?? now()->format('Y-m-d').'-'.$attributes['slug'].'.md',
            'source_hash' => hash('sha256', $attributes['body']),
        ]);
    }

    public function draft(): static
    {
        return $this->state(['published_at' => null]);
    }

    public function scheduled(): static
    {
        return $this->state(['published_at' => now()->addDay()]);
    }
}
