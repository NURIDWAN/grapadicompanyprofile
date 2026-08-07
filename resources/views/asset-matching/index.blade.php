@extends('layouts.app')

@section('title', 'Capital Connect')
@section('description', 'Temukan peluang aset terkurasi dan hubungkan kebutuhan Anda melalui Grapadi.')

@section('content')
<div class="min-h-screen bg-[#03150e] pt-20">
    {{-- Hero --}}
    <section class="relative isolate overflow-hidden border-b border-[#204331] bg-[#03150e]">
        <img
            src="{{ asset('image/background/image.png') }}"
            alt="Gedung komersial Capital Connect"
            class="absolute inset-0 -z-30 h-full w-full object-cover object-[68%_center] sm:object-[72%_center]"
        >
        <div class="absolute inset-0 -z-20 bg-[linear-gradient(90deg,#02110b_0%,#041b12_42%,rgba(4,27,18,.82)_55%,rgba(3,21,14,.2)_100%)]"></div>
        <div class="absolute inset-0 -z-20 bg-[linear-gradient(0deg,#03150e_0%,rgba(3,21,14,.12)_46%,rgba(3,21,14,.35)_100%)]"></div>
        <div class="absolute -left-32 top-10 -z-10 h-96 w-96 rounded-full bg-primary/5 blur-3xl"></div>

        <div class="mx-auto grid min-h-[560px] max-w-[1500px] items-center px-5 py-12 sm:px-8 sm:py-16 lg:grid-cols-12 lg:px-10 lg:py-14">
            <div class="lg:col-span-7 xl:col-span-6">
                <h1 class="font-display text-[3.25rem] font-medium leading-none tracking-[-.025em] text-primary sm:text-6xl lg:text-[4.5rem]">
                    Capital Connect
                </h1>
                <p class="mt-4 max-w-2xl font-display text-2xl font-medium leading-tight text-white sm:text-3xl lg:text-[2.15rem]">Connecting Capital, Assets &amp; Strategic Opportunities.</p>
                <div class="mt-7 flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('matching.dashboard') }}" class="inline-flex min-h-12 items-center gap-2 rounded-md bg-primary px-6 py-3 text-xs font-bold text-[#092016] transition hover:-translate-y-0.5 hover:bg-primary-300">
                            Buka Dashboard <span aria-hidden="true">→</span>
                        </a>
                    @else
                        <a href="{{ route('matching.register') }}" class="inline-flex min-h-12 items-center gap-2 rounded-md bg-primary px-6 py-3 text-xs font-bold text-[#092016] transition hover:-translate-y-0.5 hover:bg-primary-300">
                            Daftarkan Aset <span aria-hidden="true">→</span>
                        </a>
                        <a href="{{ route('matching.login') }}" class="inline-flex min-h-12 items-center rounded-md border border-primary/50 bg-[#061b13]/80 px-6 py-3 text-xs font-semibold text-primary backdrop-blur transition hover:border-primary hover:bg-primary/10">
                            Masuk
                        </a>
                    @endauth
                </div>

            </div>
        </div>
    </section>

    {{-- Catalog --}}
    <section class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-[1500px]">
            @include('asset-matching.partials.alerts')

            {{-- Category tabs --}}
            <div class="flex gap-1 overflow-x-auto border-b border-[#204331] pb-0 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <a
                    href="{{ route('matching.index', request()->except(['category', 'page'])) }}"
                    class="shrink-0 border-b-2 px-4 py-3 text-xs font-semibold transition {{ request('category') ? 'border-transparent text-gray-400 hover:text-white' : 'border-primary text-primary' }}"
                >
                    Semua
                </a>
                @foreach($categories as $category)
                    <a
                        href="{{ route('matching.index', [...request()->except(['category', 'page']), 'category' => $category->slug]) }}"
                        class="shrink-0 border-b-2 px-4 py-3 text-xs font-semibold transition {{ request('category') === $category->slug ? 'border-primary text-primary' : 'border-transparent text-gray-400 hover:text-white' }}"
                    >
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>

            {{-- Filters --}}
            <form method="GET" class="mt-4 grid gap-2 sm:grid-cols-2 lg:grid-cols-[minmax(260px,2fr)_repeat(4,minmax(130px,1fr))_auto]">
                <label class="relative block">
                    <span class="material-icons-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-base text-gray-500">search</span>
                    <input
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari nama aset atau peluang..."
                        class="h-11 w-full rounded-md border border-[#244735] bg-[#082219] pl-10 pr-4 text-xs text-white outline-none transition placeholder:text-gray-500 focus:border-primary"
                    >
                </label>
                <select name="province" class="h-11 rounded-md border border-[#244735] bg-[#082219] px-3 text-xs text-gray-300 outline-none focus:border-primary">
                    <option value="">Semua provinsi</option>
                    @foreach($provinces as $province)<option @selected(request('province') === $province)>{{ $province }}</option>@endforeach
                </select>
                <select name="city" class="h-11 rounded-md border border-[#244735] bg-[#082219] px-3 text-xs text-gray-300 outline-none focus:border-primary">
                    <option value="">Semua kota</option>
                    @foreach($cities as $city)<option @selected(request('city') === $city)>{{ $city }}</option>@endforeach
                </select>
                <select name="objective" class="h-11 rounded-md border border-[#244735] bg-[#082219] px-3 text-xs text-gray-300 outline-none focus:border-primary">
                    <option value="">Semua tujuan</option>
                    @foreach($objectives as $value => $label)<option value="{{ $value }}" @selected(request('objective') === $value)>{{ $label }}</option>@endforeach
                </select>
                <select name="category" class="h-11 rounded-md border border-[#244735] bg-[#082219] px-3 text-xs text-gray-300 outline-none focus:border-primary lg:hidden">
                    <option value="">Semua kategori</option>
                    @foreach($categories as $category)<option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>@endforeach
                </select>
                <button class="inline-flex h-11 items-center justify-center gap-2 rounded-md bg-primary px-5 text-xs font-bold text-[#092016] transition hover:bg-primary-300">
                    <span class="material-icons-outlined text-base">filter_alt</span> Filter
                </button>
                @if(request()->hasAny(['q', 'category', 'province', 'city', 'objective']))
                    <a href="{{ route('matching.index') }}" class="inline-flex h-11 items-center justify-center rounded-md border border-[#244735] px-4 text-xs text-gray-400 hover:text-white">Reset</a>
                @endif
            </form>

            <div class="mt-5 flex items-center justify-between">
                <p class="text-xs text-gray-500"><span class="font-semibold text-gray-300">{{ $assets->total() }}</span> aset tersedia</p>
                <p class="hidden text-[10px] uppercase tracking-widest text-gray-600 sm:block">Screening dasar oleh Grapadi</p>
            </div>

            <div class="mt-3 grid items-start gap-5 xl:grid-cols-[minmax(0,1fr)_310px]">
                {{-- Asset cards --}}
                <div>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse($assets as $asset)
                            <article class="group overflow-hidden rounded-md border border-[#234634] bg-[#082219] shadow-[0_12px_30px_rgba(0,0,0,.16)] transition hover:-translate-y-0.5 hover:border-primary/55">
                                <a href="{{ route('matching.show', $asset) }}" class="relative block overflow-hidden">
                                    @if($asset->photos->first())
                                        <img src="{{ asset('storage/'.$asset->photos->first()->path) }}" alt="{{ $asset->photos->first()->alt_text ?: $asset->name }}" class="h-40 w-full object-cover transition duration-500 group-hover:scale-[1.03]">
                                    @else
                                        <div class="flex h-40 items-center justify-center bg-[linear-gradient(135deg,#103829,#071c14)] text-gray-500">
                                            <span class="material-icons-outlined text-4xl">domain</span>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-[#03150e]/70 via-transparent to-transparent"></div>
                                    <span class="absolute left-3 top-3 rounded-sm bg-[#e9d38a] px-2 py-1 text-[9px] font-extrabold uppercase tracking-wide text-[#092016]">{{ $asset->category->name }}</span>
                                    <span class="absolute right-3 top-3 rounded-sm bg-black/70 px-2 py-1 text-[9px] font-semibold uppercase text-white">{{ $asset->listing_status->label() }}</span>
                                </a>

                                <div class="p-4">
                                    <h2 class="min-h-12 font-display text-xl font-semibold leading-tight text-white">
                                        <a href="{{ route('matching.show', $asset) }}" class="transition hover:text-primary">{{ $asset->name }}</a>
                                    </h2>
                                    <p class="mt-1 flex items-center gap-1 text-[11px] text-gray-400">
                                        <span class="material-icons-outlined text-sm text-primary">location_on</span>
                                        {{ $asset->city }}, {{ $asset->province }}
                                    </p>

                                    <p class="mt-3 font-semibold text-primary">{{ $asset->price !== null ? 'Rp '.number_format((float) $asset->price, 0, ',', '.') : 'Hubungi Grapadi' }}</p>
                                    <dl class="mt-3 grid grid-cols-3 gap-2 border-y border-[#1b3b2b] py-3">
                                        <div><dt class="text-[9px] uppercase tracking-wide text-gray-600">Luas</dt><dd class="mt-1 text-[11px] font-semibold text-gray-200">{{ number_format((float) $asset->area_sqm, 0, ',', '.') }} m²</dd></div>
                                        <div><dt class="text-[9px] uppercase tracking-wide text-gray-600">Sertifikat</dt><dd class="mt-1 truncate text-[11px] font-semibold text-gray-200">{{ $asset->certificate_type }}</dd></div>
                                        <div><dt class="text-[9px] uppercase tracking-wide text-gray-600">Kondisi</dt><dd class="mt-1 truncate text-[11px] font-semibold text-gray-200">{{ $asset->condition->label() }}</dd></div>
                                    </dl>

                                    <div class="mt-3 flex items-center justify-between gap-3">
                                        <span class="truncate text-[10px] text-primary">{{ $asset->objective->label() }}</span>
                                        <a href="{{ route('matching.show', $asset) }}" class="inline-flex shrink-0 items-center gap-1 text-[10px] font-semibold text-[#e9d38a] hover:text-white">
                                            Lihat Detail <span aria-hidden="true">→</span>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-md border border-[#234634] bg-[#082219] px-6 py-16 text-center sm:col-span-2 lg:col-span-3">
                                <span class="material-icons-outlined text-5xl text-gray-600">domain_disabled</span>
                                <h2 class="mt-4 font-display text-2xl text-white">Aset tidak ditemukan</h2>
                                <p class="mt-2 text-sm text-gray-500">Coba ubah kata kunci atau filter pencarian Anda.</p>
                                <a href="{{ route('matching.index') }}" class="mt-5 inline-flex rounded-md border border-primary/50 px-4 py-2 text-xs font-semibold text-primary">Reset Filter</a>
                            </div>
                        @endforelse
                    </div>

                    @if($assets->hasPages())
                        <div class="mt-6">{{ $assets->links() }}</div>
                    @endif
                </div>

                {{-- Trust sidebar --}}
                <aside class="rounded-md border border-[#234634] bg-[#071f17] p-5 xl:sticky xl:top-24">
                    <p class="text-[10px] font-semibold uppercase tracking-[.22em] text-primary">Mengapa Capital Connect?</p>
                    <div class="mt-4 divide-y divide-[#1c3a2b]">
                        <div class="py-4 first:pt-0">
                            <div class="grid grid-cols-[24px_minmax(0,1fr)] items-start gap-3"><span class="material-icons-outlined mt-0.5 w-6 text-lg text-primary">verified_user</span><div class="min-w-0 text-left"><h3 class="font-display text-lg font-semibold text-white">Data melalui screening</h3><p class="mt-1 text-[11px] leading-5 text-gray-500">Kelengkapan data, legalitas dasar, dan kualitas foto diperiksa sebelum dipublikasikan.</p></div></div>
                        </div>
                        <div class="py-4">
                            <div class="grid grid-cols-[24px_minmax(0,1fr)] items-start gap-3"><span class="material-icons-outlined mt-0.5 w-6 text-lg text-primary">handshake</span><div class="min-w-0 text-left"><h3 class="font-display text-lg font-semibold text-white">Terhubung secara terarah</h3><p class="mt-1 text-[11px] leading-5 text-gray-500">Minat Anda diteruskan ke tim Grapadi untuk proses tindak lanjut yang relevan.</p></div></div>
                        </div>
                        <div class="py-4">
                            <div class="grid grid-cols-[24px_minmax(0,1fr)] items-start gap-3"><span class="material-icons-outlined mt-0.5 w-6 text-lg text-primary">lock</span><div class="min-w-0 text-left"><h3 class="font-display text-lg font-semibold text-white">Data sensitif aman</h3><p class="mt-1 text-[11px] leading-5 text-gray-500">Nomor sertifikat dan identitas pemilik tidak ditampilkan publik.</p></div></div>
                        </div>
                        <div class="py-4">
                            <div class="grid grid-cols-[24px_minmax(0,1fr)] items-start gap-3"><span class="material-icons-outlined mt-0.5 w-6 text-lg text-primary">monitoring</span><div class="min-w-0 text-left"><h3 class="font-display text-lg font-semibold text-white">Pendampingan Grapadi</h3><p class="mt-1 text-[11px] leading-5 text-gray-500">Tim kami membantu menjembatani komunikasi awal antara para pihak.</p></div></div>
                        </div>
                    </div>

                    <div class="mt-2 rounded-md border border-primary/20 bg-[#0b2a1f] p-4">
                        <h3 class="font-display text-xl font-semibold text-primary">Tertarik dengan peluang ini?</h3>
                        <p class="mt-2 text-[11px] leading-5 text-gray-400">Jelajahi aset yang tersedia atau daftarkan aset Anda untuk menemukan peluang baru.</p>
                        @auth
                            <a href="{{ route('matching.dashboard') }}" class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-md bg-primary px-4 py-3 text-xs font-bold text-[#092016] hover:bg-primary-300">
                                <span class="material-icons-outlined text-base">dashboard</span> Buka Dashboard
                            </a>
                        @else
                            <a href="{{ route('matching.register') }}" class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-md bg-primary px-4 py-3 text-xs font-bold text-[#092016] hover:bg-primary-300">
                                <span class="material-icons-outlined text-base">arrow_forward</span> Mulai Sekarang
                            </a>
                        @endauth
                    </div>

                    <div class="mt-4 border-t border-[#1c3a2b] pt-4">
                        <p class="mb-2 text-[10px] font-bold uppercase tracking-[.2em] text-primary">Disclaimer</p>
                        <p class="text-[10px] leading-[1.7] text-gray-500">
                            Capital Connect merupakan layanan business matching dan transaction arrangement yang disediakan oleh Grapadi untuk membantu mempertemukan pihak-pihak yang memiliki potensi kerja sama. Grapadi tidak bertindak sebagai pembeli, penjual, broker, agen, maupun pihak dalam transaksi yang terjadi. Seluruh proses negosiasi, verifikasi, uji tuntas (due diligence), pelaksanaan transaksi, serta segala hak dan kewajiban hukum sepenuhnya menjadi tanggung jawab para pihak yang terlibat. Grapadi tidak memberikan jaminan atas keakuratan informasi yang disampaikan oleh masing-masing pihak maupun keberhasilan suatu transaksi, dan tidak bertanggung jawab atas kerugian yang timbul akibat tindakan, wanprestasi, penipuan, atau pelanggaran hukum yang dilakukan oleh salah satu pihak.
                        </p>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</div>
@endsection
