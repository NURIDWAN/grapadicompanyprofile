@props([
    'stats' => [
        ['icon' => 'schedule', 'number' => '18+', 'label' => 'Years Experience'],
        ['icon' => 'task_alt', 'number' => '500+', 'label' => 'Projects Completed'],
        ['icon' => 'groups', 'number' => '120+', 'label' => 'Corporate Clients'],
        ['icon' => 'trending_up', 'number' => 'Rp5T+', 'label' => 'Investment Analysed'],
    ]
])

{{-- Stats Section: 4-column grid on md+, 2-column on mobile (Requirements 5.1, 5.2) --}}
@if(is_array($stats) && count($stats) > 0)
<section {{ $attributes->merge(['class' => 'w-full px-4 sm:px-6 lg:px-8 py-12 md:py-16']) }}>
    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
            @foreach($stats as $stat)
                {{-- Requirement 5.5: Omit stat item if missing required fields --}}
                @if(isset($stat['icon']) && isset($stat['number']) && isset($stat['label']))
                <div class="flex flex-col items-center text-center rounded-xl border border-border-dark bg-surface-dark p-6 md:p-8">
                    {{-- Gold-colored Material Icon (Requirement 5.3) --}}
                    <span class="material-icons-outlined text-4xl md:text-5xl text-primary mb-3" aria-hidden="true">{{ $stat['icon'] }}</span>

                    {{-- Bold number at minimum 24px (Requirement 5.3) --}}
                    <p class="text-2xl md:text-3xl font-bold text-white mb-1">{{ $stat['number'] }}</p>

                    {{-- Descriptive label (Requirement 5.3) --}}
                    <p class="text-sm md:text-base text-gray-400">{{ $stat['label'] }}</p>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif
