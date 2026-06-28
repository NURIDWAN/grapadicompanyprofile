@php
$currentRoute = Request::path();

// Get site settings with fallbacks
$companyName = site_setting('site_company_name', 'Grapadi');
$logo = site_setting('site_logo');
$logoDark = site_setting('site_logo_dark');
$logoOnly = site_setting('site_logo_only', false);

// Navigation items with route matching
$navItems = [
    ['label' => 'Home', 'url' => url('/'), 'active' => $currentRoute === '/'],
    ['label' => 'About', 'url' => null, 'active' => in_array($currentRoute, ['about', 'timeline']), 'children' => [
        ['label' => 'About Us', 'url' => url('/about'), 'active' => $currentRoute === 'about'],
        ['label' => 'Timeline', 'url' => url('/timeline'), 'active' => $currentRoute === 'timeline'],
    ]],
    ['label' => 'Services', 'url' => url('/services'), 'active' => str_starts_with($currentRoute, 'services')],
    ['label' => 'Portfolio', 'url' => url('/portfolio'), 'active' => $currentRoute === 'portfolio'],
    ['label' => 'Blog', 'url' => url('/blog'), 'active' => str_starts_with($currentRoute, 'blog')],
    ['label' => 'Strategix', 'url' => site_setting('strategix_cta_url', 'https://strategix.grapadikonsultan.co.id'), 'active' => false, 'external' => true],
];
@endphp

<nav
    x-data="{
        scrolled: false,
        mobileMenuOpen: false,
        init() {
            this.scrolled = window.pageYOffset > 20;
            window.addEventListener('scroll', () => {
                this.scrolled = window.pageYOffset > 20;
            });
        }
    }"
    :class="scrolled ? 'shadow-lg shadow-black/20' : ''"
    class="fixed top-0 w-full z-50 bg-background-dark border-b border-border-dark transition-shadow duration-300"
    role="navigation"
    aria-label="Main navigation"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            {{-- Logo --}}
            <div class="flex items-center">
                <a class="flex-shrink-0 flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background-dark rounded" href="{{ url('/') }}">
                    @if($logo)
                        <img
                            src="{{ str_starts_with($logo, 'http') ? $logo : asset('storage/' . $logo) }}"
                            alt="{{ $companyName }} Logo"
                            class="h-10 w-auto"
                        >
                    @else
                        <img
                            src="{{ asset('image/logo/image.png') }}"
                            alt="{{ $companyName }} Logo"
                            class="h-10 w-auto"
                        >
                    @endif
                    @if(!$logoOnly)
                        <span class="font-bold text-xl font-display text-white">
                            {{ $companyName }}
                        </span>
                    @endif
                </a>
            </div>

            {{-- Desktop Navigation Items --}}
            <div class="hidden md:flex items-center space-x-1 lg:space-x-2">
                @foreach($navItems as $item)
                    @if(isset($item['children']))
                        {{-- Dropdown item --}}
                        <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="relative">
                            <button
                                @click="open = !open"
                                class="relative px-3 py-2 text-sm font-medium transition-colors duration-200 rounded inline-flex items-center gap-1 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background-dark {{ $item['active'] ? 'text-primary' : 'text-gray-300 hover:text-white' }}"
                            >
                                {{ $item['label'] }}
                                <span class="material-icons-outlined text-sm transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                                @if($item['active'])
                                    <span class="absolute bottom-0 left-3 right-3 h-0.5 bg-primary rounded-full"></span>
                                @endif
                            </button>
                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 translate-y-1"
                                class="absolute top-full left-0 mt-1 w-44 bg-white rounded-xl shadow-lg py-2 z-50"
                                x-cloak
                            >
                                @foreach($item['children'] as $child)
                                    <a
                                        href="{{ $child['url'] }}"
                                        class="block px-4 py-2.5 text-sm font-medium transition-colors duration-200 {{ $child['active'] ? 'text-primary' : 'text-gray-700 hover:text-primary hover:bg-gray-50' }}"
                                    >
                                        {{ $child['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a
                            href="{{ $item['url'] }}"
                            @if(isset($item['external']) && $item['external']) target="_blank" rel="noopener" @endif
                            class="relative px-3 py-2 text-sm font-medium transition-colors duration-200 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background-dark {{ $item['active'] ? 'text-primary' : 'text-gray-300 hover:text-white' }}"
                        >
                            {{ $item['label'] }}
                            @if($item['active'])
                                <span class="absolute bottom-0 left-3 right-3 h-0.5 bg-primary rounded-full"></span>
                            @endif
                        </a>
                    @endif
                @endforeach
            </div>

            {{-- Desktop CTA Button --}}
            <div class="hidden md:flex items-center">
                <a
                    href="{{ url('/contact') }}"
                    class="bg-primary hover:bg-primary-400 text-background-dark font-semibold px-5 py-2.5 rounded-lg transition-colors duration-200 min-h-[44px] inline-flex items-center focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background-dark"
                >
                    Konsultasi
                </a>
            </div>

            {{-- Mobile Menu Toggle --}}
            <div class="md:hidden flex items-center">
                <button
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    :aria-expanded="mobileMenuOpen.toString()"
                    aria-controls="mobile-menu-panel"
                    aria-label="Toggle navigation menu"
                    class="inline-flex items-center justify-center w-11 h-11 text-gray-300 hover:text-white rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background-dark"
                >
                    <span x-show="!mobileMenuOpen" class="material-icons-outlined text-2xl">menu</span>
                    <span x-show="mobileMenuOpen" class="material-icons-outlined text-2xl" x-cloak>close</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu Panel --}}
    <div
        id="mobile-menu-panel"
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="md:hidden bg-surface-dark border-t border-border-dark"
        x-cloak
    >
        <div class="px-4 py-4 space-y-1">
            @foreach($navItems as $item)
                @if(isset($item['children']))
                    {{-- Mobile dropdown --}}
                    <div x-data="{ open: {{ $item['active'] ? 'true' : 'false' }} }">
                        <button
                            @click="open = !open"
                            class="w-full flex items-center justify-between py-3 px-4 rounded-lg text-base font-medium min-h-[44px] transition-colors duration-200 focus:outline-none {{ $item['active'] ? 'text-primary bg-primary/10' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                        >
                            {{ $item['label'] }}
                            <span class="material-icons-outlined text-sm transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                        </button>
                        <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1">
                            @foreach($item['children'] as $child)
                                <a
                                    href="{{ $child['url'] }}"
                                    class="block py-2.5 px-4 rounded-lg text-sm font-medium min-h-[44px] flex items-center transition-colors duration-200 {{ $child['active'] ? 'text-primary' : 'text-gray-400 hover:text-white hover:bg-white/5' }}"
                                >
                                    {{ $child['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a
                        href="{{ $item['url'] }}"
                        @if(isset($item['external']) && $item['external']) target="_blank" rel="noopener" @endif
                        class="block py-3 px-4 rounded-lg text-base font-medium min-h-[44px] flex items-center transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-surface-dark {{ $item['active'] ? 'text-primary bg-primary/10' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach

            {{-- Mobile CTA --}}
            <div class="pt-3 mt-3 border-t border-border-dark">
                <a
                    href="{{ url('/contact') }}"
                    class="block w-full text-center bg-primary hover:bg-primary-400 text-background-dark font-semibold py-3 px-4 rounded-lg min-h-[44px] flex items-center justify-center transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-surface-dark"
                >
                    Konsultasi
                </a>
            </div>
        </div>
    </div>
</nav>

{{-- Noscript fallback: show navigation links when JavaScript is unavailable --}}
<noscript>
    <style>
        /* Hide the Alpine.js-powered nav on noscript and show fallback */
        nav[x-data] .md\:hidden button[aria-controls="mobile-menu-panel"] { display: none; }
    </style>
    <div class="fixed top-20 left-0 w-full z-40 bg-surface-dark border-b border-border-dark md:hidden">
        <div class="px-4 py-3 space-y-1">
            @foreach($navItems as $item)
                @if(isset($item['children']))
                    @foreach($item['children'] as $child)
                        <a
                            href="{{ $child['url'] }}"
                            class="block py-2 px-4 rounded text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-surface-dark {{ $child['active'] ? 'text-primary' : 'text-gray-300' }}"
                        >
                            {{ $child['label'] }}
                        </a>
                    @endforeach
                @else
                    <a
                        href="{{ $item['url'] }}"
                        class="block py-2 px-4 rounded text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-surface-dark {{ $item['active'] ? 'text-primary' : 'text-gray-300' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
            <a
                href="{{ url('/contact') }}"
                class="block mt-2 text-center bg-primary text-background-dark font-semibold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-surface-dark"
            >
                Konsultasi
            </a>
        </div>
    </div>
</noscript>
