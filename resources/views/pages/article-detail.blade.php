@extends('layouts.app')

@section('title', $article->meta_title ?? $article->title . ' - Grapadi')
@section('description', $article->meta_description ?? $article->excerpt_display)

@section('content')
    {{-- Article Header --}}
    <article>
        <header class="bg-navy-brand py-16 lg:py-24">
            <div class="max-w-4xl mx-auto px-4 text-center">
                <nav class="text-sm mb-6">
                    <a href="{{ route('blog') }}" class="text-gray-400 hover:text-white transition">Blog</a>
                    @if($article->category)
                    <span class="text-gray-500 mx-2">/</span>
                    <a href="{{ route('blog', ['category' => $article->category->slug]) }}" class="text-primary hover:text-white transition">
                        {{ $article->category->category_name }}
                    </a>
                    @endif
                </nav>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold font-display text-white leading-tight mb-6">
                    {{ $article->title }}
                </h1>
                <div class="flex flex-wrap items-center justify-center gap-4 text-gray-400 text-sm">
                    @if($article->author)
                    <div class="flex items-center gap-2">
                        <span class="material-icons-outlined text-lg">person</span>
                        <span>{{ $article->author->name }}</span>
                    </div>
                    <span>•</span>
                    @endif
                    <div class="flex items-center gap-2">
                        <span class="material-icons-outlined text-lg">calendar_today</span>
                        <span>{{ $article->created_at->format('d F Y') }}</span>
                    </div>
                    <span>•</span>
                    <div class="flex items-center gap-2">
                        <span class="material-icons-outlined text-lg">schedule</span>
                        <span>{{ $article->reading_time_display }} min read</span>
                    </div>
                    <span>•</span>
                    <div class="flex items-center gap-2">
                        <span class="material-icons-outlined text-lg">visibility</span>
                        <span>{{ number_format($article->views_count) }} views</span>
                    </div>
                </div>
            </div>
        </header>

        {{-- Featured Image --}}
        <div class="relative -mt-8">
            <div class="max-w-4xl mx-auto px-4">
                <img src="{{ $article->image_display }}" alt="{{ $article->title }}" class="w-full rounded-xl shadow-xl object-cover max-h-[400px]">
            </div>
        </div>

        {{-- Article Content --}}
        <div class="py-12 bg-background-dark">
            <div class="max-w-7xl mx-auto px-4">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    {{-- Table of Contents (Sticky Sidebar) --}}
                    <aside class="lg:col-span-1 order-2 lg:order-1">
                        <div class="lg:sticky lg:top-24 space-y-6">
                            <x-table-of-contents :content="$article->content" />
                            
                            {{-- Share Section (Desktop) --}}
                            <div class="hidden lg:block bg-surface-dark rounded-xl p-6">
                                <h3 class="font-bold text-white mb-4 flex items-center gap-2">
                                    <span class="material-icons-outlined text-primary">share</span>
                                    Share
                                </h3>
                                <div class="flex flex-col gap-2">
                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($article->title) }}" target="_blank" rel="noopener" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-800 text-gray-400 hover:text-primary transition">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                        <span class="text-sm">Twitter</span>
                                    </a>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-800 text-gray-400 hover:text-primary-600 transition">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                        <span class="text-sm">Facebook</span>
                                    </a>
                                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($article->title) }}" target="_blank" rel="noopener" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-800 text-gray-400 hover:text-primary-700 transition">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                        <span class="text-sm">LinkedIn</span>
                                    </a>
                                    <a href="https://wa.me/?text={{ urlencode($article->title . ' ' . request()->url()) }}" target="_blank" rel="noopener" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-800 text-gray-400 hover:text-green-500 transition">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                        <span class="text-sm">WhatsApp</span>
                                    </a>
                                    <button onclick="copyToClipboard()" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-800 text-gray-400 hover:text-primary transition">
                                        <span class="material-icons-outlined">content_copy</span>
                                        <span class="text-sm" id="copyText">Copy Link</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </aside>

                    {{-- Main Content --}}
                    <div class="lg:col-span-3 order-1 lg:order-2">
                        {{-- Article Body --}}
                        <div class="prose prose-lg dark:prose-invert max-w-none prose-headings:font-display prose-a:text-primary hover:prose-a:text-red-700 prose-img:rounded-lg" id="article-content">
                            {!! $article->content !!}
                        </div>

                        {{-- Tags --}}
                        @if($article->tags->count() > 0)
                        <div class="mt-8 pt-8 border-t border-gray-700">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm text-gray-400 mr-2">Tags:</span>
                                @foreach($article->tags as $tag)
                                <a href="{{ route('blog', ['tag' => $tag->slug]) }}" 
                                   class="inline-block px-3 py-1 text-sm bg-gray-800 text-gray-400 hover:bg-primary hover:text-white rounded-full transition">
                                    {{ $tag->name }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Mobile Share Buttons --}}
                        <div class="lg:hidden mt-8 pt-8 border-t border-gray-700">
                            <p class="text-sm text-gray-400 mb-4">Share this article:</p>
                            <div class="flex flex-wrap gap-3">
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($article->title) }}" target="_blank" rel="noopener" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 text-gray-300 hover:bg-primary hover:text-white transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 text-gray-300 hover:bg-primary hover:text-white transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </a>
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($article->title) }}" target="_blank" rel="noopener" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 text-gray-300 hover:bg-primary hover:text-white transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                </a>
                                <a href="https://wa.me/?text={{ urlencode($article->title . ' ' . request()->url()) }}" target="_blank" rel="noopener" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 text-gray-300 hover:bg-green-500 hover:text-white transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                </a>
                                <button onclick="copyToClipboard()" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 text-gray-300 hover:bg-primary hover:text-white transition">
                                    <span class="material-icons-outlined">content_copy</span>
                                </button>
                            </div>
                        </div>

                        {{-- Author Bio --}}
                        @if($article->author)
                        <div class="mt-8">
                            <x-author-bio :author="$article->author" />
                        </div>
                        @endif

                        {{-- Previous/Next Navigation --}}
                        @if($previousArticle || $nextArticle)
                        <div class="mt-12 pt-8 border-t border-gray-700">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($previousArticle)
                                <a href="{{ route('blog.show', $previousArticle->slug) }}" class="group p-4 bg-surface-dark rounded-xl hover:bg-primary/5 transition">
                                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                                        <span class="material-icons-outlined text-sm">arrow_back</span>
                                        Previous Article
                                    </div>
                                    <h4 class="font-bold text-white group-hover:text-primary transition line-clamp-2">
                                        {{ $previousArticle->title }}
                                    </h4>
                                </a>
                                @else
                                <div></div>
                                @endif

                                @if($nextArticle)
                                <a href="{{ route('blog.show', $nextArticle->slug) }}" class="group p-4 bg-surface-dark rounded-xl hover:bg-primary/5 transition text-right">
                                    <div class="flex items-center justify-end gap-2 text-sm text-gray-500 mb-2">
                                        Next Article
                                        <span class="material-icons-outlined text-sm">arrow_forward</span>
                                    </div>
                                    <h4 class="font-bold text-white group-hover:text-primary transition line-clamp-2">
                                        {{ $nextArticle->title }}
                                    </h4>
                                </a>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </article>

    {{-- Related Articles --}}
    @if($relatedArticles->count() > 0)
    <section class="py-16 bg-surface-dark">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-8">
                Related Articles
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($relatedArticles as $relatedArticle)
                <x-article-card 
                    :title="$relatedArticle->title"
                    :image="$relatedArticle->image_display"
                    :date="$relatedArticle->created_at->format('F d, Y')"
                    :category="$relatedArticle->category->category_name ?? 'General'"
                    :categorySlug="$relatedArticle->category->slug ?? null"
                    :link="route('blog.show', $relatedArticle->slug)"
                    :excerpt="$relatedArticle->excerpt_display"
                    :readingTime="$relatedArticle->reading_time_display"
                />
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Newsletter CTA --}}
    <x-newsletter-form 
        title="Subscribe to Our Newsletter"
        description="Dapatkan insights dan research terbaru langsung ke inbox Anda."
        source="article"
    />

    @push('scripts')
    <script>
        function copyToClipboard() {
            navigator.clipboard.writeText(window.location.href).then(function() {
                const copyText = document.getElementById('copyText');
                if (copyText) {
                    copyText.textContent = 'Copied!';
                    setTimeout(() => {
                        copyText.textContent = 'Copy Link';
                    }, 2000);
                }
                // Show notification for mobile
                alert('Link copied to clipboard!');
            });
        }

        // Add IDs to headings for TOC navigation
        document.addEventListener('DOMContentLoaded', function() {
            const content = document.getElementById('article-content');
            if (content) {
                const headings = content.querySelectorAll('h2, h3');
                headings.forEach((heading, index) => {
                    const slug = heading.textContent.toLowerCase()
                        .replace(/[^a-z0-9]+/g, '-')
                        .replace(/(^-|-$)/g, '') + '-' + index;
                    heading.id = slug;
                });
            }
        });
    </script>
    @endpush
@endsection
