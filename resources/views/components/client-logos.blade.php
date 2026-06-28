@props([
    'brands' => collect([]),
    'title' => 'Trusted By',
    'direction' => 'left',
    'speed' => 5,
    'rows' => 1,
    'showCard' => false,
    'cardNumber' => '100++',
    'cardTitle' => 'Trusted Clients',
    'cardDescription' => 'Lebih dari 100 perusahaan dan institusi publik telah mempercayakan strategi dan transformasi bisnisnya kepada kami.',
    'cardCtaText' => "Let's Work Together",
    'cardCtaUrl' => '/contact',
])

@php
    // Default logos to show when no brands in database
    $defaultLogos = [
        ['name' => 'Google', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/2/2f/Google_2015_logo.svg', 'height' => 'h-8'],
        ['name' => 'Microsoft', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/9/96/Microsoft_logo_%282012%29.svg', 'height' => 'h-6'],
        ['name' => 'Amazon', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/a/a9/Amazon_logo.svg', 'height' => 'h-7'],
        ['name' => 'Meta', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/7/7b/Meta_Platforms_Inc._logo.svg', 'height' => 'h-6'],
        ['name' => 'Apple', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg', 'height' => 'h-8'],
        ['name' => 'Netflix', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/0/08/Netflix_2015_logo.svg', 'height' => 'h-6'],
    ];
    
    $hasBrands = $brands && $brands->count() > 0;
    
    // Calculate repetition to ensure seamless scroll even on wide screens
    $minItems = 12;
    $itemCount = $hasBrands ? $brands->count() : count($defaultLogos);
    $repeatCount = $direction === 'static' ? 1 : ($itemCount > 0 ? ceil($minItems / $itemCount) : 1);
    
    // Helper function to get logo URL
    $getLogoUrl = function($brand) {
        if (!empty($brand->logo)) {
            if (str_starts_with($brand->logo, 'http')) {
                return $brand->logo;
            }
            return asset('storage/' . $brand->logo);
        }
        return null;
    };

    // Determine grid columns based on whether card is shown
    $gridCols = $showCard ? 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-6' : 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-6';
@endphp

<div class="bg-background-dark py-12 border-b border-border-dark overflow-hidden relative">
    {{-- Gradient Overlays --}}
    <div class="absolute inset-y-0 left-0 w-24 md:w-48 bg-gradient-to-r from-background-dark via-background-dark/90 to-transparent z-10 pointer-events-none"></div>
    @if(!$showCard)
    <div class="absolute inset-y-0 right-0 w-24 md:w-48 bg-gradient-to-l from-background-dark via-background-dark/90 to-transparent z-10 pointer-events-none"></div>
    @endif

    {{-- Title --}}
    @if($title)
    <p class="text-center text-sm text-gray-400 mb-8 uppercase tracking-wider font-medium font-display relative z-10">{{ $title }}</p>
    @endif
    
    {{-- Row 1: Scroll Left --}}
    <div class="relative {{ $direction === 'static' ? '' : 'overflow-hidden' }} mb-4">
        @if($direction === 'static')
        {{-- Static Layout with optional card --}}
        <div class="flex flex-col lg:flex-row gap-8 px-6 sm:px-8 lg:px-12 max-w-6xl mx-auto">
            {{-- Logos Grid --}}
            <div class="{{ $showCard ? 'lg:w-[65%]' : 'w-full' }}">
                <div class="{{ $showCard ? 'grid ' . $gridCols . ' gap-6 sm:gap-8 lg:gap-10' : 'flex flex-wrap justify-center gap-6 sm:gap-8 lg:gap-10' }}">
                    @if($hasBrands)
                        @foreach($brands as $brand)
                            @php $logoUrl = $getLogoUrl($brand); @endphp
                            @if($logoUrl)
                                @if($brand->url)
                                    <a href="{{ $brand->url }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center overflow-hidden rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background-dark" style="height:90px;{{ $showCard ? 'width:100%' : 'width:130px' }};">
                                        <img 
                                            alt="{{ $brand->name }}" 
                                            class="object-contain opacity-60 grayscale brightness-200 hover:grayscale-0 hover:opacity-100 hover:brightness-100 transition-all duration-300" 
                                            src="{{ $logoUrl }}"
                                            loading="lazy"
                                            style="max-width:100%;max-height:70px;"
                                        >
                                    </a>
                                @else
                                    <div class="flex items-center justify-center overflow-hidden rounded-lg p-3" style="height:90px;{{ $showCard ? 'width:100%' : 'width:130px' }};">
                                        <img 
                                            alt="{{ $brand->name }}" 
                                            class="object-contain opacity-60 grayscale brightness-200 hover:grayscale-0 hover:opacity-100 hover:brightness-100 transition-all duration-300" 
                                            src="{{ $logoUrl }}"
                                            loading="lazy"
                                            style="max-width:100%;max-height:70px;"
                                        >
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    @else
                        @foreach($defaultLogos as $logo)
                            <div class="flex items-center justify-center overflow-hidden rounded-lg p-3" style="height:90px;{{ $showCard ? 'width:100%' : 'width:130px' }};">
                                <img 
                                    alt="{{ $logo['name'] }}" 
                                    class="object-contain opacity-60 grayscale brightness-200 hover:grayscale-0 hover:opacity-100 hover:brightness-100 transition-all duration-300" 
                                    src="{{ $logo['url'] }}"
                                    loading="lazy"
                                    style="max-width:100%;max-height:70px;"
                                >
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Stats Card (right side) --}}
            @if($showCard)
            <div class="lg:w-[35%] flex items-stretch">
                <div class="w-full bg-surface-dark rounded-2xl p-8 lg:p-10 flex flex-col justify-center border border-border-dark">
                    {{-- Number --}}
                    <p class="text-5xl md:text-6xl font-extrabold text-white mb-2" style="letter-spacing: 2px;">
                        {{ $cardNumber }}
                    </p>
                    {{-- Title --}}
                    <h3 class="text-lg font-bold text-white mb-4">
                        {{ $cardTitle }}
                    </h3>
                    {{-- Description --}}
                    <p class="text-sm text-gray-400 leading-relaxed mb-6" style="text-align: justify;">
                        {{ $cardDescription }}
                    </p>
                    {{-- CTA Link --}}
                    <a href="{{ $cardCtaUrl }}" class="inline-flex items-center text-sm font-bold text-primary hover:text-primary-300 transition-colors group focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background-dark rounded">
                        {{ $cardCtaText }}
                        <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>
            @endif
        </div>
        @else
        {{-- Scrolling Layout --}}
        <div class="flex animate-scroll">
            {{-- First set --}}
            <div class="flex items-center gap-16 px-8 shrink-0">
                @for ($i = 0; $i < $repeatCount; $i++)
                    @if($hasBrands)
                        @foreach($brands as $brand)
                            @php $logoUrl = $getLogoUrl($brand); @endphp
                            @if($logoUrl)
                                @if($brand->url)
                                    <a href="{{ $brand->url }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center w-28 h-16 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background-dark rounded">
                                        <img 
                                            alt="{{ $brand->name }}" 
                                            class="max-h-full max-w-full object-contain opacity-60 grayscale brightness-200 hover:grayscale-0 hover:opacity-100 hover:brightness-100 transition-all duration-300" 
                                            src="{{ $logoUrl }}"
                                            loading="lazy"
                                        >
                                    </a>
                                @else
                                    <div class="flex items-center justify-center w-28 h-16">
                                        <img 
                                            alt="{{ $brand->name }}" 
                                            class="max-h-full max-w-full object-contain opacity-60 grayscale brightness-200 hover:grayscale-0 hover:opacity-100 hover:brightness-100 transition-all duration-300" 
                                            src="{{ $logoUrl }}"
                                            loading="lazy"
                                        >
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    @else
                        @foreach($defaultLogos as $logo)
                            <div class="flex items-center justify-center w-28 h-16">
                                <img 
                                    alt="{{ $logo['name'] }}" 
                                    class="max-h-full max-w-full object-contain opacity-60 grayscale brightness-200 hover:grayscale-0 hover:opacity-100 hover:brightness-100 transition-all duration-300" 
                                    src="{{ $logo['url'] }}"
                                    loading="lazy"
                                >
                            </div>
                        @endforeach
                    @endif
                @endfor
            </div>
            
            {{-- Duplicate for seamless scroll --}}
            @if($direction !== 'static')
            <div class="flex items-center gap-16 px-8 shrink-0">
                @for ($i = 0; $i < $repeatCount; $i++)
                    @if($hasBrands)
                        @foreach($brands as $brand)
                            @php $logoUrl = $getLogoUrl($brand); @endphp
                            @if($logoUrl)
                                @if($brand->url)
                                    <a href="{{ $brand->url }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center w-28 h-16 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background-dark rounded">
                                        <img 
                                            alt="{{ $brand->name }}" 
                                            class="max-h-full max-w-full object-contain opacity-60 grayscale brightness-200 hover:grayscale-0 hover:opacity-100 hover:brightness-100 transition-all duration-300" 
                                            src="{{ $logoUrl }}"
                                            loading="lazy"
                                            width="112"
                                            height="64"
                                        >
                                    </a>
                                @else
                                    <div class="flex items-center justify-center w-28 h-16">
                                        <img 
                                            alt="{{ $brand->name }}" 
                                            class="max-h-full max-w-full object-contain opacity-60 grayscale brightness-200 hover:grayscale-0 hover:opacity-100 hover:brightness-100 transition-all duration-300" 
                                            src="{{ $logoUrl }}"
                                            loading="lazy"
                                            width="112"
                                            height="64"
                                        >
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    @else
                        @foreach($defaultLogos as $logo)
                            <img 
                                alt="{{ $logo['name'] }}" 
                                class="{{ $logo['height'] }} object-contain opacity-60 grayscale brightness-200 hover:grayscale-0 hover:opacity-100 hover:brightness-100 transition-all duration-300" 
                                src="{{ $logo['url'] }}"
                                loading="lazy"
                                width="112"
                                height="64"
                            >
                        @endforeach
                    @endif
                @endfor
            </div>
            @endif
        </div>
        @endif
    </div>

    {{-- Row 2: Scroll Right (Only if not static and rows > 1) --}}
    @if($direction !== 'static' && $rows > 1)
    <div class="relative overflow-hidden">
        <div class="flex animate-scroll-reverse">
            {{-- First set --}}
            <div class="flex items-center gap-16 px-8 shrink-0">
                @for ($i = 0; $i < $repeatCount; $i++)
                    @if($hasBrands)
                        @foreach($brands as $brand)
                            @php $logoUrl = $getLogoUrl($brand); @endphp
                            @if($logoUrl)
                                @if($brand->url)
                                    <a href="{{ $brand->url }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center w-28 h-16 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background-dark rounded">
                                        <img 
                                            alt="{{ $brand->name }}" 
                                            class="max-h-full max-w-full object-contain opacity-60 grayscale brightness-200 hover:grayscale-0 hover:opacity-100 hover:brightness-100 transition-all duration-300" 
                                            src="{{ $logoUrl }}"
                                        >
                                    </a>
                                @else
                                    <div class="flex items-center justify-center w-28 h-16">
                                        <img 
                                            alt="{{ $brand->name }}" 
                                            class="max-h-full max-w-full object-contain opacity-60 grayscale brightness-200 hover:grayscale-0 hover:opacity-100 hover:brightness-100 transition-all duration-300" 
                                            src="{{ $logoUrl }}"
                                        >
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    @else
                        @foreach($defaultLogos as $logo)
                            <img 
                                alt="{{ $logo['name'] }}" 
                                class="{{ $logo['height'] }} object-contain opacity-60 grayscale brightness-200 hover:grayscale-0 hover:opacity-100 hover:brightness-100 transition-all duration-300" 
                                src="{{ $logo['url'] }}"
                            >
                        @endforeach
                    @endif
                @endfor
            </div>
            
            {{-- Duplicate for seamless scroll --}}
            <div class="flex items-center gap-16 px-8 shrink-0">
                @for ($i = 0; $i < $repeatCount; $i++)
                    @if($hasBrands)
                        @foreach($brands as $brand)
                            @php $logoUrl = $getLogoUrl($brand); @endphp
                            @if($logoUrl)
                                @if($brand->url)
                                    <a href="{{ $brand->url }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center w-28 h-16 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background-dark rounded">
                                        <img 
                                            alt="{{ $brand->name }}" 
                                            class="max-h-full max-w-full object-contain opacity-60 grayscale brightness-200 hover:grayscale-0 hover:opacity-100 hover:brightness-100 transition-all duration-300" 
                                            src="{{ $logoUrl }}"
                                        >
                                    </a>
                                @else
                                    <div class="flex items-center justify-center w-28 h-16">
                                        <img 
                                            alt="{{ $brand->name }}" 
                                            class="max-h-full max-w-full object-contain opacity-60 grayscale brightness-200 hover:grayscale-0 hover:opacity-100 hover:brightness-100 transition-all duration-300" 
                                            src="{{ $logoUrl }}"
                                        >
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    @else
                        @foreach($defaultLogos as $logo)
                            <img 
                                alt="{{ $logo['name'] }}" 
                                class="{{ $logo['height'] }} object-contain opacity-60 grayscale brightness-200 hover:grayscale-0 hover:opacity-100 hover:brightness-100 transition-all duration-300" 
                                src="{{ $logo['url'] }}"
                            >
                        @endforeach
                    @endif
                @endfor
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
    @keyframes scroll {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    @keyframes scroll-reverse {
        0% { transform: translateX(-50%); }
        100% { transform: translateX(0); }
    }
    .animate-scroll {
        animation: scroll {{ $speed }}s linear infinite;
    }
    .animate-scroll-reverse {
        animation: scroll-reverse {{ $speed }}s linear infinite;
    }
    .animate-scroll:hover,
    .animate-scroll-reverse:hover {
        animation-play-state: paused;
    }
</style>
@endpush
