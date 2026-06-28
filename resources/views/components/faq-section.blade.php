@props([
    'title' => 'Pertanyaan yang Sering Diajukan',
    'faqs' => [] // Array of ['question' => '', 'answer' => '']
])

@if(count($faqs) > 0)
<section class="py-6 md:py-10">
    {{-- Section Title --}}
    <div class="text-center mb-8 md:mb-10">
        <p class="text-xs text-primary uppercase tracking-[0.2em] font-semibold font-display mb-3">FAQ</p>
        <h2 class="text-2xl md:text-3xl font-display font-bold text-white">
            {{ $title }}
        </h2>
    </div>

    {{-- FAQ Grid: 2-column on md+, single column on mobile --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
        @foreach($faqs as $index => $faq)
            @php
                $faqId = 'faq-answer-' . $index;
            @endphp
            <div
                x-data="{ isOpen: false }"
                class="bg-surface-dark border border-border-dark rounded-xl overflow-hidden"
            >
                {{-- Question Toggle --}}
                <div
                    role="button"
                    tabindex="0"
                    :aria-expanded="isOpen.toString()"
                    aria-controls="{{ $faqId }}"
                    @click="isOpen = !isOpen"
                    @keydown.enter.prevent="isOpen = !isOpen"
                    @keydown.space.prevent="isOpen = !isOpen"
                    class="w-full flex items-center justify-between p-4 md:p-5 text-left cursor-pointer select-none min-h-[44px] focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-surface-dark rounded-xl"
                >
                    <span class="text-sm md:text-base font-semibold text-white pr-4">
                        {{ $faq['question'] }}
                    </span>
                    <span
                        class="material-icons-outlined text-primary flex-shrink-0 transition-transform duration-300"
                        :class="isOpen ? 'rotate-180' : ''"
                    >
                        expand_more
                    </span>
                </div>

                {{-- Answer (visible by default for graceful degradation, hidden by Alpine on init) --}}
                <div
                    id="{{ $faqId }}"
                    x-show="isOpen"
                    x-collapse
                    x-cloak
                >
                    <div class="px-4 md:px-5 pb-4 md:pb-5 text-sm text-gray-400 leading-relaxed">
                        {{ $faq['answer'] }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- Graceful degradation: show all answers when Alpine.js fails to load --}}
<noscript>
    <style>
        /* When JS is unavailable, show all FAQ answers and hide the chevron rotation */
        [x-cloak] {
            display: block !important;
        }
    </style>
</noscript>
@endif
