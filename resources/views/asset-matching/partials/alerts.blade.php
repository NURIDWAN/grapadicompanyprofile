@if(session('success'))
    <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-5 py-4 text-emerald-200">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="mb-6 rounded-xl border border-red-500/30 bg-red-500/10 px-5 py-4 text-red-200">
        <p class="font-semibold">Mohon periksa kembali data berikut:</p>
        <ul class="mt-2 list-disc pl-5 text-sm">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
