@extends('layouts.app')

@section('title', site_setting('page_title_contact', 'Contact Us - Grapadi'))
@section('description', 'Get in touch with our team for inquiries and consultations')

@php
// Get contact info from site settings
$address = site_setting('site_address', '');
$email = site_setting('site_email', 'info@grapadi.com');
$phone = site_setting('site_phone', '');
$businessHours = site_setting('site_business_hours', 'Monday - Friday: 9:00 AM - 6:00 PM');

// Social links
$instagramUrl = site_setting('instagram_url', '');
$linkedinUrl = site_setting('linkedin_url', '');
$facebookUrl = site_setting('facebook_url', '');
$twitterUrl = site_setting('twitter_url', '');
@endphp

@section('content')
    {{-- Hero Section --}}
    <section class="bg-navy-brand py-12 sm:py-16 lg:py-20">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <span class="text-primary font-bold tracking-wider uppercase text-sm mb-2 block" data-animate="fade-in-up">Get In Touch</span>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold font-display text-white leading-tight mb-4 sm:mb-6" data-animate="fade-in-up" data-delay="100">
                Contact Us
            </h1>
            <p class="text-base sm:text-lg text-gray-300 max-w-2xl mx-auto">
                Ready to start your next project? Get in touch with our team.
            </p>
        </div>
    </section>

    {{-- Contact Form & Info --}}
    <section class="py-20 bg-background-dark">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20">
                {{-- Contact Form --}}
                <div class="bg-surface-dark p-8 lg:p-12 rounded-2xl shadow-lg" data-animate="fade-in-left">
                    <h2 class="text-2xl font-bold text-white mb-6">Kirim Pesan</h2>
                    @livewire('contact-form')
                </div>

                {{-- Contact Info --}}
                <div class="space-y-8" data-animate="fade-in-right" data-delay="200">
                    <div>
                        <h2 class="text-2xl font-bold text-white mb-6">Contact Information</h2>
                        <p class="text-gray-400 mb-8">
                            Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
                        </p>
                    </div>

                    <div class="space-y-6">
                        @if($address)
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center shrink-0">
                                <span class="material-icons-outlined text-primary">location_on</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-white mb-1">Office Address</h3>
                                <p class="text-gray-400 text-sm">
                                    {!! nl2br(e($address)) !!}
                                </p>
                            </div>
                        </div>
                        @endif

                        @if($email)
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center shrink-0">
                                <span class="material-icons-outlined text-primary">email</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-white mb-1">Email</h3>
                                <p class="text-gray-400 text-sm">
                                    {{ $email }}
                                </p>
                            </div>
                        </div>
                        @endif

                        @if($phone)
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center shrink-0">
                                <span class="material-icons-outlined text-primary">phone</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-white mb-1">Phone</h3>
                                <p class="text-gray-400 text-sm">
                                    {!! nl2br(e($phone)) !!}
                                </p>
                            </div>
                        </div>
                        @endif

                        @if($businessHours)
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center shrink-0">
                                <span class="material-icons-outlined text-primary">schedule</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-white mb-1">Business Hours</h3>
                                <p class="text-gray-400 text-sm">
                                    {!! nl2br(e($businessHours)) !!}
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Social Links --}}
                    @if($instagramUrl || $linkedinUrl || $facebookUrl || $twitterUrl)
                    <div class="pt-8 border-t border-gray-700">
                        <h3 class="font-bold text-white mb-4">Follow Us</h3>
                        <div class="flex gap-4">
                            @if($facebookUrl)
                            <a href="{{ $facebookUrl }}" target="_blank" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition">
                                <span class="material-icons-outlined">facebook</span>
                            </a>
                            @endif
                            @if($twitterUrl)
                            <a href="{{ $twitterUrl }}" target="_blank" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition">
                                <span class="material-icons-outlined">twitter</span>
                            </a>
                            @endif
                            @if($linkedinUrl)
                            <a href="{{ $linkedinUrl }}" target="_blank" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition">
                                <span class="material-icons-outlined">linkedin</span>
                            </a>
                            @endif
                            @if($instagramUrl)
                            <a href="{{ $instagramUrl }}" target="_blank" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition">
                                <span class="material-icons-outlined">instagram</span>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
