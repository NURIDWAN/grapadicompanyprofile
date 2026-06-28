@props([
    'title' => 'Article Title',
    'image' => null,
    'date' => null,
    'link' => '#',
    'category' => null,
    'categorySlug' => null,
    'excerpt' => null,
    'readingTime' => null,
    'viewsCount' => null,
])

<article class="bg-surface-dark border border-border-dark rounded-xl overflow-hidden hover:border-primary/30 transition-all duration-300 group flex flex-col">
    <div class="h-48 overflow-hidden relative">
        @if($image)
        <img alt="{{ $title }}" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500" src="{{ $image }}" loading="lazy">
        @else
        <div class="w-full h-full bg-gray-700 flex items-center justify-center">
            <span class="material-icons-outlined text-4xl text-gray-400">article</span>
        </div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
        @if($category)
        @if($categorySlug)
        <a href="{{ route('blog', ['category' => $categorySlug]) }}" class="absolute top-3 left-3 bg-primary hover:bg-primary-800 text-white text-sm font-bold px-2 py-1 rounded transition">
            {{ $category }}
        </a>
        @else
        <span class="absolute top-3 left-3 bg-primary text-white text-sm font-bold px-2 py-1 rounded">{{ $category }}</span>
        @endif
        @endif
    </div>
    <div class="p-5 flex flex-col flex-grow">
        <a href="{{ $link }}">
            <h3 class="font-bold text-2xl mb-2 text-white line-clamp-2 group-hover:text-primary transition">
                {{ $title }}
            </h3>
        </a>
        
        @if($excerpt)
        <p class="text-lg text-gray-400 mb-4 line-clamp-2 flex-grow">
            {{ $excerpt }}
        </p>
        @endif

        <div class="flex items-center justify-between text-sm text-gray-500 mt-auto pt-4 border-t border-border-dark">
            <div class="flex items-center gap-3">
                @if($date)
                <span class="flex items-center gap-1">
                    <span class="material-icons-outlined text-base">calendar_today</span>
                    {{ $date }}
                </span>
                @endif
            </div>
            <div class="flex items-center gap-3">
                @if($readingTime)
                <span class="flex items-center gap-1">
                    <span class="material-icons-outlined text-base">schedule</span>
                    {{ $readingTime }} min
                </span>
                @endif
                @if($viewsCount !== null && $viewsCount > 0)
                <span class="flex items-center gap-1">
                    <span class="material-icons-outlined text-base">visibility</span>
                    {{ number_format($viewsCount) }}
                </span>
                @endif
            </div>
        </div>
    </div>
</article>
