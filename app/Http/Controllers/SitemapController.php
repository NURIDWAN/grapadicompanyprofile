<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Asset;
use App\Models\Service;

class SitemapController extends Controller
{
    /**
     * Generate main sitemap index
     */
    public function index()
    {
        $content = $this->generateSitemapIndex();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate sitemap for static pages
     */
    public function pages()
    {
        $pages = [
            [
                'url' => url('/'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'url' => url('/about'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ],
            [
                'url' => url('/services'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ],
            [
                'url' => url('/portfolio'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'url' => url('/blog'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'url' => url('/timeline'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
            [
                'url' => url('/contact'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ],
        ];

        $content = $this->generateUrlSet($pages);

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate sitemap for blog articles
     */
    public function blog()
    {
        $articles = Article::published()
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($article) {
                return [
                    'url' => url('/blog/'.$article->slug),
                    'lastmod' => $article->updated_at->toDateString(),
                    'changefreq' => 'weekly',
                    'priority' => $article->is_featured ? '0.9' : '0.7',
                ];
            });

        $content = $this->generateUrlSet($articles->toArray());

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate sitemap for services
     */
    public function services()
    {
        $services = Service::all()
            ->map(function ($service) {
                return [
                    'url' => url('/services/'.$service->slug),
                    'lastmod' => $service->updated_at->toDateString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.8',
                ];
            });

        $content = $this->generateUrlSet($services->toArray());

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }

    public function assets()
    {
        $assets = Asset::publiclyListed()->orderBy('updated_at', 'desc')->get()->map(fn (Asset $asset) => [
            'url' => route('matching.show', $asset),
            'lastmod' => $asset->updated_at->toDateString(),
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ]);

        return response($this->generateUrlSet($assets->all()), 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Generate sitemap index XML
     */
    private function generateSitemapIndex(): string
    {
        $sitemaps = [
            url('/sitemap-pages.xml'),
            url('/sitemap-blog.xml'),
            url('/sitemap-services.xml'),
            url('/sitemap-assets.xml'),
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;

        foreach ($sitemaps as $sitemap) {
            $xml .= '  <sitemap>'.PHP_EOL;
            $xml .= '    <loc>'.htmlspecialchars($sitemap).'</loc>'.PHP_EOL;
            $xml .= '    <lastmod>'.now()->toDateString().'</lastmod>'.PHP_EOL;
            $xml .= '  </sitemap>'.PHP_EOL;
        }

        $xml .= '</sitemapindex>';

        return $xml;
    }

    /**
     * Generate URL set XML
     */
    private function generateUrlSet(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;

        foreach ($urls as $url) {
            $xml .= '  <url>'.PHP_EOL;
            $xml .= '    <loc>'.htmlspecialchars($url['url']).'</loc>'.PHP_EOL;
            $xml .= '    <lastmod>'.$url['lastmod'].'</lastmod>'.PHP_EOL;
            $xml .= '    <changefreq>'.$url['changefreq'].'</changefreq>'.PHP_EOL;
            $xml .= '    <priority>'.$url['priority'].'</priority>'.PHP_EOL;
            $xml .= '  </url>'.PHP_EOL;
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
