@extends('layouts.app')

@section('title', site_setting('page_title_home', 'Home - Grapadi'))
@section('description', 'Market Intelligence & Consulting - Be The Top 1% Businesses')

@section('content')
    {{-- Main content wrapper: constrain to max-width 1536px on large viewports --}}
    <div class="max-w-[1536px] mx-auto">

        {{-- 1. Hero Section --}}
        <x-hero-section
            :title="$hero['title']"
            :subtitle="$hero['subtitle']"
            :ctaText="$hero['cta_text']"
            :ctaUrl="$hero['cta_url']"
            :showLogo="$hero['show_logo']"
            :stats="$hero['stats']"
        />

        {{-- 2. About the Founder Section --}}
        <section class="py-12 lg:py-20">
            <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
                <div style="display: flex; flex-direction: row; gap: 3rem; align-items: flex-start;" class="about-founder-wrap">
                    {{-- Left: Director Image --}}
                    <div style="width: 300px; min-width: 300px;" class="about-founder-img" data-animate="fade-in-left">
                        @php
                            $directorImage = $director['image'];
                            if ($directorImage && !str_starts_with($directorImage, 'http')) {
                                $directorImageUrl = str_starts_with($directorImage, 'image/')
                                    ? asset($directorImage)
                                    : asset('storage/' . $directorImage);
                            } else {
                                $directorImageUrl = $directorImage;
                            }
                        @endphp
                        <div class="relative">
                            {{-- Gold accent border at top --}}
                            <div class="absolute -top-1 left-4 right-4 h-1 bg-primary rounded-full z-10"></div>
                            <div class="rounded-2xl overflow-hidden border border-border-dark shadow-2xl">
                                <img 
                                    src="{{ $directorImageUrl }}" 
                                    alt="{{ $director['name'] }}" 
                                    style="width: 100%; height: auto; display: block;"
                                    class="object-cover"
                                    loading="lazy"
                                >
                            </div>
                        </div>
                    </div>

                    {{-- Right: Director Info --}}
                    <div style="flex: 1; min-width: 0;" data-animate="fade-in-right" data-delay="200">
                        {{-- Tag --}}
                        <p class="text-xs text-gray-400 uppercase tracking-[0.2em] font-semibold mb-3">
                            About the Founder
                        </p>

                        {{-- Name --}}
                        <div class="flex flex-wrap items-center gap-3 mb-3">
                            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold font-display text-white leading-tight">
                                {{ $director['name'] }}
                            </h2>
                        </div>

                        {{-- Subtitle/Role --}}
                        <p class="text-base md:text-lg font-display mb-6">
                            <span class="text-white font-semibold">Property, Investment & Corporate</span>
                            <span class="text-primary font-semibold"> Strategy Advisor</span>
                        </p>

                        {{-- Description --}}
                        <div class="text-gray-400 leading-relaxed text-sm md:text-base space-y-4 mb-8">
                            <p>{{ $director['description'] }}</p>
                        </div>

                        {{-- Signature --}}
                        <div class="mb-6">
                            <p style="font-family: 'Dancing Script', cursive; font-size: 2rem; color: #ffffff;">
                                {{ explode(',', $director['name'])[0] }}
                            </p>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-wrap items-center gap-4">
                            <a href="{{ $director['linkedin'] ?: '/about' }}" target="{{ $director['linkedin'] ? '_blank' : '_self' }}" rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 border border-border-dark hover:border-primary/50 text-white hover:text-primary py-3 px-6 rounded-lg transition-all duration-200 text-sm font-medium min-h-[44px] focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background-dark">
                                View Profile
                                <span class="material-icons-outlined text-base">arrow_forward</span>
                            </a>
                            @if($director['linkedin'])
                            <a href="{{ $director['linkedin'] }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center justify-center w-11 h-11 rounded-lg border border-border-dark hover:border-[#0077b5] text-gray-400 hover:text-[#0077b5] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background-dark"
                               aria-label="LinkedIn Profile">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <style>
            @media (max-width: 767px) {
                .about-founder-wrap {
                    flex-direction: column !important;
                }
                .about-founder-img {
                    width: 100% !important;
                    min-width: unset !important;
                    max-width: 300px;
                }
            }
        </style>

        {{-- 3. Trusted By + CTA --}}
        <x-trusted-by-section :brands="$trustedBrands" :brandsByType="$brandsByType ?? collect()" />

        {{-- 4. Services Section --}}
        @if($services->isNotEmpty())
            <section class="py-8 lg:py-14">
                <div class="px-6 sm:px-8 lg:px-12 max-w-7xl mx-auto">
                    {{-- Section header --}}
                    <div class="text-center mb-8">
                        <p class="text-sm text-primary uppercase tracking-[0.2em] font-semibold font-display mb-4">
                            Layanan Kami
                        </p>
                        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold font-display text-white leading-tight">
                            Solusi Strategis untuk<br>Setiap Tahap Bisnis Anda.
                        </h2>
                    </div>

                    {{-- Service cards - 4 columns --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($services as $service)
                            <x-service-card
                                :icon="$service->icon_url ?? 'analytics'"
                                :title="$service->service_name"
                                :description="$service->description ?? ''"
                                :link="'/services/' . $service->slug"
                                linkText="Pelajari lebih lanjut"
                            />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- 5. Grapadi Strategix Section --}}
        @if($strategix['is_active'])
        <section class="py-12 lg:py-20">
            <div class="max-w-[1000px] mx-auto px-6 sm:px-8 lg:px-12 text-center">
                {{-- Logo --}}
                @php
                    $strategixLogo = $strategix['logo'];
                    if ($strategixLogo && !str_starts_with($strategixLogo, 'http')) {
                        $strategixLogo = asset('storage/' . $strategixLogo);
                    }
                @endphp
                @if($strategixLogo)
                <div class="mb-8" data-animate="zoom-in">
                    <img src="{{ $strategixLogo }}" alt="{{ $strategix['title'] }}" class="h-32 md:h-40 mx-auto">
                </div>
                @else
                <div class="mb-8" data-animate="zoom-in">
                    <div class="w-24 h-24 md:w-32 md:h-32 mx-auto bg-primary rounded-2xl flex items-center justify-center">
                        <span class="text-white text-4xl md:text-5xl font-bold">G</span>
                    </div>
                </div>
                @endif

                <h2 class="text-3xl md:text-5xl font-bold font-display text-white mb-6 tracking-tight" data-animate="fade-in-up" data-delay="100">
                    {{ $strategix['title'] }}
                </h2>
                <p class="text-gray-400 text-lg leading-relaxed max-w-3xl mx-auto mb-8" data-animate="fade-in-up" data-delay="200">
                    {{ $strategix['description'] }}
                </p>
                <a href="https://strategix.grapadikonsultan.co.id" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 bg-primary hover:bg-primary-400 text-background-dark font-bold py-4 px-8 rounded-lg transition-colors duration-200 text-base min-h-[44px] focus:outline-none focus:ring-2 focus:ring-primary-300 focus:ring-offset-2 focus:ring-offset-background-dark"
                   data-animate="fade-in-up" data-delay="300">
                    {{ $strategix['cta_text'] }}
                    <span class="material-icons-outlined text-lg">arrow_forward</span>
                </a>
            </div>
        </section>
        @endif

        {{-- 6. FAQ Section --}}
        @if(!empty($faqs))
            <section class="py-8 lg:py-14 px-6 sm:px-8 lg:px-12">
                <div class="max-w-7xl mx-auto">
                    <x-faq-section :faqs="$faqs" />
                </div>
            </section>
        @endif

        {{-- 7. Final CTA Banner --}}
        <section class="py-8 lg:py-12 px-6 sm:px-8 lg:px-12">
            <div class="max-w-7xl mx-auto border border-border-dark rounded-2xl p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6 bg-surface-dark/40">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full border-2 border-primary flex items-center justify-center shrink-0">
                        <span class="material-icons-outlined text-primary text-xl">chat_bubble_outline</span>
                    </div>
                    <div>
                        <h3 class="text-xl md:text-2xl font-display font-bold text-white">
                            Let's Build Your Next Strategic Move.
                        </h3>
                        <p class="text-sm text-gray-400 mt-1">
                            Kami siap menjadi partner strategis Anda dalam mencapai pertumbuhan berkelanjutan.
                        </p>
                    </div>
                </div>
                <a
                    href="/contact"
                    class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-400 text-background-dark font-bold py-3 px-6 rounded-lg transition-colors duration-200 text-sm min-h-[44px] whitespace-nowrap focus:outline-none focus:ring-2 focus:ring-primary-300 focus:ring-offset-2 focus:ring-offset-background-dark"
                >
                    Hubungi Kami
                    <span class="material-icons-outlined text-base">arrow_forward</span>
                </a>
            </div>
        </section>

    </div>
@endsection
