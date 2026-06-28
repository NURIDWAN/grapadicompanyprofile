@props([
    'brands' => collect([]),
    'brandsByType' => collect([]),
])

@php
    use App\Models\Brand;

    $getLogoUrl = function($brand) {
        if (!empty($brand->logo)) {
            if (str_starts_with($brand->logo, 'http')) {
                return $brand->logo;
            }
            return asset('storage/' . $brand->logo);
        }
        return null;
    };

    // Use brandsByType if available, otherwise fall back to brands prop
    $hasBrandsByType = $brandsByType && $brandsByType->count() > 0;
    $hasBrands = $brands && $brands->count() > 0;

    // Category labels mapping
    $typeLabels = Brand::TYPE_OPTIONS;
@endphp

<section class="py-12 lg:py-16 border-t border-border-dark">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">

        {{-- Main Title --}}
        <div class="text-center mb-4">
            <p class="text-sm text-primary uppercase tracking-[0.2em] font-semibold font-display mb-3">
                Trusted By
            </p>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold font-display text-white mb-2">
                Dipercaya oleh 100+ Perusahaan
            </h2>
            <p class="text-sm text-gray-400 italic">
                Dan ratusan perusahaan lainnya di berbagai sektor
            </p>
        </div>

        @if($hasBrandsByType)
            {{-- Display each brand category --}}
            @foreach($brandsByType as $type => $typeBrands)
                @if($typeBrands->count() > 0)
                    <div class="mt-10">
                        {{-- Category Label (skip for first/trusted type since main title already says it) --}}
                        @if($type !== 'trusted')
                        <div class="flex items-center gap-4 mb-6">
                            <span class="text-xs text-gray-400 uppercase tracking-[0.15em] font-semibold whitespace-nowrap">
                                {{ $typeLabels[$type] ?? ucfirst($type) }}
                            </span>
                            <div class="flex-1 h-px bg-border-dark"></div>
                        </div>
                        @endif

                        {{-- Logos Grid - uniform size, no background, centered --}}
                        <div class="flex flex-wrap justify-center gap-3">
                            @foreach($typeBrands as $brand)
                                @php $logoUrl = $getLogoUrl($brand); @endphp
                                <div class="w-32 h-16 flex items-center justify-center p-2 group bg-surface-dark/30 rounded-lg border border-border-dark/30 hover:border-primary/50 transition-all duration-300">
                                    @if($logoUrl)
                                        @if($brand->url)
                                            <a href="{{ $brand->url }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center w-full h-full" aria-label="{{ $brand->name }}">
                                                <img
                                                    src="{{ $logoUrl }}"
                                                    alt="{{ $brand->name }}"
                                                    class="max-h-full max-w-full w-auto h-auto object-contain opacity-70 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-300"
                                                    loading="lazy"
                                                >
                                            </a>
                                        @else
                                            <img
                                                src="{{ $logoUrl }}"
                                                alt="{{ $brand->name }}"
                                                class="max-h-full max-w-full w-auto h-auto object-contain opacity-70 grayscale group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-300"
                                                loading="lazy"
                                            >
                                        @endif
                                    @else
                                        {{-- Text fallback when no logo --}}
                                        <span class="text-xs text-gray-400 group-hover:text-white font-medium text-center leading-tight transition-colors duration-300">
                                            {{ $brand->name }}
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        @elseif($hasBrands)
            {{-- Fallback: display brands without grouping --}}
            <div class="mt-10">
                <div class="brand-grid">
                    @foreach($brands as $brand)
                        @php $logoUrl = $getLogoUrl($brand); @endphp
                        <div class="brand-card flex items-center justify-center p-2 group">
                            @if($logoUrl)
                                <img
                                    src="{{ $logoUrl }}"
                                    alt="{{ $brand->name }}"
                                    class="max-h-full max-w-full w-auto h-auto object-contain opacity-70 grayscale group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-300"
                                    loading="lazy"
                                >
                            @else
                                <span class="text-xs text-gray-400 group-hover:text-white font-medium text-center leading-tight transition-colors duration-300">
                                    {{ $brand->name }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            {{-- No brands at all - show placeholder --}}
            <div class="mt-10">
                <div class="brand-grid">
                    @for($i = 0; $i < 8; $i++)
                        <div class="brand-card flex items-center justify-center p-2 animate-pulse">
                            <div class="w-12 h-6 bg-white/10 rounded"></div>
                        </div>
                    @endfor
                </div>
            </div>
        @endif

        {{-- Footer note --}}
        <p class="text-center text-xs text-gray-500 mt-8 flex items-center justify-center gap-2">
            <span class="material-icons-outlined text-sm">verified</span>
            Bergabunglah bersama perusahaan-perusahaan terkemuka di Indonesia
        </p>

        {{-- CTA Banner --}}
        <div class="mt-10 border border-border-dark rounded-2xl p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6 bg-surface-dark/40">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full border-2 border-primary flex items-center justify-center shrink-0">
                    <span class="material-icons-outlined text-primary text-xl">calendar_today</span>
                </div>
                <div>
                    <h3 class="text-xl md:text-2xl font-display font-bold text-white">
                        Ready to Transform Insight into Action?
                    </h3>
                    <p class="text-sm text-gray-400 mt-1">
                        Diskusikan tantangan bisnis Anda bersama konsultan strategis kami
                    </p>
                </div>
            </div>
            <a
                href="/contact"
                class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-400 text-background-dark font-bold py-3 px-6 rounded-lg transition-colors duration-200 text-sm min-h-[44px] whitespace-nowrap focus:outline-none focus:ring-2 focus:ring-primary-300 focus:ring-offset-2 focus:ring-offset-background-dark"
            >
                Book Consultation
                <span class="material-icons-outlined text-base">arrow_forward</span>
            </a>
        </div>
    </div>
</section>

<style>
    .brand-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.75rem;
    }
    .brand-card {
        height: 5rem;
    }
    @media (min-width: 640px) {
        .brand-grid {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }
    }
    @media (min-width: 768px) {
        .brand-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr));
        }
    }
    @media (min-width: 1024px) {
        .brand-grid {
            grid-template-columns: repeat(8, minmax(0, 1fr));
        }
    }
</style>
