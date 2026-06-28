@props([
    'title' => 'Subscribe to Our Newsletter',
    'description' => 'Get the latest insights and research delivered directly to your inbox.',
    'source' => 'website',
    'dark' => false,
])

<section class="py-16 {{ $dark ? 'bg-navy-brand' : 'bg-background-dark' }}">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">
            {{ $title }}
        </h2>
        <p class="text-gray-400 mb-8">
            {{ $description }}
        </p>
        
        <form id="newsletter-form-{{ $source }}" class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto" onsubmit="subscribeNewsletter(event, '{{ $source }}')">
            @csrf
            <input type="hidden" name="source" value="{{ $source }}">
            <input 
                type="email" 
                name="email" 
                id="newsletter-email-{{ $source }}"
                placeholder="Enter your email" 
                required
                class="flex-1 px-4 py-3 rounded border border-gray-700 bg-surface-dark text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-primary outline-none"
            >
            <button 
                type="submit" 
                id="newsletter-btn-{{ $source }}"
                class="bg-primary hover:bg-primary-800 text-white font-bold px-6 py-3 rounded transition inline-flex items-center justify-center gap-2"
            >
                <span id="newsletter-btn-text-{{ $source }}">Subscribe</span>
                <svg id="newsletter-spinner-{{ $source }}" class="hidden animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </form>
        
        <p id="newsletter-message-{{ $source }}" class="mt-4 text-sm hidden"></p>
    </div>
</section>

@pushOnce('scripts')
<script>
function subscribeNewsletter(event, source) {
    event.preventDefault();
    
    const form = document.getElementById('newsletter-form-' + source);
    const email = document.getElementById('newsletter-email-' + source).value;
    const btn = document.getElementById('newsletter-btn-' + source);
    const btnText = document.getElementById('newsletter-btn-text-' + source);
    const spinner = document.getElementById('newsletter-spinner-' + source);
    const message = document.getElementById('newsletter-message-' + source);
    const csrfToken = form.querySelector('input[name="_token"]').value;
    
    // Show loading state
    btn.disabled = true;
    btnText.textContent = 'Subscribing...';
    spinner.classList.remove('hidden');
    message.classList.add('hidden');
    
    fetch('{{ route("newsletter.subscribe") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            email: email,
            source: source,
        }),
    })
    .then(response => response.json())
    .then(data => {
        message.classList.remove('hidden');
        
        if (data.success) {
            message.textContent = data.message;
            message.className = 'mt-4 text-sm text-green-600 dark:text-green-400';
            form.reset();
        } else {
            message.textContent = data.message;
            message.className = 'mt-4 text-sm text-red-600 dark:text-red-400';
        }
    })
    .catch(error => {
        message.classList.remove('hidden');
        message.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
        message.className = 'mt-4 text-sm text-red-600 dark:text-red-400';
    })
    .finally(() => {
        btn.disabled = false;
        btnText.textContent = 'Subscribe';
        spinner.classList.add('hidden');
    });
}
</script>
@endPushOnce
