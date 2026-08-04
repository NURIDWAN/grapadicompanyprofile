@extends('layouts.app')

@section('title', $asset->name.' | Grapadi Asset Matching')
@section('description', $asset->name.' di '.$asset->city.', '.$asset->province.' — peluang aset terkurasi di Grapadi Asset Matching.')

@php($photoCount = $asset->photos->count())

@section('content')
<div class="min-h-screen bg-[#03150e] pt-20">
    {{-- Compact breadcrumb bar --}}
    <div class="border-b border-[#1e402f] bg-[#061d15] px-4 sm:px-6 lg:px-8">
        <div class="mx-auto flex min-h-12 max-w-[1500px] items-center justify-between gap-4">
            <nav class="flex min-w-0 items-center gap-2 text-[11px] text-gray-500" aria-label="Breadcrumb">
                <a href="{{ route('matching.index') }}" class="shrink-0 transition hover:text-primary">Asset Matching</a>
                <span>/</span>
                <span class="truncate text-gray-300">{{ $asset->category->name }}</span>
                <span>/</span>
                <span class="hidden truncate text-gray-500 sm:block">{{ $asset->name }}</span>
            </nav>
            <a href="{{ route('matching.index') }}" class="inline-flex shrink-0 items-center gap-1 text-[11px] font-semibold text-primary hover:text-white">
                <span class="material-icons-outlined text-sm">arrow_back</span> Katalog
            </a>
        </div>
    </div>

    <main class="px-3 py-3 sm:px-5 lg:px-6">
        <div class="mx-auto max-w-[1500px]">
            @include('asset-matching.partials.alerts')

            {{-- Gallery and primary information fill the first viewport --}}
            <div class="grid items-stretch gap-3 lg:grid-cols-[minmax(0,1.55fr)_minmax(340px,.75fr)]">
                <section class="flex min-w-0 flex-col overflow-hidden rounded-md border border-[#234634] bg-[#071f17]">
                    @if($asset->photos->isNotEmpty())
                        <div
                            x-data="{
                                active: 0,
                                total: {{ $photoCount }},
                                touchStart: 0,
                                next() { this.active = (this.active + 1) % this.total },
                                previous() { this.active = (this.active - 1 + this.total) % this.total },
                                finishSwipe(event) {
                                    const distance = event.changedTouches[0].clientX - this.touchStart;
                                    if (Math.abs(distance) > 45) distance < 0 ? this.next() : this.previous();
                                }
                            }"
                            @keydown.right.prevent="next()"
                            @keydown.left.prevent="previous()"
                            @touchstart.passive="touchStart = $event.touches[0].clientX"
                            @touchend.passive="finishSwipe($event)"
                            tabindex="0"
                            class="flex min-h-[380px] flex-1 flex-col bg-[#03150e] outline-none sm:min-h-[480px] lg:min-h-0"
                            aria-label="Galeri foto aset"
                        >
                            <div class="relative min-h-[300px] flex-1 overflow-hidden">
                                @foreach($asset->photos as $photo)
                                    <div
                                        x-show="active === {{ $loop->index }}"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 scale-[1.01]"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-200 absolute inset-0"
                                        x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0"
                                        {{ $loop->first ? '' : 'x-cloak' }}
                                        class="absolute inset-0"
                                    >
                                        <img
                                            src="{{ asset('storage/'.$photo->path) }}"
                                            alt="Foto {{ $asset->name }} {{ $loop->iteration }}"
                                            class="h-full w-full object-cover object-center"
                                        >
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/35 via-transparent to-black/10"></div>
                                    </div>
                                @endforeach

                                <span class="absolute left-4 top-4 z-10 rounded-sm bg-[#e9d38a] px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-wider text-[#092016]">{{ $asset->category->name }}</span>
                                <span class="absolute right-4 top-4 z-10 rounded-full bg-black/65 px-3 py-1.5 text-[10px] font-semibold text-white"><span x-text="active + 1"></span>/{{ $photoCount }}</span>

                                @if($photoCount > 1)
                                    <button type="button" @click="previous()" class="absolute left-3 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-white/20 bg-black/60 text-white backdrop-blur transition hover:border-primary hover:bg-primary hover:text-background-dark" aria-label="Foto sebelumnya">
                                        <span class="material-icons-outlined">chevron_left</span>
                                    </button>
                                    <button type="button" @click="next()" class="absolute right-3 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-white/20 bg-black/60 text-white backdrop-blur transition hover:border-primary hover:bg-primary hover:text-background-dark" aria-label="Foto berikutnya">
                                        <span class="material-icons-outlined">chevron_right</span>
                                    </button>
                                @endif
                            </div>

                            @if($photoCount > 1)
                                <div class="flex shrink-0 gap-2 overflow-x-auto border-t border-[#234634] bg-[#061a13] p-2 [scrollbar-color:#31533f_transparent]">
                                    @foreach($asset->photos as $photo)
                                        <button
                                            type="button"
                                            @click="active = {{ $loop->index }}"
                                            :class="active === {{ $loop->index }} ? 'border-primary opacity-100' : 'border-transparent opacity-55 hover:opacity-90'"
                                            class="relative h-14 w-20 shrink-0 overflow-hidden rounded border-2 transition"
                                            aria-label="Tampilkan foto {{ $loop->iteration }}"
                                        >
                                            <img src="{{ asset('storage/'.$photo->path) }}" alt="Thumbnail {{ $asset->name }} {{ $loop->iteration }}" class="h-full w-full object-cover">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="flex min-h-[520px] items-center justify-center bg-[linear-gradient(135deg,#103829,#071c14)] text-gray-600">
                            <div class="text-center"><span class="material-icons-outlined text-7xl">domain</span><p class="mt-2 text-sm">Foto aset belum tersedia</p></div>
                        </div>
                    @endif

                    <div class="mt-auto flex flex-wrap items-center justify-between gap-3 border-t border-[#234634] px-4 py-3">
                        <p class="flex items-center gap-2 text-[11px] text-gray-500">
                            <span class="material-icons-outlined text-base text-primary">photo_library</span>
                            {{ $asset->photos->count() }} foto aset
                        </p>
                        <p class="text-[10px] uppercase tracking-widest text-gray-600">Dipublikasikan {{ $asset->published_at?->format('d M Y') }}</p>
                    </div>
                </section>

                <aside class="flex min-w-0 flex-col rounded-md border border-[#234634] bg-[#071f17] p-5 lg:min-h-full">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-sm bg-primary/15 px-2.5 py-1 text-[9px] font-bold uppercase tracking-wider text-primary">{{ $asset->category->name }}</span>
                            <span class="rounded-sm border border-[#2c503d] px-2.5 py-1 text-[9px] font-semibold uppercase tracking-wider text-gray-400">{{ $asset->objective->label() }}</span>
                        </div>
                        <h1 class="mt-3 font-display text-3xl font-semibold leading-[1.02] text-white xl:text-4xl">{{ $asset->name }}</h1>
                        <p class="mt-3 flex items-start gap-2 text-sm text-gray-400">
                            <span class="material-icons-outlined mt-0.5 text-base text-primary">location_on</span>
                            {{ $asset->city }}, {{ $asset->province }}
                        </p>
                    </div>

                    <dl class="mt-4 grid grid-cols-2 gap-px overflow-hidden rounded-md border border-[#234634] bg-[#234634]">
                        @foreach([
                            ['straighten', 'Luas Aset', number_format((float) $asset->area_sqm, 0, ',', '.').' m²'],
                            ['description', 'Sertifikat', $asset->certificate_type],
                            ['verified', 'Kondisi', $asset->condition->label()],
                            ['account_balance', 'Kepemilikan', $asset->ownership_status->label()],
                            ['business_center', 'Pemanfaatan', $asset->utilization_status->label()],
                            ['flag', 'Tujuan', $asset->objective->label()],
                        ] as [$icon, $term, $value])
                            <div class="min-h-20 bg-[#082219] p-3">
                                <span class="material-icons-outlined text-base text-primary">{{ $icon }}</span>
                                <dt class="mt-1 text-[9px] uppercase tracking-wider text-gray-600">{{ $term }}</dt>
                                <dd class="mt-1 text-xs font-semibold leading-5 text-gray-100">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    <div class="mt-3 rounded-md border border-primary/20 bg-[#0b2a1f] p-3">
                        <div class="flex gap-3">
                            <span class="material-icons-outlined text-xl text-primary">lock</span>
                            <p class="text-[11px] leading-5 text-gray-400">Alamat lengkap, nomor sertifikat, dokumen, dan identitas pemilik dijaga oleh Grapadi.</p>
                        </div>
                    </div>

                    <div class="mt-auto pt-3">
                        @auth
                            @if(auth()->id() === $asset->owner_id)
                                <div class="flex gap-2">
                                    <a href="{{ route('matching.assets.edit', $asset) }}" class="inline-flex h-12 flex-1 items-center justify-center gap-2 rounded-md bg-primary px-4 text-xs font-bold text-background-dark hover:bg-primary-300">
                                        <span class="material-icons-outlined text-base">edit</span> Kelola Aset
                                    </a>
                                    <a href="{{ route('matching.dashboard') }}" class="inline-flex h-12 items-center justify-center rounded-md border border-[#31533f] px-4 text-xs text-gray-300 hover:border-primary hover:text-primary">Dashboard</a>
                                </div>
                            @else
                                <form method="POST" action="{{ route('matching.interests.store', $asset) }}">@csrf
                                    <button class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-md bg-primary px-5 text-sm font-bold text-background-dark transition hover:bg-primary-300">
                                        <span class="material-icons-outlined text-lg">handshake</span> Tambah Minat
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('matching.login', ['redirect' => url()->current()]) }}" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-md bg-primary px-5 text-sm font-bold text-background-dark transition hover:bg-primary-300">
                                <span class="material-icons-outlined text-lg">login</span> Masuk untuk Tambah Minat
                            </a>
                        @endauth
                        <p class="mt-2 text-center text-[9px] leading-4 text-gray-600">Minat akan diteruskan kepada tim Grapadi, bukan langsung kepada pemilik.</p>
                    </div>
                </aside>
            </div>

            {{-- Supporting details, deliberately dense with equal-height panels --}}
            <div class="mt-3 grid gap-3 lg:grid-cols-3">
                <section class="rounded-md border border-[#234634] bg-[#071f17] p-5">
                    <div class="flex items-center gap-3"><span class="material-icons-outlined text-xl text-primary">fact_check</span><h2 class="font-display text-xl font-semibold text-white">Kondisi Aset</h2></div>
                    <p class="mt-3 text-xs leading-6 text-gray-400">{{ $asset->condition_notes ?: 'Aset tercatat dalam kondisi '.$asset->condition->label().'. Detail tambahan dapat dibahas melalui tim Grapadi.' }}</p>
                </section>
                <section class="rounded-md border border-[#234634] bg-[#071f17] p-5">
                    <div class="flex items-center gap-3"><span class="material-icons-outlined text-xl text-primary">gavel</span><h2 class="font-display text-xl font-semibold text-white">Kepemilikan</h2></div>
                    <p class="mt-3 text-xs leading-6 text-gray-400">{{ $asset->ownership_notes ?: 'Status kepemilikan tercatat sebagai '.$asset->ownership_status->label().'. Dokumen legal tidak ditampilkan kepada publik.' }}</p>
                </section>
                <section class="rounded-md border border-[#234634] bg-[#071f17] p-5">
                    <div class="flex items-center gap-3"><span class="material-icons-outlined text-xl text-primary">apartment</span><h2 class="font-display text-xl font-semibold text-white">Pemanfaatan</h2></div>
                    <p class="mt-3 text-xs leading-6 text-gray-400">{{ $asset->utilization_notes ?: 'Aset saat ini berstatus '.$asset->utilization_status->label().'. Informasi lebih lanjut tersedia melalui proses matching.' }}</p>
                </section>
            </div>

            <section class="mt-3 grid overflow-hidden rounded-md border border-[#234634] bg-[#071f17] sm:grid-cols-2 lg:grid-cols-4">
                @foreach([
                    ['01', 'Data Terinventarisasi', 'Informasi dasar aset tercatat di sistem Grapadi.'],
                    ['02', 'Screening Internal', 'Kelengkapan, legalitas dasar, dan foto diperiksa.'],
                    ['03', 'Aset Dipublikasikan', 'Data sensitif tetap tersembunyi dari publik.'],
                    ['04', 'Minat Ditindaklanjuti', 'Tim Grapadi menghubungkan pihak yang relevan.'],
                ] as [$number, $title, $description])
                    <div class="border-[#234634] p-5 [&:not(:last-child)]:border-b sm:[&:not(:last-child)]:border-b-0 sm:[&:not(:last-child)]:border-r">
                        <span class="font-display text-2xl text-primary">{{ $number }}</span>
                        <h2 class="mt-2 text-xs font-semibold text-white">{{ $title }}</h2>
                        <p class="mt-1 text-[10px] leading-5 text-gray-500">{{ $description }}</p>
                    </div>
                @endforeach
            </section>

            <div class="mt-3 flex flex-col gap-3 rounded-md border border-primary/25 bg-[linear-gradient(90deg,#0d3023,#082219)] p-5 sm:flex-row sm:items-center sm:justify-between">
                <div><p class="text-[10px] font-semibold uppercase tracking-[.2em] text-primary">Perlu informasi lebih lanjut?</p><h2 class="mt-1 font-display text-2xl font-semibold text-white">Diskusikan peluang aset bersama Grapadi.</h2></div>
                <a href="{{ route('contact') }}" class="inline-flex h-11 shrink-0 items-center justify-center gap-2 rounded-md border border-primary bg-primary px-5 text-xs font-bold text-background-dark hover:bg-primary-300"><span class="material-icons-outlined text-base">chat</span> Hubungi Grapadi</a>
            </div>

            <p class="px-1 py-3 text-[9px] leading-4 text-gray-600">Screening dasar bukan valuasi, appraisal, studi kelayakan, financial model, atau rekomendasi investasi.</p>
        </div>
    </main>
</div>
@endsection
