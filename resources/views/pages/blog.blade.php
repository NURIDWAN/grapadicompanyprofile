@extends('layouts.app')

@section('title', site_setting('page_title_blog', 'Blog & Insights - Grapadi'))
@section('description', 'Latest market intelligence and industry insights')

@section('content')
    {{-- Hero Section --}}
    <section class="bg-navy-brand py-12 sm:py-16 lg:py-20">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <span class="text-primary font-bold tracking-wider uppercase text-sm mb-2 block" data-animate="fade-in-up">Insights</span>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold font-display text-white leading-tight mb-4 sm:mb-6" data-animate="fade-in-up" data-delay="100">
                Blog & Market Intelligence
            </h1>
            <p class="text-base sm:text-lg text-gray-300 max-w-2xl mx-auto">
                Stay ahead with our latest research, analysis, and industry perspectives.
            </p>

            {{-- Search Bar --}}
            <div class="max-w-xl mx-auto mt-6 sm:mt-8">
                <form action="{{ route('blog') }}" method="GET" class="relative">
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ $search ?? '' }}"
                        placeholder="Search articles..." 
                        class="w-full px-4 sm:px-5 py-3 sm:py-4 pl-10 sm:pl-12 pr-24 sm:pr-28 rounded-full bg-white/10 border border-white/20 text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition text-sm sm:text-base"
                    >
                    <span class="material-icons-outlined absolute left-3 sm:left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg sm:text-xl">search</span>
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-primary hover:bg-primary-800 text-white font-bold px-4 sm:px-6 py-1.5 sm:py-2 rounded-full transition text-sm">
                        Search
                    </button>
                </form>
            </div>
        </div>
    </section>

    {{-- Active Filters --}}
    @if($search || $currentCategory || $currentTag)
    <section class="bg-surface-dark py-4 border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-sm text-gray-400">Active filters:</span>
                
                @if($search)
                <a href="{{ route('blog', array_filter(request()->except('q'))) }}" 
                   class="inline-flex items-center gap-1 bg-primary/10 text-primary px-3 py-1 rounded-full text-sm font-medium hover:bg-primary/20 transition">
                    <span>Search: "{{ $search }}"</span>
                    <span class="material-icons-outlined text-sm">close</span>
                </a>
                @endif

                @if($currentCategory)
                <a href="{{ route('blog', array_filter(request()->except('category'))) }}" 
                   class="inline-flex items-center gap-1 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-full text-sm font-medium hover:bg-primary-200 dark:hover:bg-primary-900/50 transition">
                    <span>Category: {{ $currentCategory->category_name }}</span>
                    <span class="material-icons-outlined text-sm">close</span>
                </a>
                @endif

                @if($currentTag)
                <a href="{{ route('blog', array_filter(request()->except('tag'))) }}" 
                   class="inline-flex items-center gap-1 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 px-3 py-1 rounded-full text-sm font-medium hover:bg-green-200 dark:hover:bg-green-900/50 transition">
                    <span>Tag: {{ $currentTag->name }}</span>
                    <span class="material-icons-outlined text-sm">close</span>
                </a>
                @endif

                <a href="{{ route('blog') }}" class="text-sm text-gray-500 hover:text-primary transition ml-2">
                    Clear all
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- Featured Article (only shown when no filters) --}}
    @if($featuredArticle && !$search && !$currentCategory && !$currentTag)
    <section class="py-16 bg-background-dark">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <div class="rounded-2xl overflow-hidden shadow-xl" data-animate="fade-in-left">
                    <img alt="{{ $featuredArticle->title }}" class="w-full h-80 object-cover" src="{{ $featuredArticle->image_display }}">
                </div>
                <div class="lg:pl-8" data-animate="fade-in-right" data-delay="200">
                    <span class="text-primary font-bold text-sm uppercase tracking-wider">Featured</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-white mt-4 mb-4">
                        {{ $featuredArticle->title }}
                    </h2>
                    <p class="text-gray-400 mb-6 leading-relaxed">
                        {{ $featuredArticle->excerpt_display }}
                    </p>
                    <div class="flex flex-wrap items-center gap-4 mb-6">
                        <span class="inline-flex items-center gap-1 text-sm text-gray-500">
                            <span class="material-icons-outlined text-sm">schedule</span>
                            {{ $featuredArticle->reading_time_display }} min read
                        </span>
                        <span class="text-sm text-gray-500">•</span>
                        <span class="text-sm text-gray-500">{{ $featuredArticle->created_at->format('F d, Y') }}</span>
                        @if($featuredArticle->category)
                        <span class="text-sm text-gray-500">•</span>
                        <a href="{{ route('blog', ['category' => $featuredArticle->category->slug]) }}" class="text-sm text-primary hover:underline">
                            {{ $featuredArticle->category->category_name }}
                        </a>
                        @endif
                    </div>
                    <a class="inline-flex items-center bg-primary hover:bg-primary-800 text-white font-bold py-3 px-6 rounded transition" href="{{ route('blog.show', $featuredArticle->slug) }}">
                        Read Article
                        <span class="material-icons-outlined text-sm ml-2">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Main Content with Sidebar --}}
    <section class="py-16 bg-surface-dark">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                {{-- Sidebar --}}
                <aside class="lg:col-span-1 order-2 lg:order-1">
                    {{-- Categories --}}
                    <div class="bg-background-dark rounded-xl shadow-sm p-6 mb-6" data-animate="fade-in-left">
                        <h3 class="font-bold text-white mb-4 flex items-center gap-2">
                            <span class="material-icons-outlined text-primary">folder</span>
                            Categories
                        </h3>
                        <ul class="space-y-2">
                            @foreach($categories as $category)
                            <li>
                                <a href="{{ route('blog', ['category' => $category->slug]) }}" 
                                   class="flex items-center justify-between py-2 px-3 rounded-lg text-sm {{ $currentCategory && $currentCategory->id === $category->id ? 'bg-primary/10 text-primary font-medium' : 'text-gray-400 hover:bg-gray-800' }} transition">
                                    <span>{{ $category->category_name }}</span>
                                    <span class="bg-gray-800 text-gray-500 text-xs px-2 py-0.5 rounded-full">
                                        {{ $category->articles_count }}
                                    </span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Popular Tags --}}
                    @if($popularTags->count() > 0)
                    <div class="bg-background-dark rounded-xl shadow-sm p-6" data-animate="fade-in-left" data-delay="100">
                        <h3 class="font-bold text-white mb-4 flex items-center gap-2">
                            <span class="material-icons-outlined text-primary">label</span>
                            Popular Tags
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($popularTags as $tag)
                            <a href="{{ route('blog', ['tag' => $tag->slug]) }}" 
                               class="inline-block px-3 py-1 text-sm rounded-full {{ $currentTag && $currentTag->id === $tag->id ? 'bg-primary text-white' : 'bg-gray-800 text-gray-400 hover:bg-primary hover:text-white' }} transition">
                                {{ $tag->name }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </aside>

                {{-- Articles Grid --}}
                <div class="lg:col-span-3 order-1 lg:order-2">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-2xl font-bold text-white">
                            @if($search)
                                Search Results for "{{ $search }}"
                            @elseif($currentCategory)
                                {{ $currentCategory->category_name }}
                            @elseif($currentTag)
                                Tag: {{ $currentTag->name }}
                            @else
                                Latest Articles
                            @endif
                        </h2>
                        <span class="text-sm text-gray-500">{{ $articles->total() }} articles</span>
                    </div>

                    @if($articles->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($articles as $article)
                        <x-article-card 
                            :title="$article->title"
                            :image="$article->image_display"
                            :date="$article->created_at->format('F d, Y')"
                            :category="$article->category->category_name ?? 'General'"
                            :categorySlug="$article->category->slug ?? null"
                            :link="route('blog.show', $article->slug)"
                            :excerpt="$article->excerpt_display"
                            :readingTime="$article->reading_time_display"
                            :viewsCount="$article->views_count"
                        />
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($articles->hasPages())
                    <div class="mt-12">
                        {{ $articles->links() }}
                    </div>
                    @endif
                    @else
                    <div class="text-center py-16">
                        <span class="material-icons-outlined text-6xl text-gray-600 mb-4">article</span>
                        <h3 class="text-xl font-bold text-white mb-2">No articles found</h3>
                        <p class="text-gray-400 mb-6">
                            @if($search)
                                Try different keywords or browse all articles.
                            @else
                                Check back later for new content.
                            @endif
                        </p>
                        <a href="{{ route('blog') }}" class="inline-flex items-center bg-primary hover:bg-primary-800 text-white font-bold py-2 px-4 rounded transition">
                            View All Articles
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Newsletter CTA --}}
    <x-newsletter-form 
        title="Subscribe to Our Newsletter"
        description="Get the latest insights and research delivered directly to your inbox."
        source="blog"
    />
@endsection
