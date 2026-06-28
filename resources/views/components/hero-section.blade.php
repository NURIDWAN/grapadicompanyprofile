@props([
    'headline' => null,
    'subheadline' => null,
    'primaryCtaText' => null,
    'primaryCtaUrl' => '/contact',
    'secondaryCtaText' => null,
    'secondaryCtaUrl' => '/portfolio',
    'backgroundImage' => null,
    'stats' => [],
    // Legacy prop support (backward-compatible with existing usage)
    'title' => null,
    'subtitle' => null,
    'ctaText' => null,
    'ctaUrl' => null,
    'showLogo' => false,
])

@php
    // Fixed headline/subheadline for the new design - ignore legacy title/subtitle
    $displayHeadline = $headline ?? 'Strategic Advisory for <span class="text-gold-gradient">High-Impact Decisions</span>.';
    $displaySubheadline = $subheadline ?? 'Kami membantu investor, developer, dan korporasi membuat keputusan bisnis dan investasi yang lebih tepat melalui analisis mendalam, data akurat, dan insight strategis yang independen.';
    $displayPrimaryCtaText = $primaryCtaText ?? 'Schedule Strategy Session';
    $displayPrimaryCtaUrl = $primaryCtaUrl ?? '/contact';
    $displaySecondaryCtaText = $secondaryCtaText ?? 'View Case Studies';
    $displaySecondaryCtaUrl = $secondaryCtaUrl ?? '/portfolio';

    // Resolve background image
    $bgImage = $backgroundImage ?? asset('image/background/image.png');
@endphp

<section class="relative bg-background-dark overflow-hidden">
    {{-- Right side background image --}}
    <div class="absolute inset-y-0 right-0 w-full lg:w-[55%]">
        <img
            src="{{ $bgImage }}"
            alt=""
            aria-hidden="true"
            class="absolute inset-0 w-full h-full object-cover opacity-80"
            fetchpriority="high"
            loading="eager"
        >
        {{-- Gradient overlays for blending into dark left side --}}
        <div class="absolute inset-0 bg-gradient-to-r from-background-dark via-background-dark/80 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-background-dark via-transparent to-background-dark/40"></div>
    </div>

    {{-- Content wrapper --}}
    <div class="relative z-10 w-full max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
        {{-- Top section: headline + CTA --}}
        <div class="pt-24 lg:pt-32 pb-12">
            <div class="max-w-xl">
                {{-- Headline: serif font, gold gradient text --}}
                <h1 class="text-[2.5rem] sm:text-[3rem] md:text-[3.5rem] lg:text-[4rem] font-display font-bold text-white leading-[1.1] mb-8">
                    {!! $displayHeadline !!}
                </h1>

                {{-- Subheadline: muted gray body text --}}
                @if($displaySubheadline)
                <p class="text-base md:text-lg text-gray-400 mb-8 max-w-lg leading-relaxed">
                    {{ $displaySubheadline }}
                </p>
                @endif

                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4 items-start">
                    @if($displayPrimaryCtaText)
                    <a
                        href="{{ $displayPrimaryCtaUrl }}"
                        class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-400 text-background-dark font-bold py-4 px-8 rounded-lg transition-colors duration-200 text-base min-h-[44px] min-w-[44px] focus:outline-none focus:ring-2 focus:ring-primary-300 focus:ring-offset-2 focus:ring-offset-background-dark"
                    >
                        {{ $displayPrimaryCtaText }}
                        <span class="material-icons-outlined text-lg">arrow_forward</span>
                    </a>
                    @endif

                    @if($displaySecondaryCtaText)
                    <a
                        href="{{ $displaySecondaryCtaUrl }}"
                        class="inline-flex items-center justify-center gap-2 border-2 border-primary text-primary hover:bg-primary/10 font-bold py-4 px-8 rounded-lg transition-colors duration-200 text-base min-h-[44px] min-w-[44px] focus:outline-none focus:ring-2 focus:ring-primary-300 focus:ring-offset-2 focus:ring-offset-background-dark"
                    >
                        {{ $displaySecondaryCtaText }}
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        @if(is_array($stats) && count($stats) > 0)
        <div class="pb-8 lg:pb-10">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($stats as $stat)
                <div class="border border-border-dark rounded-xl p-4 text-center bg-surface-dark/60 backdrop-blur-sm">
                    @if(isset($stat['icon']))
                    <span class="material-icons-outlined text-2xl text-primary mb-2 block">{{ $stat['icon'] }}</span>
                    @endif
                    @if(isset($stat['number']))
                    <p class="text-2xl md:text-3xl font-bold font-display text-white mb-1">{{ $stat['number'] }}</p>
                    @endif
                    @if(isset($stat['label']))
                    <p class="text-sm font-semibold text-primary mb-2">{{ $stat['label'] }}</p>
                    @endif
                    @if(isset($stat['description']))
                    <p class="text-xs text-gray-500 leading-relaxed">{{ $stat['description'] }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
