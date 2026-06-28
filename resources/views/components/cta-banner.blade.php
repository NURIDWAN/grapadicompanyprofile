@props([
    'title' => 'Ready to Transform Insight into Action?',
    'description' => '',
    'ctaText' => 'Book Consultation',
    'ctaUrl' => '/contact',
])

<section class="w-full px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto bg-surface-dark border border-primary/20 rounded-2xl py-12 md:py-16 px-6 sm:px-10 md:px-16 text-center">
        <h2 class="font-display text-2xl sm:text-3xl md:text-4xl font-bold text-primary max-w-[80ch] mx-auto mb-4 leading-tight">
            {{ Str::limit($title, 80) }}
        </h2>

        @if($description)
            <p class="text-gray-400 text-base sm:text-lg max-w-2xl mx-auto mb-8">
                {{ $description }}
            </p>
        @endif

        <div class="@if(!$description) mt-8 @endif">
            <a
                href="{{ $ctaUrl }}"
                class="inline-block bg-primary hover:bg-primary-400 text-background-dark font-bold text-base sm:text-lg py-3 px-8 rounded-lg transition-colors duration-200 min-h-[44px] min-w-[44px] focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background-dark"
            >
                {{ $ctaText }}
            </a>
        </div>
    </div>
</section>
