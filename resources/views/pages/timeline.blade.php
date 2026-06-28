@extends('layouts.app')

@section('title', site_setting('page_title_timeline', 'Our Journey - Grapadi'))
@section('description', 'The milestones that defined our path to excellence.')

@section('content')
    <div class="pt-20">
        @if(count($history) > 0)
            <x-timeline :items="$history" />
        @else
            {{-- Empty state --}}
            <section class="py-20 bg-surface-dark">
                <div class="max-w-6xl mx-auto px-4 sm:px-6">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-800 rounded-full mb-6">
                            <span class="material-icons-outlined text-4xl text-gray-400">timeline</span>
                        </div>
                        <h2 class="text-2xl font-bold font-display text-white mb-4">
                            Milestones Coming Soon
                        </h2>
                        <p class="text-gray-400 max-w-md mx-auto">
                            Our journey timeline is being prepared. Stay tuned for the key moments that shaped our history.
                        </p>
                    </div>
                </div>
            </section>
        @endif
    </div>

    {{-- CTA Section --}}
    <x-cta-section 
        title="Ready to work with us?"
        description="Whether you're looking for insights to grow your business or a career to grow your potential, we want to hear from you."
        primaryText="Join Our Team"
        primaryUrl="/careers"
        secondaryText="Contact Us"
        secondaryUrl="/contact"
        background="navy"
    />
@endsection

