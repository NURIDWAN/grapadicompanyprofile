@extends('layouts.app')
@section('title', 'Grapadi Asset Matching')
@section('description', 'Temukan peluang aset terkurasi dan hubungkan kebutuhan Anda melalui Grapadi.')
@section('content')
<section class="border-b border-border-dark bg-surface-dark pt-32 pb-16 px-6">
    <div class="mx-auto max-w-7xl"><div class="max-w-3xl"><p class="text-sm font-semibold uppercase tracking-[.25em] text-primary">Grapadi Asset Matching</p><h1 class="mt-4 font-display text-5xl font-semibold text-white md:text-6xl">Mempertemukan aset dengan peluang terbaik.</h1><p class="mt-5 text-lg leading-relaxed text-gray-300">Inventaris aset yang telah melalui screening data dasar Grapadi. Informasi sensitif tetap terlindungi.</p></div>
    <div class="mt-8 flex flex-wrap gap-3">@auth<a href="{{ route('matching.dashboard') }}" class="rounded-xl bg-primary px-6 py-3 font-semibold text-background-dark">Buka Dashboard</a>@else<a href="{{ route('matching.register') }}" class="rounded-xl bg-primary px-6 py-3 font-semibold text-background-dark">Daftarkan Aset</a><a href="{{ route('matching.login') }}" class="rounded-xl border border-primary px-6 py-3 font-semibold text-primary">Masuk</a>@endauth</div></div>
</section>
<section class="px-6 py-14"><div class="mx-auto max-w-7xl">
    @include('asset-matching.partials.alerts')
    <form method="GET" class="grid gap-3 rounded-2xl border border-border-dark bg-surface-dark p-5 md:grid-cols-7">
        <input name="q" value="{{ request('q') }}" placeholder="Cari nama aset" class="rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white md:col-span-2">
        <select name="category" class="rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white"><option value="">Semua kategori</option>@foreach($categories as $category)<option value="{{ $category->slug }}" @selected(request('category')===$category->slug)>{{ $category->name }}</option>@endforeach</select>
        <select name="province" class="rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white"><option value="">Semua provinsi</option>@foreach($provinces as $province)<option @selected(request('province')===$province)>{{ $province }}</option>@endforeach</select>
        <select name="city" class="rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white"><option value="">Semua kota</option>@foreach($cities as $city)<option @selected(request('city')===$city)>{{ $city }}</option>@endforeach</select>
        <select name="objective" class="rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white"><option value="">Semua tujuan</option>@foreach($objectives as $value=>$label)<option value="{{ $value }}" @selected(request('objective')===$value)>{{ $label }}</option>@endforeach</select>
        <button class="rounded-xl bg-primary px-5 py-3 font-semibold text-background-dark">Cari Aset</button>
    </form>
    <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($assets as $asset)
        <article class="overflow-hidden rounded-2xl border border-border-dark bg-surface-dark transition hover:-translate-y-1 hover:border-primary/50">
            <a href="{{ route('matching.show', $asset) }}">@if($asset->photos->first())<img src="{{ asset('storage/'.$asset->photos->first()->path) }}" alt="{{ $asset->name }}" class="h-56 w-full object-cover">@else<div class="flex h-56 items-center justify-center bg-background-dark text-gray-500">Foto aset</div>@endif</a>
            <div class="p-6"><div class="flex items-center justify-between gap-3"><span class="rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">{{ $asset->category->name }}</span><span class="text-xs text-gray-400">{{ $asset->objective->label() }}</span></div><h2 class="mt-4 font-display text-2xl font-semibold text-white"><a href="{{ route('matching.show', $asset) }}">{{ $asset->name }}</a></h2><p class="mt-2 text-sm text-gray-400">{{ $asset->city }}, {{ $asset->province }} · {{ number_format((float)$asset->area_sqm, 0, ',', '.') }} m²</p></div>
        </article>
        @empty<div class="rounded-2xl border border-border-dark bg-surface-dark p-10 text-center text-gray-400 md:col-span-2 lg:col-span-3">Belum ada aset yang sesuai dengan pencarian Anda.</div>@endforelse
    </div><div class="mt-10">{{ $assets->links() }}</div>
</div></section>
@endsection
