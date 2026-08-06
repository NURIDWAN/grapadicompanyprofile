@extends('layouts.app')
@section('title', 'Profil Capital Connect | Grapadi')
@section('content')
<section class="min-h-screen px-6 pb-20 pt-32">
    <div class="mx-auto max-w-xl rounded-2xl border border-border-dark bg-surface-dark p-8 shadow-2xl">
        <p class="text-sm font-semibold uppercase tracking-widest text-primary">Capital Connect</p>
        <h1 class="mt-2 font-display text-4xl font-semibold text-white">Lengkapi profil</h1>
        <p class="mt-2 text-gray-400">Informasi ini membantu tim Grapadi menghubungi Anda. Semua kolom bersifat opsional dan dapat diperbarui kapan saja.</p>
        <div class="mt-6">@include('asset-matching.partials.alerts')</div>

        <form method="POST" action="{{ route('matching.profile.update') }}" class="mt-6 space-y-5">
            @csrf
            @method('PUT')
            <label class="block text-sm text-gray-300">Nama perusahaan <span class="text-gray-500">(opsional)</span>
                <input name="company" maxlength="100" value="{{ old('company', auth()->user()->company) }}" placeholder="Contoh: PT Grapadi Indonesia" class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white outline-none focus:border-primary">
            </label>
            <label class="block text-sm text-gray-300">Nomor WhatsApp <span class="text-gray-500">(opsional)</span>
                <input name="whatsapp" inputmode="tel" maxlength="20" value="{{ old('whatsapp', auth()->user()->whatsapp) }}" placeholder="08xxxxxxxxxx" class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white outline-none focus:border-primary">
            </label>
            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                <button type="submit" form="skip-profile-form" class="rounded-xl border border-border-dark px-5 py-3 text-gray-300 hover:border-primary/50">Lewati</button>
                <button type="submit" class="rounded-xl bg-primary px-5 py-3 font-semibold text-background-dark hover:bg-primary-400">Simpan profil</button>
            </div>
        </form>

        <form id="skip-profile-form" method="POST" action="{{ route('matching.profile.skip') }}">@csrf</form>
    </div>
</section>
@endsection
