@extends('layouts.app')
@section('title', $asset->name.' | Grapadi Asset Matching')
@section('content')
<section class="pt-28 pb-20 px-6"><div class="mx-auto max-w-6xl">
    @include('asset-matching.partials.alerts')
    <a href="{{ route('matching.index') }}" class="text-sm text-primary">← Kembali ke katalog</a>
    <div class="mt-6 grid gap-8 lg:grid-cols-5"><div class="lg:col-span-3"><div class="grid gap-3 sm:grid-cols-2">@foreach($asset->photos as $photo)<img src="{{ asset('storage/'.$photo->path) }}" alt="Foto {{ $asset->name }}" class="h-64 w-full rounded-2xl object-cover {{ $loop->first ? 'sm:col-span-2 sm:h-96' : '' }}">@endforeach</div></div>
    <aside class="lg:col-span-2"><div class="sticky top-28 rounded-2xl border border-border-dark bg-surface-dark p-7"><span class="rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">{{ $asset->category->name }}</span><h1 class="mt-4 font-display text-4xl font-semibold text-white">{{ $asset->name }}</h1><p class="mt-2 text-gray-400">{{ $asset->city }}, {{ $asset->province }}</p>
        <dl class="mt-7 divide-y divide-border-dark text-sm">@foreach([['Luas', number_format((float)$asset->area_sqm, 0, ',', '.').' m²'],['Tujuan', $asset->objective->label()],['Kondisi', $asset->condition->label()],['Kepemilikan', $asset->ownership_status->label()],['Pemanfaatan', $asset->utilization_status->label()],['Jenis Sertifikat', $asset->certificate_type]] as [$term,$value])<div class="flex justify-between gap-4 py-3"><dt class="text-gray-400">{{ $term }}</dt><dd class="text-right font-medium text-white">{{ $value }}</dd></div>@endforeach</dl>
        <p class="mt-5 text-xs leading-relaxed text-gray-500">Alamat lengkap, nomor sertifikat, dokumen, dan identitas pemilik dijaga oleh Grapadi.</p>
        @auth @if(auth()->id()===$asset->owner_id)<p class="mt-6 rounded-xl bg-white/5 p-4 text-sm text-gray-300">Ini adalah aset milik Anda.</p>@else<form method="POST" action="{{ route('matching.interests.store', $asset) }}" class="mt-6">@csrf<button class="w-full rounded-xl bg-primary px-5 py-3 font-semibold text-background-dark">Tambah Minat</button></form>@endif @else<a href="{{ route('matching.login', ['redirect'=>url()->current()]) }}" class="mt-6 block w-full rounded-xl bg-primary px-5 py-3 text-center font-semibold text-background-dark">Masuk untuk Tambah Minat</a>@endauth
    </div></aside></div>
</div></section>
@endsection
