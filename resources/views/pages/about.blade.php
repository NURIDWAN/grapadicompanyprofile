@extends('layouts.app')

@section('title', site_setting('page_title_about', 'About Us - Grapadi'))
@section('description', 'Strategic Insights, Digital Advantage')

@section('content')
    {{-- Hero Section --}}
    <section class="bg-background-dark py-16 lg:py-28">
        <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-20 items-center">
            <div data-animate="fade-in-left">
                <span class="text-primary font-bold tracking-wider uppercase text-sm mb-2 block">{{ $aboutHero['tagline'] }}</span>
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold font-display text-white leading-tight mb-4 sm:mb-6">
                    {{ $aboutHero['title'] }}
                </h1>
                <p class="text-base sm:text-lg text-gray-400 mb-6 sm:mb-8 leading-relaxed">
                    {{ $aboutHero['description'] }}
                </p>
            </div>
            <div class="relative rounded-2xl overflow-hidden shadow-2xl bg-navy-brand" data-animate="fade-in-right" data-delay="200">
                @php
                    $heroImage = $aboutHero['image'];
                    if ($heroImage && !str_starts_with($heroImage, 'http')) {
                        $heroImageUrl = str_starts_with($heroImage, 'image/') 
                            ? asset($heroImage) 
                            : asset('storage/' . $heroImage);
                    } else {
                        $heroImageUrl = $heroImage;
                    }
                @endphp
                <img alt="Modern Office" class="w-full h-full object-cover transform hover:scale-105 transition duration-700" src="{{ $heroImageUrl }}">
            </div>
        </div>
    </section>

    {{-- Story & Mission / Director Section --}}
    <section class="bg-surface-dark py-16 lg:py-20">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-20 items-center">
            <div class="order-2 lg:order-2 relative h-[350px] sm:h-[400px] lg:h-[500px] rounded-2xl overflow-hidden shadow-lg group" data-animate="fade-in-right" data-delay="200">
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
                <img alt="{{ $director['name'] }}" class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-500" src="{{ $directorImageUrl }}">
                <div class="absolute inset-0 bg-navy-brand/0"></div>
            </div>
            <div class="order-1 lg:order-1" data-animate="fade-in-left">
                <h3 class="text-xl font-bold text-gray-400 mb-2">
                    {{ $director['title'] }}
                </h3>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold font-display text-white mb-4">
                    {{ $director['name'] }}
                </h2>
                
                @if($director['linkedin'])
                <div class="mb-6">
                    <a href="{{ $director['linkedin'] }}" target="_blank" rel="noopener noreferrer" class="text-[#0077b5] hover:text-[#005582] transition">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                </div>
                @endif

                <p class="text-gray-300 mb-6 leading-relaxed text-lg">
                    {{ $director['description'] }}
                </p>
            </div>
        </div>
    </section>



    {{-- What We Do Section --}}
    <section class="bg-background-dark py-20">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12" data-animate="fade-in-up">
                <h2 class="text-3xl md:text-4xl font-bold font-display text-primary mb-6">
                    {{ $aboutWhatWeDo['title'] }}
                </h2>
                <p class="max-w-4xl mx-auto text-lg text-gray-400 leading-relaxed">
                    {{ $aboutWhatWeDo['description'] }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
                @foreach($aboutWhatWeDo['items'] as $index => $item)
                <div class="bg-surface-dark rounded-2xl py-6 px-4 text-center hover:shadow-lg transition duration-300" data-animate="fade-in-up" data-delay="{{ $index * 100 }}">
                    <span class="text-lg font-semibold text-gray-200">{{ $item['text'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Vision & Mission Section --}}
    <section class="bg-primary py-20 text-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20">
                {{-- Vision --}}
                <div class="space-y-6 relative lg:border-r lg:border-white/20 lg:pr-12" data-animate="fade-in-left">
                    <div class="flex flex-col items-center text-center lg:items-start lg:text-left mb-6">
                        <span class="material-icons-outlined text-6xl mb-4">psychology</span>
                        <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold font-display">{{ $aboutVision['title'] }}</h2>
                    </div>
                    <p class="text-lg leading-relaxed text-white/90 text-center lg:text-left">
                        {{ $aboutVision['description'] }}
                    </p>
                </div>

                {{-- Mission --}}
                <div class="space-y-6" data-animate="fade-in-right" data-delay="200">
                     <div class="flex flex-col items-center text-center lg:items-start lg:text-left mb-6">
                        <span class="material-icons-outlined text-6xl mb-4">track_changes</span>
                        <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold font-display">{{ $aboutMission['title'] }}</h2>
                    </div>
                    <ul class="space-y-4 text-lg text-white/90">
                        @foreach($aboutMission['items'] as $item)
                        <li class="flex items-start gap-3">
                            <span class="mt-2 w-1.5 h-1.5 bg-white rounded-full flex-shrink-0"></span>
                            <span>{{ $item['text'] }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Solutions Section --}}
    <section class="grid grid-cols-1 lg:grid-cols-2">
        {{-- Left Panel --}}
        <div class="bg-background-dark py-12 sm:py-16 lg:py-20 px-4 lg:px-16 order-2 lg:order-1">
            <div class="max-w-xl mx-auto lg:ml-auto lg:mr-0 pl-8 sm:pl-10 border-l border-gray-700 space-y-10 sm:space-y-16 relative">
                 <div class="absolute -left-[5px] top-0 bottom-0 w-[1px] bg-gray-700 hidden"></div>

                 @foreach($aboutSolutions['items'] as $item)
                 <div class="relative group">
                    <div class="absolute -left-[53px] top-0 w-6 h-6 bg-primary rounded-full group-hover:scale-125 transition duration-300"></div>
                    <h3 class="text-xl font-bold font-display text-white mb-3">{{ $item['title'] }}</h3>
                    <p class="text-gray-400 leading-relaxed text-sm">
                        {{ $item['description'] }}
                    </p>
                 </div>
                 @endforeach
            </div>
        </div>

        {{-- Right Panel --}}
        <div class="bg-background-dark py-12 sm:py-16 lg:py-20 px-4 sm:px-8 lg:px-16 flex items-center order-1 lg:order-2">
             <div class="max-w-xl mx-auto lg:mx-0">
                 <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-7xl font-bold font-display text-white mb-4 sm:mb-8">
                     {{ $aboutSolutions['title'] }}
                 </h2>
                 <p class="text-gray-400 text-lg leading-relaxed text-justify">
                     {{ $aboutSolutions['description'] }}
                 </p>
             </div>
        </div>
    </section>

    {{-- Leadership Team --}}
    <section class="bg-surface-dark py-20">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold font-display text-white mb-4">
                    {{ $teamSection['title'] }}
                </h2>
                <p class="text-gray-400">
                    {{ $teamSection['subtitle'] }}
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse($executiveTeam as $member)
                <x-team-card 
                    :name="$member->name"
                    :position="$member->position"
                    :photo="$member->photo_url ? asset('storage/' . $member->photo_url) : null"
                    :bio="$member->bio ?? ''"
                    :linkedin="$member->linkedin_url"
                />
                @empty
                <div class="col-span-full text-center py-12 text-gray-400">
                    <span class="material-icons-outlined text-6xl mb-4 block">groups</span>
                    <p>No team members found. Add team members in the admin panel.</p>
                </div>
                @endforelse
            </div>

        </div>
    </section>

    {{-- FAQ Section --}}
    @if($faqs->count() > 0)
    <section class="bg-background-dark py-16 lg:py-20">
        <div class="max-w-4xl mx-auto px-4">
            <div class="text-center mb-12" data-animate="fade-in-up">
                <h2 class="text-3xl md:text-4xl font-bold font-display text-white mb-4">
                    Pertanyaan yang Sering Diajukan
                </h2>
                <p class="text-gray-400">
                    Temukan jawaban untuk pertanyaan umum tentang layanan kami
                </p>
            </div>

            <div class="space-y-4" data-animate="fade-in-up" data-delay="200">
                @foreach($faqs as $index => $faq)
                <div x-data="{ open: {{ $index === 0 ? 'true' : 'false' }} }" class="border border-gray-700 rounded-xl overflow-hidden">
                    <button 
                        @click="open = !open" 
                        class="w-full flex items-center justify-between px-6 py-4 text-left bg-surface-dark hover:bg-gray-800 transition-colors"
                    >
                        <span class="font-semibold text-white pr-4">{{ $faq->question }}</span>
                        <span class="material-icons-outlined text-primary flex-shrink-0 transition-transform duration-300" :class="{ 'rotate-180': open }">
                            expand_more
                        </span>
                    </button>
                    <div 
                        x-show="open" 
                        x-collapse
                        class="px-6 py-4 bg-background-dark"
                    >
                        <p class="text-gray-400 leading-relaxed">{{ $faq->answer }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- CTA Section --}}
    <x-cta-section 
        :title="$aboutCta['title']"
        :description="$aboutCta['description']"
        :primaryText="$aboutCta['primary_text']"
        :primaryUrl="$aboutCta['primary_url']"
        :secondaryText="$aboutCta['secondary_text']"
        :secondaryUrl="$aboutCta['secondary_url']"
        background="navy"
    />
@endsection
