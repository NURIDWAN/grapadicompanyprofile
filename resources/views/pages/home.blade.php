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

        {{-- 2. Trusted By + CTA --}}
        <x-trusted-by-section :brands="$trustedBrands" />

        {{-- 3. Services Section --}}
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

        {{-- 4. FAQ Section --}}
        @if(!empty($faqs))
            <section class="py-8 lg:py-14 px-6 sm:px-8 lg:px-12">
                <div class="max-w-7xl mx-auto">
                    <x-faq-section :faqs="$faqs" />
                </div>
            </section>
        @endif

        {{-- 5. Final CTA Banner --}}
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
