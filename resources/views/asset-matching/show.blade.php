@extends('layouts.app')

@section('title', $asset->seo_title)
@section('description', $asset->meta_description)
@push('meta')
<link rel="canonical" href="{{ route('matching.show', $asset) }}">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $asset->seo_title }}">
<meta property="og:description" content="{{ $asset->meta_description }}">
<meta property="og:url" content="{{ route('matching.show', $asset) }}">
@if($asset->photos->first())<meta property="og:image" content="{{ asset('storage/'.$asset->photos->first()->path) }}">@endif
@endpush

@php($photoCount = $asset->photos->count())

@push('styles')
<style>
    /* Prevent the browser from hijacking pinch/pan gestures inside the lightbox. */
    .asset-lightbox-stage { touch-action: none; }
</style>
@endpush

@section('content')
@include('asset-matching.partials.gallery-script')
<div class="min-h-screen bg-[#03150e] pt-20">
    {{-- Compact breadcrumb bar --}}
    <div class="border-b border-[#1e402f] bg-[#061d15] px-4 sm:px-6 lg:px-8">
        <div class="mx-auto flex min-h-12 max-w-[1500px] items-center justify-between gap-4">
            <nav class="flex min-w-0 items-center gap-2 text-[11px] text-gray-500" aria-label="Breadcrumb">
                <a href="{{ route('matching.index') }}" class="shrink-0 transition hover:text-primary">Capital Connect</a>
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
                            x-data="assetGallery({{ $photoCount }})"
                            @keydown.right.prevent="next()"
                            @keydown.left.prevent="previous()"
                            @keydown.escape="closeLightbox()"
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
                                            alt="{{ $photo->alt_text ?: 'Foto '.$asset->name.' '.$loop->iteration }}"
                                            class="h-full w-full cursor-zoom-in object-cover object-center"
                                            @click="openLightbox({{ $loop->index }})"
                                        >
                                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/35 via-transparent to-black/10"></div>
                                    </div>
                                @endforeach

                                <span class="absolute left-4 top-4 z-10 rounded-sm bg-[#e9d38a] px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-wider text-[#092016]">{{ $asset->category->name }}</span>
                                <span class="absolute right-4 top-4 z-10 rounded-full bg-black/65 px-3 py-1.5 text-[10px] font-semibold text-white"><span x-text="active + 1"></span>/{{ $photoCount }}</span>

                                <button
                                    type="button"
                                    @click="openLightbox()"
                                    class="absolute bottom-4 left-4 z-10 inline-flex items-center gap-1.5 rounded-full border border-white/20 bg-black/65 px-3 py-2 text-[10px] font-semibold text-white backdrop-blur transition hover:border-primary hover:bg-primary hover:text-background-dark"
                                    aria-label="Perbesar foto"
                                >
                                    <span class="material-icons-outlined text-sm">zoom_in</span> Perbesar
                                </button>

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
                                            @click="show({{ $loop->index }})"
                                            :class="active === {{ $loop->index }} ? 'border-primary opacity-100' : 'border-transparent opacity-55 hover:opacity-90'"
                                            class="relative h-14 w-20 shrink-0 overflow-hidden rounded border-2 transition"
                                            aria-label="Tampilkan foto {{ $loop->iteration }}"
                                        >
                                            <img src="{{ asset('storage/'.$photo->path) }}" alt="Thumbnail: {{ $photo->alt_text ?: $asset->name }}" class="h-full w-full object-cover">
                                        </button>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Fullscreen zoomable lightbox --}}
                            <template x-teleport="body">
                                <div
                                    x-show="open"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    @keydown.window.escape="closeLightbox()"
                                    @keydown.window.right.prevent="open && next()"
                                    @keydown.window.left.prevent="open && previous()"
                                    class="fixed inset-0 z-[100] flex flex-col bg-black/95 backdrop-blur-sm"
                                    role="dialog"
                                    aria-modal="true"
                                    aria-label="Pratinjau foto aset"
                                >
                                    {{-- Toolbar --}}
                                    <div class="relative z-20 flex shrink-0 items-center justify-between gap-3 border-b border-white/10 px-4 py-3">
                                        <p class="min-w-0 truncate text-xs text-gray-300">
                                            <span class="font-semibold text-white">{{ $asset->name }}</span>
                                            <span class="ml-2 text-gray-500"><span x-text="active + 1"></span>/{{ $photoCount }}</span>
                                        </p>

                                        <div class="flex shrink-0 items-center gap-1.5">
                                            <span class="mr-1 hidden text-[10px] tabular-nums text-gray-400 sm:block" x-text="Math.round(scale * 100) + '%'"></span>

                                            <button type="button" @click="zoomOut()" :disabled="scale <= minScale" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/20 text-white transition hover:border-primary hover:text-primary disabled:cursor-not-allowed disabled:opacity-35" aria-label="Perkecil">
                                                <span class="material-icons-outlined text-lg">remove</span>
                                            </button>
                                            <button type="button" @click="zoomIn()" :disabled="scale >= maxScale" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/20 text-white transition hover:border-primary hover:text-primary disabled:cursor-not-allowed disabled:opacity-35" aria-label="Perbesar">
                                                <span class="material-icons-outlined text-lg">add</span>
                                            </button>
                                            <button type="button" @click="resetZoom()" x-show="zoomed" class="inline-flex h-9 items-center justify-center gap-1 rounded-full border border-white/20 px-3 text-[10px] font-semibold text-white transition hover:border-primary hover:text-primary" aria-label="Reset zoom">
                                                <span class="material-icons-outlined text-sm">restart_alt</span> Reset
                                            </button>
                                            <button type="button" @click="closeLightbox()" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/20 text-white transition hover:border-red-400 hover:text-red-400" aria-label="Tutup pratinjau">
                                                <span class="material-icons-outlined text-lg">close</span>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Zoom stage --}}
                                    <div
                                        x-ref="stage"
                                        class="asset-lightbox-stage relative flex-1 overflow-hidden"
                                        @wheel.prevent="onWheel($event)"
                                        @dblclick.prevent="toggleZoom($event)"
                                        @mousedown.prevent="startDrag($event)"
                                        @mousemove="onDrag($event)"
                                        @mouseup="endDrag()"
                                        @mouseleave="endDrag()"
                                        @touchstart="onTouchStart($event)"
                                        @touchmove.prevent="onTouchMove($event)"
                                        @touchend="onTouchEnd($event)"
                                        @click.self="closeLightbox()"
                                        :class="zoomed ? (dragging ? 'cursor-grabbing' : 'cursor-grab') : 'cursor-zoom-in'"
                                    >
                                        @foreach($asset->photos as $photo)
                                            <img
                                                x-show="active === {{ $loop->index }}"
                                                {{ $loop->first ? '' : 'x-cloak' }}
                                                data-index="{{ $loop->index }}"
                                                src="{{ asset('storage/'.$photo->path) }}"
                                                alt="{{ $photo->alt_text ?: 'Foto '.$asset->name.' '.$loop->iteration }}"
                                                class="pointer-events-none absolute inset-0 h-full w-full select-none object-contain"
                                                :style="`transform: translate(${tx}px, ${ty}px) scale(${scale}); transition: ${dragging || pinchStart ? 'none' : 'transform 150ms ease-out'}`"
                                                draggable="false"
                                            >
                                        @endforeach

                                        @if($photoCount > 1)
                                            <button type="button" x-show="!zoomed" @click.stop="previous()" class="absolute left-3 top-1/2 z-10 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-white/20 bg-black/60 text-white backdrop-blur transition hover:border-primary hover:bg-primary hover:text-background-dark" aria-label="Foto sebelumnya">
                                                <span class="material-icons-outlined">chevron_left</span>
                                            </button>
                                            <button type="button" x-show="!zoomed" @click.stop="next()" class="absolute right-3 top-1/2 z-10 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-white/20 bg-black/60 text-white backdrop-blur transition hover:border-primary hover:bg-primary hover:text-background-dark" aria-label="Foto berikutnya">
                                                <span class="material-icons-outlined">chevron_right</span>
                                            </button>
                                        @endif

                                        <p x-show="!zoomed" class="pointer-events-none absolute bottom-3 left-1/2 z-10 -translate-x-1/2 rounded-full bg-black/60 px-3 py-1.5 text-center text-[10px] text-gray-300 backdrop-blur">
                                            Scroll atau klik dua kali untuk zoom &middot; seret untuk menggeser
                                        </p>
                                    </div>

                                    {{-- Lightbox thumbnails --}}
                                    @if($photoCount > 1)
                                        <div class="flex shrink-0 justify-center gap-2 overflow-x-auto border-t border-white/10 px-3 py-2">
                                            @foreach($asset->photos as $photo)
                                                <button
                                                    type="button"
                                                    @click="show({{ $loop->index }})"
                                                    :class="active === {{ $loop->index }} ? 'border-primary opacity-100' : 'border-transparent opacity-50 hover:opacity-90'"
                                                    class="relative h-12 w-16 shrink-0 overflow-hidden rounded border-2 transition"
                                                    aria-label="Tampilkan foto {{ $loop->iteration }}"
                                                >
                                                    <img src="{{ asset('storage/'.$photo->path) }}" alt="Thumbnail: {{ $photo->alt_text ?: $asset->name }}" class="h-full w-full object-cover">
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </template>
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
                            <span class="rounded-sm border border-primary/40 px-2.5 py-1 text-[9px] font-semibold uppercase tracking-wider text-primary">{{ $asset->listing_status->label() }}</span>
                        </div>
                        <h1 class="mt-3 font-display text-3xl font-semibold leading-[1.02] text-white xl:text-4xl">{{ $asset->name }}</h1>
                        <p class="mt-3 flex items-start gap-2 text-sm text-gray-400">
                            <span class="material-icons-outlined mt-0.5 text-base text-primary">location_on</span>
                            {{ collect([$asset->village, $asset->district, $asset->city, $asset->province])->filter()->implode(', ') }}
                        </p>
                        <p class="mt-4 font-display text-2xl font-semibold text-primary">{{ $asset->price !== null ? 'Rp '.number_format((float) $asset->price, 0, ',', '.') : 'Hubungi Grapadi' }}</p>
                        @if($asset->price_per_sqm)<p class="mt-1 text-xs text-gray-500">Rp {{ number_format((float) $asset->price_per_sqm, 0, ',', '.') }}/m²</p>@endif
                    </div>

                    <dl class="mt-4 grid grid-cols-2 gap-px overflow-hidden rounded-md border border-[#234634] bg-[#234634]">
                        @foreach([
                            ['straighten', 'Luas Aset', number_format((float) $asset->area_sqm, 0, ',', '.').' m²'],
                            ['description', 'Sertifikat', $asset->certificate_type],
                            ['verified', 'Kondisi', $asset->condition->label()],
                            ['sell', 'Harga/m²', $asset->price_per_sqm ? 'Rp '.number_format((float) $asset->price_per_sqm, 0, ',', '.') : 'Hubungi Grapadi'],
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
                            <span class="material-icons-outlined text-xl text-primary">location_on</span>
                            <div><p class="text-[11px] leading-5 text-gray-300">{{ $asset->full_address }}</p>@if($asset->google_maps_url)<a href="{{ $asset->google_maps_url }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex items-center gap-1 text-[11px] font-semibold text-primary">Buka Google Maps <span aria-hidden="true">↗</span></a>@endif</div>
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
                            @elseif($asset->listing_status !== \App\Enums\AssetListingStatus::Closed)
                                <form method="POST" action="{{ route('matching.interests.store', $asset) }}">@csrf
                                    <button class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-md bg-primary px-5 text-sm font-bold text-background-dark transition hover:bg-primary-300">
                                        <span class="material-icons-outlined text-lg">handshake</span> Tambah Minat
                                    </button>
                                </form>
                            @else<div class="flex h-12 w-full items-center justify-center rounded-md border border-[#31533f] text-xs font-semibold text-gray-400">Aset tidak menerima minat baru</div>@endif
                        @else
                            @if($asset->listing_status !== \App\Enums\AssetListingStatus::Closed)<a href="{{ route('matching.login', ['redirect' => url()->current()]) }}" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-md bg-primary px-5 text-sm font-bold text-background-dark transition hover:bg-primary-300">
                                <span class="material-icons-outlined text-lg">login</span> Masuk untuk Tambah Minat
                            </a>@else<div class="flex h-12 w-full items-center justify-center rounded-md border border-[#31533f] text-xs font-semibold text-gray-400">Aset tidak menerima minat baru</div>@endif
                        @endauth
                        <p class="mt-2 text-center text-[9px] leading-4 text-gray-600">Minat akan diteruskan kepada tim Grapadi, bukan langsung kepada pemilik.</p>
                    </div>
                </aside>
            </div>

            {{-- Supporting details, deliberately dense with equal-height panels --}}
            <div class="mt-3 grid gap-3 lg:grid-cols-3">
                <section class="rounded-md border border-[#234634] bg-[#071f17] p-5">
                    <div class="flex items-center gap-3"><span class="material-icons-outlined text-xl text-primary">fact_check</span><h2 class="font-display text-xl font-semibold text-white">Kondisi Aset</h2></div>
                    <p class="mt-3 text-xs leading-6 text-gray-400">Aset tercatat dalam kondisi {{ $asset->condition->label() }}.</p>
                </section>
                <section class="rounded-md border border-[#234634] bg-[#071f17] p-5">
                    <div class="flex items-center gap-3"><span class="material-icons-outlined text-xl text-primary">description</span><h2 class="font-display text-xl font-semibold text-white">Deskripsi</h2></div>
                    <p class="mt-3 whitespace-pre-line text-xs leading-6 text-gray-400">{{ $asset->description }}</p>
                </section>
                <section class="rounded-md border border-[#234634] bg-[#071f17] p-5">
                    <div class="flex items-center gap-3"><span class="material-icons-outlined text-xl text-primary">apartment</span><h2 class="font-display text-xl font-semibold text-white">Pemanfaatan</h2></div>
                    <p class="mt-3 text-xs leading-6 text-gray-400">Aset saat ini berstatus {{ $asset->utilization_status->label() }} dengan tujuan {{ strtolower($asset->objective->label()) }}.</p>
                </section>
            </div>

            @if($asset->facilities->isNotEmpty())
            <section class="mt-3 rounded-md border border-[#234634] bg-[#071f17] p-5"><div class="flex items-center gap-3"><span class="material-icons-outlined text-xl text-primary">checklist</span><h2 class="font-display text-xl font-semibold text-white">Fasilitas</h2></div><div class="mt-4 grid gap-2 sm:grid-cols-2 lg:grid-cols-4">@foreach($asset->facilities as $facility)<div class="flex items-center gap-2 rounded-md border border-[#234634] bg-[#082219] px-3 py-2 text-xs text-gray-300"><span class="material-icons-outlined text-base text-primary">{{ $facility->icon ?: 'check_circle' }}</span>{{ $facility->name }}</div>@endforeach</div></section>
            @endif

            <section class="mt-3 grid overflow-hidden rounded-md border border-[#234634] bg-[#071f17] sm:grid-cols-2 lg:grid-cols-4">
                @foreach([
                    ['01', 'Data Terinventarisasi', 'Informasi dasar aset tercatat di sistem Grapadi.'],
                    ['02', 'Screening Internal', 'Kelengkapan, legalitas dasar, dan foto diperiksa.'],
                    ['03', 'Aset Dipublikasikan', 'Data sensitif tetap terlindungi.'],
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
