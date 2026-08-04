<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegacyBlogIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_hero_uses_latest_created_published_article(): void
    {
        $older = Article::create([
            'title' => 'Artikel Lama', 'slug' => 'artikel-lama', 'content' => 'Isi artikel lama',
            'is_published' => true, 'is_featured' => true, 'published_at' => now(),
        ]);
        $older->forceFill(['created_at' => now()->subDay(), 'updated_at' => now()->subDay()])->saveQuietly();

        $latest = Article::create([
            'title' => 'Artikel Terbaru', 'slug' => 'artikel-terbaru', 'content' => 'Isi artikel terbaru',
            'is_published' => true, 'is_featured' => false, 'published_at' => now()->subWeek(),
        ]);
        $latest->forceFill(['created_at' => now(), 'updated_at' => now()])->saveQuietly();

        $this->get(route('blog'))
            ->assertOk()
            ->assertSee('Latest Article')
            ->assertSeeInOrder([$latest->title, $older->title]);
    }
}
