@props([
    'brands' => collect([]),
])

@php
    $hasBrands = $brands && $brands->count() > 0;

    $getLogoUrl = function($brand) {
        if (!empty($brand->logo)) {
            if (str_starts_with($brand->logo, 'http')) {
                return $brand->logo;
            }
            return asset('storage/' . $brand->logo);
        }
        return null;
    };

    // Default logos as fallback
    $defaultLogos = [
        ['name' => 'Sinarmas', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/27/Sinar_Mas_Group_Logo.svg/320px-Sinar_Mas_Group_Logo.svg.png'],
        ['name' => 'Ciputra', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e7/Ciputra_Development_logo.svg/320px-Ciputra_Development_logo.svg.png'],
        ['name' => 'Jaya Property', 'url' => 'https://upload.wikimedia.org/wikipedia/id/thumb/a/ac/Logo_Jaya_Real_Property.svg/320px-Logo_Jaya_Real_Property.svg.png'],
        ['name' => 'Pollux', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6d/Pollux_Properti_Indonesia_Logo.png/320px-Pollux_Properti_Indonesia_Logo.png'],
        ['name' => 'WIKA', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/19/Wijaya_Karya.svg/320px-Wijaya_Karya.svg.png'],
    ];
@endphp

<section class="py-8 lg:py-10 border-t border-border-dark">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
        {{-- Title --}}
        <p class="text-center text-sm text-primary uppercase tracking-[0.2em] font-semibold font-display mb-6">
            Trusted By
        </p>

        {{-- Logos row --}}
        <div class="flex flex-wrap items-center justify-center gap-8 md:gap-12 lg:gap-16 mb-8">
            @if($hasBrands)
                @foreach($brands as $brand)
                    @php $logoUrl = $getLogoUrl($brand); @endphp
                    @if($logoUrl)
                        <div class="flex items-center justify-center h-12">
                            <img
                                src="{{ $logoUrl }}"
                                alt="{{ $brand->name }}"
                                class="h-8 md:h-10 w-auto object-contain opacity-70 brightness-200 grayscale hover:grayscale-0 hover:opacity-100 hover:brightness-100 transition-all duration-300"
                                loading="lazy"
                            >
                        </div>
                    @endif
                @endforeach
            @else
                @foreach($defaultLogos as $logo)
                    <div class="flex items-center justify-center h-12">
                        <img
                            src="{{ $logo['url'] }}"
                            alt="{{ $logo['name'] }}"
                            class="h-8 md:h-10 w-auto object-contain opacity-70 brightness-200 grayscale hover:grayscale-0 hover:opacity-100 hover:brightness-100 transition-all duration-300"
                            loading="lazy"
                        >
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Subtitle --}}
        <p class="text-center text-sm text-gray-500 italic mb-8">
            Dan ratusan perusahaan lainnya di berbagai sektor
        </p>

        {{-- CTA Banner --}}
        <div class="border border-border-dark rounded-2xl p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6 bg-surface-dark/40">
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
