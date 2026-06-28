@props(['items' => []])

<section class="py-20 bg-surface-dark relative">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-16">
            <h2 class="text-2xl sm:text-3xl font-bold font-display text-white mb-4">
                Milestones of Excellence
            </h2>
            <p class="text-gray-400">
                Key moments that shaped our history.
            </p>
        </div>
        <div class="relative">
            {{-- Central Line - visible on mobile as left border --}}
            <div class="absolute left-4 md:left-1/2 md:-translate-x-1/2 top-0 h-full w-1 bg-gray-700 rounded-full"></div>
            
            <div class="space-y-8 md:space-y-12">
                @foreach($items as $index => $item)
                    @php
                        $isEven = $index % 2 === 0;
                    @endphp
                    <div class="relative flex flex-col {{ $isEven ? 'md:flex-row' : 'md:flex-row-reverse' }} items-start md:items-center justify-between group">
                        
                        {{-- Spacer (hidden on mobile) --}}
                        <div class="hidden md:block order-1 md:w-5/12"></div>
                        
                        {{-- Center Dot --}}
                        <div class="absolute left-4 md:relative md:left-auto z-20 flex items-center order-1 bg-primary shadow-xl w-8 h-8 rounded-full border-4 border-surface-dark -translate-x-1/2 md:translate-x-0">
                        </div>
                        
                        {{-- Content Card --}}
                        <div class="ml-10 md:ml-0 order-1 w-[calc(100%-2.5rem)] md:w-5/12 px-4 sm:px-6 py-4 rounded-lg bg-background-dark shadow-md hover:shadow-xl transition-shadow border-t-4 border-primary text-left relative overflow-hidden">
                            {{-- Year Label --}}
                            <span class="inline-block bg-primary text-background-dark text-xs sm:text-sm font-semibold px-3 py-1 rounded-full mb-3">
                                {{ $item['year'] }}
                            </span>
                            
                            <div class="relative z-10">
                                @if(!empty($item['image']))
                                <img
                                    alt="{{ $item['title'] }}"
                                    class="w-full h-32 sm:h-48 object-cover rounded mb-4 filter grayscale group-hover:grayscale-0 transition duration-500"
                                    src="{{ $item['image'] }}"
                                />
                                @endif
                                <h3 class="mb-2 font-bold text-lg sm:text-xl text-white font-display">
                                    {{ $item['title'] }}
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-400 leading-relaxed">
                                    {{ $item['description'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
