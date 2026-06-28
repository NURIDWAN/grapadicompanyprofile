@extends('layouts.app')

@section('title', $service->service_name . ' - Grapadi')
@section('description', Str::limit(strip_tags($service->description), 160))

@section('content')
    {{-- Hero Section --}}
    <section class="bg-navy-brand py-16 lg:py-24">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="flex-1 text-center lg:text-left">
                    <nav class="text-sm mb-4">
                        <a href="{{ route('services') }}" class="text-gray-400 hover:text-white transition">Services</a>
                        <span class="text-gray-500 mx-2">/</span>
                        <span class="text-white">{{ $service->service_name }}</span>
                    </nav>
                    <div class="w-16 h-16 rounded-full bg-primary/20 flex items-center justify-center text-primary mb-6 mx-auto lg:mx-0">
                        <span class="material-icons-outlined text-4xl">{{ $service->icon_url ?? 'analytics' }}</span>
                    </div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold font-display text-white leading-tight mb-6">
                        {{ $service->service_name }}
                    </h1>
                    @if($service->description)
                    <div class="text-lg text-gray-300 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        {!! Str::limit(strip_tags($service->description), 200) !!}
                    </div>
                    @endif
                    <div class="mt-8">
                        <a href="{{ route('contact') }}" class="inline-flex items-center bg-primary hover:bg-primary-800 text-white font-bold py-3 px-8 rounded transition">
                            Konsultasi Gratis
                            <span class="material-icons-outlined ml-2">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Service Description --}}
    <section class="py-16 bg-background-dark">
        <div class="max-w-4xl mx-auto px-4">
            @if($service->description)
            <div class="prose prose-lg dark:prose-invert max-w-none">
                {!! $service->description !!}
            </div>
            @endif

            {{-- Features --}}
            @if($service->features && is_array($service->features) && count($service->features) > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-white mb-6">Layanan yang Termasuk</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($service->features as $feature)
                    <div class="flex items-start gap-3 p-4 bg-surface-dark rounded-lg">
                        <span class="material-icons-outlined text-primary text-xl shrink-0">check_circle</span>
                        <span class="text-gray-300">{{ $feature }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </section>

    {{-- Related Portfolios --}}
    @if($portfolios->count() > 0)
    <section class="py-16 bg-surface-dark">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-8">
                Proyek Terkait
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($portfolios as $portfolio)
                <div class="bg-background-dark rounded-xl overflow-hidden shadow-lg">
                    @if($portfolio->image_url)
                    <img src="{{ $portfolio->image_url }}" alt="{{ $portfolio->project_title }}" class="w-full h-48 object-cover">
                    @endif
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="bg-primary/10 text-primary text-xs font-bold px-2 py-1 rounded">{{ $portfolio->project_year }}</span>
                        </div>
                        <p class="text-primary text-sm font-semibold mb-1">{{ $portfolio->client_name }}</p>
                        <h3 class="text-lg font-bold text-white mb-2">{{ $portfolio->project_title }}</h3>
                        @if($portfolio->results && is_array($portfolio->results))
                        <div class="flex flex-wrap gap-2 mt-3">
                            @foreach(array_slice($portfolio->results, 0, 2) as $result)
                            <span class="text-xs text-gray-400 flex items-center gap-1">
                                <span class="material-icons-outlined text-sm text-primary">check</span>
                                {{ $result }}
                            </span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-8">
                <a href="{{ route('portfolio') }}" class="inline-flex items-center text-primary hover:text-red-700 font-bold transition">
                    Lihat Semua Portfolio
                    <span class="material-icons-outlined ml-1">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- Related Services --}}
    @if($relatedServices->count() > 0)
    <section class="py-16 bg-background-dark">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-8">
                Layanan Lainnya
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($relatedServices as $relatedService)
                <a href="{{ route('services.show', $relatedService->slug) }}" class="block bg-surface-dark rounded-xl p-6 hover:shadow-lg transition group">
                    <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-4">
                        <span class="material-icons-outlined text-2xl">{{ $relatedService->icon_url ?? 'analytics' }}</span>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2 group-hover:text-primary transition">
                        {{ $relatedService->service_name }}
                    </h3>
                    @if($relatedService->description)
                    <p class="text-sm text-gray-400">
                        {{ Str::limit(strip_tags($relatedService->description), 100) }}
                    </p>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- CTA Section --}}
    <x-cta-section 
        title="Tertarik dengan layanan ini?"
        description="Hubungi kami untuk konsultasi gratis dan temukan solusi terbaik untuk bisnis Anda."
        primaryText="Hubungi Kami"
        primaryUrl="/contact"
        background="navy"
    />
@endsection
