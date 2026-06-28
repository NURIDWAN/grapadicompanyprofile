@extends('layouts.app')

@section('title', site_setting('page_title_services', 'Our Services - Grapadi'))
@section('description', 'Strategic Intelligence for Global Markets - Data-driven insights that empower executive decision-making')

@section('content')
    {{-- Hero Section --}}
    <section class="relative bg-navy-brand py-16 lg:py-32 px-4 sm:px-6 md:px-10 lg:px-40 flex flex-col justify-center items-center text-center overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 32px 32px;"></div>
        <div class="relative z-10 max-w-4xl flex flex-col gap-4 sm:gap-6 items-center" data-animate="fade-in-up">
            <h1 class="text-white text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight tracking-tight">
                {{ $heroSection['subtitle'] }}
                <span class="text-gray-300">{{ $heroSection['highlight'] }}</span>
            </h1>
            <p class="text-gray-300 text-base sm:text-lg md:text-xl font-normal leading-relaxed max-w-2xl">
                {{ $heroSection['description'] }}
            </p>
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mt-4 justify-center w-full sm:w-auto">
                <a class="flex cursor-pointer items-center justify-center rounded-lg h-12 px-8 bg-white text-navy-brand hover:bg-gray-100 transition-colors text-base font-bold" href="{{ $heroSection['primary_url'] }}">
                    {{ $heroSection['primary_text'] }}
                </a>
                <a class="flex cursor-pointer items-center justify-center rounded-lg h-12 px-8 bg-transparent border border-white/30 text-white hover:bg-white/10 transition-colors text-base font-bold" href="{{ $heroSection['secondary_url'] }}">
                    {{ $heroSection['secondary_text'] }}
                </a>
            </div>
        </div>
    </section>

    {{-- Expertise Headline --}}
    <section class="bg-background-dark py-16 px-6 md:px-10 lg:px-40">
        <div class="max-w-[960px] mx-auto text-center">
            <span class="text-primary font-bold text-sm tracking-widest uppercase mb-2 block" data-animate="fade-in-up">{{ $expertiseSection['tagline'] }}</span>
            <h2 class="text-white text-3xl md:text-4xl font-bold leading-tight" data-animate="fade-in-up" data-delay="100">
                {{ $expertiseSection['headline'] }}
            </h2>
        </div>
    </section>

    {{-- Services Grid --}}
    <section id="services" class="bg-background-dark pb-24 px-6 md:px-10 lg:px-40">
        <div class="max-w-[1200px] mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($services as $index => $service)
            <div class="flex flex-col gap-4 rounded-lg bg-surface-dark p-8 shadow-sm border border-gray-700 hover:-translate-y-1 hover:shadow-lg transition-all duration-300" data-animate="fade-in-up" data-delay="{{ $index * 100 }}">
                <div class="w-12 h-12 rounded-full bg-primary-900/20 flex items-center justify-center text-primary mb-2">
                    <span class="material-icons-outlined text-3xl">{{ $service->icon_url ?? 'analytics' }}</span>
                </div>
                <div>
                    <h3 class="text-white text-xl font-bold leading-tight mb-2">
                        {{ $service->service_name }}
                    </h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-4">
                        {!! Str::limit(strip_tags($service->description), 150) !!}
                    </p>
                    @if($service->features && is_array($service->features))
                    <ul class="space-y-2">
                        @foreach($service->features as $feature)
                        <li class="flex items-start gap-2 text-sm text-gray-300 font-medium">
                            <span class="material-icons-outlined text-primary text-lg">check_circle</span>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </section>



    {{-- Grapadi Strategix Section --}}
    @if($strategix['is_active'])
    {{-- Product Intro --}}
    <section class="bg-background-dark py-20 px-6 md:px-10 lg:px-40">
        <div class="max-w-[1000px] mx-auto text-center">
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

            <h2 class="text-3xl md:text-5xl font-bold text-white mb-6 tracking-tight" data-animate="fade-in-up" data-delay="100">
                {{ $strategix['title'] }}
            </h2>
            <p class="text-gray-400 text-lg leading-relaxed max-w-3xl mx-auto mb-8" data-animate="fade-in-up" data-delay="200">
                {{ $strategix['description'] }}
            </p>
            <a href="{{ $strategix['cta_url'] }}" target="_blank" rel="noopener" 
               class="inline-flex items-center gap-2 bg-primary hover:bg-primary-700 text-white font-bold py-4 px-8 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl" data-animate="fade-in-up" data-delay="300">
                {{ $strategix['cta_text'] }}
                <span class="material-icons-outlined">arrow_forward</span>
            </a>
        </div>
    </section>

    {{-- Platform Access Steps --}}
    @if(count($platformSteps['steps']) > 0)
    <section class="bg-background-dark py-20 px-6 md:px-10 lg:px-20 border-t border-gray-800">
        <div class="max-w-[1200px] mx-auto">
            <h2 class="text-2xl md:text-4xl font-bold text-white text-center mb-12" data-animate="fade-in-up">
                {{ $platformSteps['title'] }}
            </h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-{{ min(count($platformSteps['steps']), 2) }} gap-8">
                @foreach($platformSteps['steps'] as $index => $step)
                <div class="bg-surface-dark rounded-2xl p-6 md:p-8 shadow-lg relative border border-gray-700" data-animate="fade-in-up" data-delay="{{ $index * 150 }}">
                    {{-- Step Number --}}
                    <div class="flex items-center gap-3 mb-4">
                        <span class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center font-bold text-sm">
                            {{ $index + 1 }}
                        </span>
                        <h3 class="text-lg md:text-xl font-bold text-white">{{ $step['title'] ?? 'Step ' . ($index + 1) }}</h3>
                    </div>

                    <div class="flex flex-col md:flex-row gap-6">
                        {{-- Content --}}
                        <div class="flex-1">
                            @if($step['description'] ?? false)
                            <p class="text-gray-400 mb-4">{{ $step['description'] }}</p>
                            @endif

                            @if(isset($step['bullets']) && count($step['bullets']) > 0)
                            <ol class="list-decimal list-inside space-y-1 text-gray-300 mb-4">
                                @foreach($step['bullets'] as $bullet)
                                <li>{{ $bullet['text'] ?? '' }}</li>
                                @endforeach
                            </ol>
                            @endif

                            @if($step['action_text'] ?? false)
                            <p class="text-gray-400 mt-4">{!! nl2br(e($step['action_text'])) !!}</p>
                            @endif
                        </div>

                        {{-- Screenshot --}}
                        @php
                            $stepImage = $step['image'] ?? '';
                            if ($stepImage && !str_starts_with($stepImage, 'http')) {
                                $stepImage = asset('storage/' . $stepImage);
                            }
                        @endphp
                        @if($stepImage)
                        <div class="flex-1">
                            <div class="relative">
                                <img src="{{ $stepImage }}" alt="{{ $step['title'] ?? 'Step' }}" class="w-full rounded-lg shadow-md">
                                @if($step['image_label'] ?? false)
                                <span class="absolute -bottom-3 left-1/2 transform -translate-x-1/2 bg-primary text-white text-xs font-bold px-4 py-2 rounded-full shadow-lg whitespace-nowrap">
                                    {{ $step['image_label'] }}
                                </span>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Dashboard Features --}}
    @if(count($strategix['dashboards']) > 0)
    <section class="bg-background-dark py-20 px-6 md:px-10 lg:px-40 border-t border-gray-800">
        <div class="max-w-[1200px] mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold text-white text-center mb-4" data-animate="fade-in-up">
                {{ $dashboardSection['title'] }}
            </h2>
            <p class="text-gray-400 text-center mb-12 max-w-2xl mx-auto" data-animate="fade-in-up" data-delay="100">
                {{ $dashboardSection['description'] }}
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($strategix['dashboards'] as $index => $dashboard)
                <div class="text-center" data-animate="fade-in-up" data-delay="{{ ($index % 4) * 100 + 200 }}">
                    {{-- Dashboard Image with Laptop Frame --}}
                    <div class="relative mb-6">
                        <div class="bg-gray-800 rounded-lg p-2 shadow-2xl">
                            <div class="bg-gray-900 rounded-t-sm h-3 flex items-center gap-1 px-2">
                                <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                            </div>
                            @php
                                $dashboardImage = $dashboard['image'] ?? '';
                                if ($dashboardImage && !str_starts_with($dashboardImage, 'http')) {
                                    $dashboardImage = asset('storage/' . $dashboardImage);
                                }
                            @endphp
                            @if($dashboardImage)
                            <img src="{{ $dashboardImage }}" alt="{{ $dashboard['title'] ?? 'Dashboard' }}" class="w-full rounded-b">
                            @else
                            <div class="w-full h-48 bg-gradient-to-br from-primary-700 to-primary-900 rounded-b flex items-center justify-center">
                                <span class="material-icons-outlined text-white/30 text-6xl">dashboard</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    <h3 class="text-white font-bold text-lg mb-2">{{ $dashboard['title'] ?? 'Dashboard' }}</h3>
                    <p class="text-gray-400 text-sm">{{ $dashboard['description'] ?? '' }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Pricing Table --}}
    @if(count($strategix['pricing_plans']) > 0)
    <section class="bg-background-dark py-20 px-6 md:px-10 lg:px-20 border-t border-gray-800">
        <div class="max-w-[1400px] mx-auto">
            <h2 class="text-2xl md:text-4xl font-bold text-white text-center mb-12" data-animate="fade-in-up">
                {{ $strategix['pricing_title'] }}
            </h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ min(count($strategix['pricing_plans']), 4) }} gap-4 md:gap-6">
                @foreach($strategix['pricing_plans'] as $index => $plan)
                <div class="relative {{ ($plan['is_highlighted'] ?? false) ? 'bg-primary/10 border-2 border-primary' : 'bg-surface-dark' }} rounded-xl p-6 shadow-xl" data-animate="fade-in-up" data-delay="{{ $index * 100 }}">
                    {{-- Highlight Badge --}}
                    @if($plan['is_highlighted'] ?? false)
                    <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                        <span class="bg-primary text-background-dark text-xs font-bold px-3 py-1 rounded-full">POPULAR</span>
                    </div>
                    @endif

                    <div class="text-center mb-6">
                        <h3 class="text-xl font-bold text-white mb-1">{{ $plan['name'] ?? 'Plan' }}</h3>
                        @if($plan['subtitle'] ?? false)
                        <span class="text-xs text-primary font-medium">{{ $plan['subtitle'] }}</span>
                        @endif
                        <div class="border-b-4 border-primary w-12 mx-auto my-3"></div>
                        <p class="text-gray-400 text-sm font-medium">{{ $plan['price'] ?? '' }}</p>
                    </div>

                    {{-- Features List --}}
                    <ul class="space-y-2">
                        @foreach(($plan['features'] ?? []) as $feature)
                        <li class="flex items-start gap-2 text-sm">
                            @if($feature['included'] ?? true)
                            <span class="material-icons-outlined text-green-500 text-lg flex-shrink-0">check_circle</span>
                            @else
                            <span class="material-icons-outlined text-red-500 text-lg flex-shrink-0">cancel</span>
                            @endif
                            <span class="text-gray-300">{{ $feature['text'] ?? '' }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>

            <div class="text-center mt-12" data-animate="fade-in-up" data-delay="400">
                <a href="{{ $strategix['cta_url'] }}" target="_blank" rel="noopener" 
                   class="inline-flex items-center gap-2 bg-primary hover:bg-primary-700 text-white font-bold py-4 px-8 rounded-lg transition-all duration-300 shadow-lg">
                    {{ $strategix['cta_text'] }}
                    <span class="material-icons-outlined">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>
    @endif
    @endif

    {{-- CTA Section --}}
    <x-cta-section 
        title="{{ $ctaSection['title'] }}"
        description="{{ $ctaSection['description'] }}"
        primaryText="{{ $ctaSection['primary_text'] }}"
        primaryUrl="{{ $ctaSection['primary_url'] }}"
        background="white"
    />
@endsection
