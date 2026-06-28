@extends('layouts.app')

@section('title', site_setting('page_title_portfolio', 'Portfolio - Grapadi'))
@section('description', 'Explore our successful projects and client results')

@section('content')
    {{-- Hero Section --}}
    <section class="bg-navy-brand py-16 lg:py-28">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <span class="text-primary font-bold tracking-wider uppercase text-sm mb-2 block" data-animate="fade-in-up">Our Work</span>
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold font-display text-white leading-tight mb-4 sm:mb-6" data-animate="fade-in-up" data-delay="100">
                Portfolio & Case Studies
            </h1>
            <p class="text-base sm:text-lg text-gray-300 max-w-2xl mx-auto">
                Discover how we've supported clients at every step of their journey with data-driven insights and strategic solutions.
            </p>
        </div>
    </section>

    {{-- Portfolio Grid --}}
    <section class="py-20 bg-background-dark">
        <div class="max-w-7xl mx-auto px-4">
            {{-- Category Filter --}}
            @if($portfolioCategories->count() > 0)
            <div class="flex flex-wrap gap-3 mb-10 justify-center" data-animate="fade-in-up">
                <a href="{{ url('/portfolio') }}" 
                   class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-300 
                          {{ !$currentCategory ? 'bg-primary text-white shadow-lg' : 'bg-surface-dark text-gray-300 hover:bg-gray-700 border border-gray-600' }}">
                    All Projects
                </a>
                @foreach($portfolioCategories as $category)
                <a href="{{ url('/portfolio?category=' . $category->slug) }}" 
                   class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-300
                          {{ $currentCategory && $currentCategory->id === $category->id ? 'bg-primary text-white shadow-lg' : 'bg-surface-dark text-gray-300 hover:bg-gray-700 border border-gray-600' }}">
                    {{ $category->name }}
                    <span class="ml-1 text-xs opacity-70">({{ $category->portfolios_count }})</span>
                </a>
                @endforeach
            </div>
            @endif

            {{-- Current Filter Info --}}
            @if($currentCategory)
            <div class="mb-8 text-center">
                <p class="text-gray-400">
                    Showing projects in <span class="font-semibold text-primary">{{ $currentCategory->name }}</span>
                    <a href="{{ url('/portfolio') }}" class="ml-2 text-sm text-gray-500 hover:text-primary underline">Clear filter</a>
                </p>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($portfolios as $index => $portfolio)
                <div class="bg-surface-dark rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 group hover:-translate-y-1" data-animate="fade-in-up" data-delay="{{ ($index % 6) * 100 }}">
                    {{-- Image --}}
                    <div class="h-56 overflow-hidden relative">
                        <img alt="{{ $portfolio->project_title }}" 
                             class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500" 
                             src="{{ $portfolio->image_url ?? 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800' }}">
                        {{-- Category Badge --}}
                        @if($portfolio->category)
                        <div class="absolute top-4 left-4">
                            <span class="bg-primary/90 text-white text-xs font-bold px-3 py-1.5 rounded-full backdrop-blur-sm">
                                {{ $portfolio->category->name }}
                            </span>
                        </div>
                        @endif
                        {{-- Year Badge --}}
                        <div class="absolute top-4 right-4">
                            <span class="bg-gray-800/90 text-gray-200 text-xs font-bold px-3 py-1.5 rounded-full backdrop-blur-sm">
                                {{ $portfolio->project_year }}
                            </span>
                        </div>
                    </div>
                    
                    {{-- Card Content --}}
                    <div class="p-6">
                        {{-- Project Title --}}
                        <h3 class="text-lg font-bold text-white mb-2 line-clamp-2 group-hover:text-primary transition">
                            {{ $portfolio->project_title }}
                        </h3>
                        
                        {{-- Company Name --}}
                        <p class="text-primary text-sm font-semibold mb-3">
                            {{ $portfolio->client_name }}
                        </p>
                        
                        {{-- Location --}}
                        @if($portfolio->location)
                        <div class="flex items-center gap-2 text-sm text-gray-400 mb-4">
                            <span class="material-icons-outlined text-lg">location_on</span>
                            {{ $portfolio->location }}
                        </div>
                        @endif

                        {{-- Service Badge --}}
                        @if($portfolio->service)
                        <div class="pt-4 border-t border-gray-700">
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-400 bg-gray-700 px-3 py-1 rounded-full">
                                <span class="material-icons-outlined text-sm">work_outline</span>
                                {{ $portfolio->service->service_name }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <span class="material-icons-outlined text-6xl text-gray-600 mb-4">folder_open</span>
                    <h3 class="text-xl font-semibold text-gray-400 mb-2">No projects found</h3>
                    <p class="text-gray-500">
                        @if($currentCategory)
                            No projects in this category yet.
                            <a href="{{ url('/portfolio') }}" class="text-primary hover:underline">View all projects</a>
                        @else
                            Portfolio is empty.
                        @endif
                    </p>
                </div>
                @endforelse
            </div>
        </div>
    </section>


    {{-- CTA Section --}}
    <x-cta-section 
        title="Have a project in mind?"
        description="Let's discuss how we can help you achieve your business goals with our data-driven approach."
        primaryText="Start a Project"
        primaryUrl="/contact"
        background="navy"
    />
@endsection
