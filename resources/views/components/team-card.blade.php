@props([
    'name' => 'Team Member',
    'position' => 'Position',
    'photo' => null,
    'bio' => '',
    'linkedin' => null
])

<div class="bg-background-dark rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition">
    <div class="h-64 overflow-hidden">
        @if($photo)
        <img alt="{{ $name }}" class="w-full h-full object-cover object-top hover:scale-110 transition duration-500" src="{{ $photo }}">
        @else
        <div class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
            <span class="material-icons-outlined text-6xl text-gray-400">person</span>
        </div>
        @endif
    </div>
    <div class="p-6 text-center">
        <h3 class="text-lg font-bold text-navy-brand dark:text-white">{{ $name }}</h3>
        <p class="text-primary text-sm font-semibold mb-3">{{ $position }}</p>
        @if($bio)
        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">{{ $bio }}</p>
        @endif
        @if($linkedin)
        <a href="{{ $linkedin }}" target="_blank" class="inline-flex items-center gap-1 mt-3 text-[#0077b5] hover:text-[#005582] transition group/link">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
            </svg>
            <span class="text-sm font-medium">LinkedIn</span>
        </a>
        @endif
        {{-- Accordion Section --}}
        <div class="mt-4 space-y-2 text-left" x-data="{ active: null }">
            {{-- Experience and Expertise --}}
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-gray-50 dark:bg-gray-800 shadow-sm">
                <button @click="active = (active === 1 ? null : 1)" class="w-full px-4 py-3 text-left flex justify-between items-center focus:outline-none hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <span class="font-bold text-xs uppercase tracking-wider text-navy-brand dark:text-white">Experience & Expertise</span>
                    <span class="material-icons-outlined text-sm transform transition-transform duration-300" :class="{ 'rotate-180': active === 1 }">expand_more</span>
                </button>
                <div x-show="active === 1" x-collapse class="px-4 py-3 text-gray-600 dark:text-gray-300 text-xs leading-relaxed border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-background-dark">
                    Extensive background in strategic planning, digital transformation, and market analysis across Southeast Asia. Specializes in helping scalable businesses optimize disparate operations.
                </div>
            </div>

            {{-- Notable Projects and Clients --}}
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-gray-50 dark:bg-gray-800 shadow-sm">
                <button @click="active = (active === 2 ? null : 2)" class="w-full px-4 py-3 text-left flex justify-between items-center focus:outline-none hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <span class="font-bold text-xs uppercase tracking-wider text-navy-brand dark:text-white">Notable Projects</span>
                    <span class="material-icons-outlined text-sm transform transition-transform duration-300" :class="{ 'rotate-180': active === 2 }">expand_more</span>
                </button>
                <div x-show="active === 2" x-collapse class="px-4 py-3 text-gray-600 dark:text-gray-300 text-xs leading-relaxed border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-background-dark">
                    Led key initiatives for Fortune 500 companies in finance and tech sectors. Successfully delivered high-impact feasibility studies and restructuring plans for regional conglomerates.
                </div>
            </div>

            {{-- Achievements and Contributions --}}
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-gray-50 dark:bg-gray-800 shadow-sm">
                <button @click="active = (active === 3 ? null : 3)" class="w-full px-4 py-3 text-left flex justify-between items-center focus:outline-none hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <span class="font-bold text-xs uppercase tracking-wider text-navy-brand dark:text-white">Achievements</span>
                    <span class="material-icons-outlined text-sm transform transition-transform duration-300" :class="{ 'rotate-180': active === 3 }">expand_more</span>
                </button>
                <div x-show="active === 3" x-collapse class="px-4 py-3 text-gray-600 dark:text-gray-300 text-xs leading-relaxed border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-background-dark">
                    Recognized for excellence in consulting and leadership. Key contributor to industry whitepapers on emerging market trends and sustainable business practices.
                </div>
            </div>

            {{-- Vision and Mission --}}
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-gray-50 dark:bg-gray-800 shadow-sm">
                <button @click="active = (active === 4 ? null : 4)" class="w-full px-4 py-3 text-left flex justify-between items-center focus:outline-none hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <span class="font-bold text-xs uppercase tracking-wider text-navy-brand dark:text-white">Vision & Mission</span>
                    <span class="material-icons-outlined text-sm transform transition-transform duration-300" :class="{ 'rotate-180': active === 4 }">expand_more</span>
                </button>
                <div x-show="active === 4" x-collapse class="px-4 py-3 text-gray-600 dark:text-gray-300 text-xs leading-relaxed border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-background-dark">
                    Driven by a commitment to data integrity and actionable insights. Aims to empower clients to build resilient, future-proof organizations through evidence-based strategies.
                </div>
            </div>
        </div>
    </div>
</div>
