@props([
    'icon' => 'analytics',
    'title' => 'Service Title',
    'description' => '',
    'link' => '#',
    'linkText' => 'Explore',
])

<div class="bg-surface-dark border border-border-dark rounded-xl p-6 hover:border-primary/30 transition-colors duration-300 group flex flex-col">
    <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mb-5">
        <span class="material-icons-outlined text-2xl text-primary">{{ $icon }}</span>
    </div>

    <h3 class="text-lg font-display font-bold text-white mb-3">{{ $title }}</h3>

    @if ($description)
        <p class="text-sm text-gray-400 mb-5 leading-relaxed flex-1">
            {!! Str::limit(strip_tags($description), 100) !!}
        </p>
    @endif

    <a href="{{ $link }}"
       class="text-primary text-sm font-semibold flex items-center gap-2 group-hover:gap-3 transition-all min-h-[44px] focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-surface-dark rounded mt-auto">
        {{ $linkText }}
        <span class="material-icons-outlined text-sm">arrow_forward</span>
    </a>
</div>
