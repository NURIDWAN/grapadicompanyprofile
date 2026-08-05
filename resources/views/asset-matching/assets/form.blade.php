@extends('layouts.app')
@php($editing = isset($asset))
@section('title', ($editing ? 'Edit' : 'Daftarkan').' Aset | Grapadi')

@section('content')
<section class="px-4 pb-20 pt-28 sm:px-6">
    <div class="mx-auto max-w-5xl">
        <a href="{{ route('matching.dashboard') }}" class="text-sm text-primary">← Kembali ke dashboard</a>
        <div class="mt-4 flex flex-wrap items-end justify-between gap-4">
            <div><p class="text-xs font-semibold uppercase tracking-[.2em] text-primary">Asset Matching</p><h1 class="mt-2 font-display text-4xl font-semibold text-white">{{ $editing ? 'Edit Aset' : 'Daftarkan Aset' }}</h1></div>
            @if($editing)<span class="rounded-full border border-primary/30 px-4 py-2 text-xs text-primary">{{ $asset->status->label() }}</span>@endif
        </div>
        <p class="mt-3 max-w-3xl text-sm leading-6 text-gray-400">Lengkapi informasi dasar aset untuk memulai proses registrasi. Data yang Anda masukkan akan menjadi profil awal aset dalam sistem.</p>
        <div class="mt-6">@include('asset-matching.partials.alerts')</div>
        @if($editing && $asset->status === \App\Enums\AssetStatus::Published)<div class="mb-6 rounded-xl border border-amber-500/30 bg-amber-500/10 p-4 text-sm text-amber-200">Perubahan akan mengirim aset kembali ke proses review. Slug tetap dikunci agar URL publik tidak berubah.</div>@endif

        <form id="asset-form" method="POST" enctype="multipart/form-data" action="{{ $editing ? route('matching.assets.update', $asset) : route('matching.assets.store') }}" class="space-y-6">
            @csrf @if($editing)@method('PUT')@endif

            <section class="rounded-2xl border border-border-dark bg-surface-dark p-5 sm:p-7">
                <div class="flex items-center gap-3"><span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-sm font-bold text-background-dark">1</span><h2 class="font-display text-2xl font-semibold text-white">Informasi Aset</h2></div>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <label class="block text-sm text-gray-300 sm:col-span-2">Nama Aset<input id="asset-name" name="name" value="{{ old('name', $asset->name ?? '') }}" required maxlength="150" class="form-input"></label>
                    <label class="block text-sm text-gray-300 sm:col-span-2">Slug
                        <div class="mt-2 flex overflow-hidden rounded-xl border border-border-dark bg-background-dark focus-within:border-primary"><span class="hidden items-center border-r border-border-dark px-4 text-xs text-gray-500 md:flex">/asset-matching/aset/</span><input id="asset-slug" name="slug" value="{{ old('slug', $asset->slug ?? '') }}" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" {{ $editing && $asset->slug_locked_at ? 'readonly' : '' }} class="min-w-0 flex-1 bg-transparent px-4 py-3 text-white outline-none disabled:opacity-60"></div>
                        <span class="mt-1 block text-xs text-gray-500">{{ $editing && $asset->slug_locked_at ? 'Slug terkunci setelah publikasi.' : 'Dibuat otomatis dari nama dan dapat disunting sebelum publikasi.' }}</span>
                    </label>
                    <label class="block text-sm text-gray-300">Jenis Aset<select name="asset_category_id" required class="form-input"><option value="">Pilih jenis</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('asset_category_id', $asset->asset_category_id ?? '') == $category->id)>{{ $category->name }}</option>@endforeach</select></label>
                    <label class="block text-sm text-gray-300">Status Listing<select name="listing_status" required class="form-input">@foreach($listingStatuses as $value => $label)<option value="{{ $value }}" @selected(old('listing_status', isset($asset) ? $asset->listing_status->value : 'available') === $value)>{{ $label }}</option>@endforeach</select></label>
                    <label class="block text-sm text-gray-300">Luas (m²)<input id="asset-area" name="area_sqm" type="number" min="0.01" step="0.01" value="{{ old('area_sqm', $asset->area_sqm ?? '') }}" required class="form-input"></label>
                    <label class="block text-sm text-gray-300">Harga (Rp) <span class="text-gray-500">— opsional</span><input id="asset-price" name="price" type="number" min="0" step="1" value="{{ old('price', $asset->price ?? '') }}" class="form-input"></label>
                    <label class="block text-sm text-gray-300 sm:col-span-2">Harga/m² <span class="text-gray-500">— dihitung otomatis, dapat diedit</span><input id="price-per-sqm" name="price_per_sqm" type="number" min="0" step="1" value="{{ old('price_per_sqm', $asset->price_per_sqm ?? '') }}" placeholder="Otomatis dari harga ÷ luas" class="form-input text-primary"><span class="mt-1 block text-xs text-gray-500">Ubah nilai ini jika harga per meter persegi yang dipakai berbeda dari hasil kalkulasi.</span></label>
                </div>
            </section>

            <section class="rounded-2xl border border-border-dark bg-surface-dark p-5 sm:p-7">
                <div class="flex items-center gap-3"><span class="section-number">2</span><h2 class="section-title">Lokasi</h2></div>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <label class="field-label">Provinsi<input name="province" value="{{ old('province', $asset->province ?? '') }}" required class="form-input"></label>
                    <label class="field-label">Kabupaten/Kota<input name="city" value="{{ old('city', $asset->city ?? '') }}" required class="form-input"></label>
                    <label class="field-label">Kecamatan<input name="district" value="{{ old('district', $asset->district ?? '') }}" required class="form-input"></label>
                    <label class="field-label">Kelurahan/Desa<input name="village" value="{{ old('village', $asset->village ?? '') }}" required class="form-input"></label>
                    <label class="field-label sm:col-span-2">Alamat Lengkap<textarea name="full_address" rows="3" required class="form-input">{{ old('full_address', $asset->full_address ?? '') }}</textarea></label>
                    <label class="field-label sm:col-span-2">Google Maps <span class="text-gray-500">— opsional</span><input name="google_maps_url" type="url" value="{{ old('google_maps_url', $asset->google_maps_url ?? '') }}" placeholder="https://maps.google.com/..." class="form-input"><span class="mt-1 block text-xs text-gray-500">Alamat dan tautan Maps akan ditampilkan pada halaman publik.</span></label>
                </div>
            </section>

            <section class="rounded-2xl border border-border-dark bg-surface-dark p-5 sm:p-7">
                <div class="flex items-center gap-3"><span class="section-number">3</span><h2 class="section-title">Legalitas</h2></div>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <label class="field-label">Sertifikat<input name="certificate_type" value="{{ old('certificate_type', $asset->certificate_type ?? '') }}" placeholder="Contoh: SHM, HGB" required class="form-input"></label>
                    <label class="field-label">Nomor Sertifikat <span class="text-gray-500">— privat</span><input name="certificate_number" value="{{ old('certificate_number', $asset->certificate_number ?? '') }}" required class="form-input"></label>
                    <div id="certificate-uploader" class="sm:col-span-2">
                        <label class="field-label" for="certificate-file">Dokumen (PDF/JPG/PNG, maksimal 10 MB) @if($editing)<span class="text-gray-500">— kosongkan jika tidak diganti</span>@endif</label>
                        <input id="certificate-file" name="certificate_file" type="file" accept="application/pdf,image/jpeg,image/png" {{ $editing ? '' : 'required' }} class="file-input">
                        <div id="certificate-new-preview" class="mt-4 hidden overflow-hidden rounded-xl border border-border-dark bg-background-dark"><div class="flex items-center justify-between border-b border-border-dark p-3"><span id="certificate-preview-name" class="truncate text-xs text-gray-300"></span><button id="certificate-preview-remove" type="button" class="text-xs text-red-300">Hapus</button></div><img id="certificate-image-preview" alt="Preview sertifikat" class="hidden max-h-96 w-full object-contain p-4"><iframe id="certificate-pdf-preview" title="Preview PDF sertifikat" class="hidden h-96 w-full bg-white"></iframe></div>
                        @if($editing)<div class="mt-4 overflow-hidden rounded-xl border border-border-dark"><div class="flex items-center justify-between bg-background-dark p-3"><span class="text-xs text-gray-400">Dokumen tersimpan</span><div class="flex gap-2"><a target="_blank" href="{{ route('matching.assets.certificate.preview', $asset) }}" class="text-xs text-primary">Buka</a><a href="{{ route('matching.assets.certificate', $asset) }}" class="text-xs text-primary">Unduh</a></div></div><iframe src="{{ route('matching.assets.certificate.preview', $asset) }}" title="Dokumen sertifikat tersimpan" loading="lazy" class="h-72 w-full bg-white"></iframe></div>@endif
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-border-dark bg-surface-dark p-5 sm:p-7">
                <div class="flex items-center gap-3"><span class="section-number">4</span><h2 class="section-title">Detail Aset</h2></div>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <label class="field-label sm:col-span-2">Deskripsi<textarea id="asset-description" name="description" rows="6" maxlength="10000" required class="form-input">{{ old('description', $asset->description ?? '') }}</textarea></label>
                    <label class="field-label">Kondisi<select name="condition" required class="form-input"><option value="">Pilih kondisi</option>@foreach($conditions as $value => $label)<option value="{{ $value }}" @selected(old('condition', isset($asset) ? $asset->condition->value : '') === $value)>{{ $label }}</option>@endforeach</select></label>
                    <label class="field-label">Pemanfaatan<select name="utilization_status" required class="form-input"><option value="">Pilih pemanfaatan</option>@foreach($utilizations as $value => $label)<option value="{{ $value }}" @selected(old('utilization_status', isset($asset) ? $asset->utilization_status->value : '') === $value)>{{ $label }}</option>@endforeach</select></label>
                    <label class="field-label sm:col-span-2">Tujuan<select name="objective" required class="form-input"><option value="">Pilih tujuan</option>@foreach($objectives as $value => $label)<option value="{{ $value }}" @selected(old('objective', isset($asset) ? $asset->objective->value : '') === $value)>{{ $label }}</option>@endforeach</select></label>
                </div>
            </section>

            <section class="rounded-2xl border border-border-dark bg-surface-dark p-5 sm:p-7">
                <div class="flex items-center gap-3"><span class="section-number">5</span><h2 class="section-title">Fasilitas</h2></div>
                <p class="mt-3 text-sm text-gray-400">Centang fasilitas yang tersedia pada aset.</p>
                @php($selectedFacilities = old('facilities', $editing ? $asset->facilities->pluck('id')->all() : []))
                <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">@forelse($facilities as $facility)<label class="flex cursor-pointer items-center gap-3 rounded-xl border border-border-dark bg-background-dark p-4 text-sm text-gray-300 transition has-[:checked]:border-primary has-[:checked]:text-primary"><input type="checkbox" name="facilities[]" value="{{ $facility->id }}" @checked(in_array($facility->id, $selectedFacilities)) class="rounded border-border-dark bg-surface-dark text-primary focus:ring-primary"><span class="material-icons-outlined text-lg">{{ $facility->icon ?: 'check_circle' }}</span>{{ $facility->name }}</label>@empty<p class="text-sm text-gray-500">Belum ada master fasilitas aktif.</p>@endforelse</div>
            </section>

            <section id="asset-photo-uploader" data-existing-count="{{ $editing ? $asset->photos->count() : 0 }}" class="rounded-2xl border border-border-dark bg-surface-dark p-5 sm:p-7">
                <div class="flex items-center justify-between gap-3"><div class="flex items-center gap-3"><span class="section-number">6</span><div><h2 class="section-title">Foto</h2><p class="mt-1 text-xs text-gray-500">Maksimal 10 foto, masing-masing 5 MB.</p></div></div><span id="asset-photo-count" class="rounded-full bg-primary/10 px-3 py-1 text-xs text-primary">0/10</span></div>
                @if($editing && $asset->photos->isNotEmpty())<div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">@foreach($asset->photos as $photo)<div data-existing-photo class="overflow-hidden rounded-xl border border-border-dark bg-background-dark"><img src="{{ asset('storage/'.$photo->path) }}" alt="{{ $photo->alt_text ?: $asset->name }}" class="h-40 w-full object-cover"><div class="space-y-3 p-3"><label class="text-xs text-gray-400">Alt Text<input name="existing_photo_alt[{{ $photo->id }}]" maxlength="180" value="{{ old('existing_photo_alt.'.$photo->id, $photo->alt_text ?: $asset->name) }}" class="form-input !mt-1 !py-2 text-xs"></label><label class="flex items-center gap-2 text-xs text-red-300"><input data-delete-existing type="checkbox" name="delete_photo_ids[]" value="{{ $photo->id }}"> Hapus foto</label></div></div>@endforeach</div>@endif
                <label id="asset-photo-dropzone" for="asset-photos" class="mt-6 flex min-h-36 cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-[#28513b] bg-background-dark/60 p-6 text-center hover:border-primary"><span class="material-icons-outlined text-4xl text-primary">add_photo_alternate</span><span class="mt-2 text-sm font-semibold text-white">Pilih atau tarik foto</span><input id="asset-photos" name="photos[]" type="file" accept="image/jpeg,image/png,image/webp" multiple {{ $editing ? '' : 'required' }} class="sr-only"></label>
                <p id="asset-photo-error" class="mt-3 hidden text-xs text-red-300"></p><div id="asset-new-photo-section" class="mt-5 hidden"><h3 class="mb-3 text-xs uppercase tracking-wider text-gray-500">Preview foto baru dan Alt Text</h3><div id="asset-photo-previews" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3"></div></div>
            </section>

            <section class="rounded-2xl border border-border-dark bg-surface-dark p-5 sm:p-7">
                <div class="flex items-center gap-3"><span class="section-number">7</span><h2 class="section-title">SEO Otomatis</h2></div>
                <div class="mt-6 space-y-4"><label class="field-label">SEO Title<input id="seo-title-preview" readonly class="form-input cursor-not-allowed text-gray-400" value="{{ $asset->seo_title ?? '' }}"></label><label class="field-label">Meta Description<textarea id="seo-description-preview" readonly rows="3" class="form-input cursor-not-allowed text-gray-400">{{ $asset->meta_description ?? '' }}</textarea></label><p class="text-xs text-gray-500">SEO diperbarui otomatis dari nama, jenis, luas, dan lokasi aset.</p></div>
            </section>

            <div class="flex justify-end gap-3"><a href="{{ route('matching.dashboard') }}" class="rounded-xl border border-border-dark px-6 py-3 text-gray-300">Batal</a><button class="rounded-xl bg-primary px-6 py-3 font-semibold text-background-dark">{{ $editing ? 'Simpan Perubahan' : 'Simpan Draft' }}</button></div>
        </form>
    </div>
</section>
@endsection

@push('styles')<style>.form-input{margin-top:.5rem;width:100%;border-radius:.75rem;border:1px solid #173b2a;background:#03150e;padding:.75rem 1rem;color:white;outline:none}.form-input:focus{border-color:#d5b24b}.field-label{display:block;font-size:.875rem;color:#d1d5db}.section-number{display:flex;height:2rem;width:2rem;align-items:center;justify-content:center;border-radius:9999px;background:#d5b24b;font-size:.875rem;font-weight:700;color:#03150e}.section-title{font-family:Playfair Display,serif;font-size:1.5rem;font-weight:600;color:white}.file-input{margin-top:.5rem;display:block;width:100%;border-radius:.75rem;border:1px solid #173b2a;background:#03150e;padding:.75rem 1rem;color:#d1d5db}.file-input::file-selector-button{margin-right:1rem;border:0;border-radius:.5rem;background:#d5b24b;padding:.5rem 1rem;font-size:.75rem;font-weight:600;color:#03150e}</style>@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const name = document.getElementById('asset-name'), slug = document.getElementById('asset-slug'), area = document.getElementById('asset-area'), price = document.getElementById('asset-price');
    let slugTouched = slug.readOnly || slug.value.length > 0;
    const slugify = value => value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
    slug.addEventListener('input', () => { slugTouched = true; slug.value = slugify(slug.value); updateSeo(); });
    name.addEventListener('input', () => { if (!slugTouched) slug.value = slugify(name.value); updateSeo(); });
    const pricePerSqm = document.getElementById('price-per-sqm');
    const updatePrice = (preserveExisting = false) => { const result = Number(price.value) && Number(area.value) ? Math.round(Number(price.value) / Number(area.value)) : ''; if (!preserveExisting || !pricePerSqm.value) pricePerSqm.value = result; updateSeo(); };
    const updateSeo = () => { const city = document.querySelector('[name=city]').value, province = document.querySelector('[name=province]').value, village = document.querySelector('[name=village]').value; const type = document.querySelector('[name=asset_category_id] option:checked')?.textContent.trim(); document.getElementById('seo-title-preview').value = [name.value, type && type !== 'Pilih jenis' ? `${type} di ${city || province}` : `Aset di ${city || province}`, 'Grapadi'].filter(Boolean).join(' | ').slice(0,180); document.getElementById('seo-description-preview').value = `Temukan ${[name.value,type && type !== 'Pilih jenis' ? type : '',area.value ? `${area.value} m²` : '',village,city,province].filter(Boolean).join(', ')}. Hubungi Grapadi untuk informasi lebih lanjut.`.slice(0,160); };
    [area, price].forEach(el => el.addEventListener('input', () => updatePrice(false))); document.querySelectorAll('[name=city],[name=province],[name=village],[name=asset_category_id]').forEach(el => el.addEventListener('input', updateSeo)); updatePrice(true); updateSeo();

    const certInput = document.getElementById('certificate-file'), certBox = document.getElementById('certificate-new-preview'), certImage = document.getElementById('certificate-image-preview'), certPdf = document.getElementById('certificate-pdf-preview'); let certUrl;
    const clearCert = () => { if(certUrl) URL.revokeObjectURL(certUrl); certInput.value=''; certBox.classList.add('hidden'); certImage.classList.add('hidden'); certPdf.classList.add('hidden'); };
    certInput.addEventListener('change', () => { const file=certInput.files[0]; if(!file) return clearCert(); if(certUrl) URL.revokeObjectURL(certUrl); certUrl=URL.createObjectURL(file); document.getElementById('certificate-preview-name').textContent=file.name; certBox.classList.remove('hidden'); certImage.classList.toggle('hidden',!file.type.startsWith('image/')); certPdf.classList.toggle('hidden',file.type!=='application/pdf'); if(file.type.startsWith('image/')) certImage.src=certUrl; if(file.type==='application/pdf') certPdf.src=certUrl; }); document.getElementById('certificate-preview-remove').addEventListener('click', clearCert);

    const uploader=document.getElementById('asset-photo-uploader'), input=document.getElementById('asset-photos'), grid=document.getElementById('asset-photo-previews'), section=document.getElementById('asset-new-photo-section'), counter=document.getElementById('asset-photo-count'), error=document.getElementById('asset-photo-error'), checks=[...uploader.querySelectorAll('[data-delete-existing]')]; let files=[]; const existing=Number(uploader.dataset.existingCount||0);
    const activeExisting=()=>existing-checks.filter(c=>c.checked).length; const sync=()=>{const dt=new DataTransfer();files.forEach(item=>dt.items.add(item.file));input.files=dt.files};
    const render=()=>{grid.innerHTML='';section.classList.toggle('hidden',!files.length);files.forEach((item,index)=>{const card=document.createElement('div');card.className='overflow-hidden rounded-xl border border-border-dark bg-background-dark';const url=URL.createObjectURL(item.file);card.innerHTML=`<div class="relative"><img src="${url}" alt="Preview" class="h-40 w-full object-cover"><button type="button" class="absolute right-2 top-2 h-8 w-8 rounded-full bg-black/75 text-white">×</button></div><div class="p-3"><p class="truncate text-xs text-gray-400"></p><label class="mt-3 block text-xs text-gray-400">Alt Text<input name="photo_alt_texts[]" maxlength="180" class="form-input !mt-1 !py-2 text-xs"></label></div>`;card.querySelector('p').textContent=item.file.name;card.querySelector('input').value=item.alt;card.querySelector('input').addEventListener('input',e=>item.alt=e.target.value);card.querySelector('button').addEventListener('click',()=>{files.splice(index,1);sync();render()});grid.append(card)});counter.textContent=`${activeExisting()+files.length}/10`;};
    const addFiles=list=>{error.classList.add('hidden');for(const file of list){if(!['image/jpeg','image/png','image/webp'].includes(file.type)||file.size>5*1024*1024)continue;if(!files.some(x=>x.file.name===file.name&&x.file.size===file.size))files.push({file,alt:`${name.value || 'Aset'} - Foto ${activeExisting()+files.length+1}`});}const slots=Math.max(0,10-activeExisting());if(files.length>slots){files=files.slice(0,slots);error.textContent='Maksimal 10 foto.';error.classList.remove('hidden')}sync();render()};
    input.addEventListener('change',e=>addFiles([...e.target.files]));checks.forEach(c=>c.addEventListener('change',render));const drop=document.getElementById('asset-photo-dropzone');['dragover','drop'].forEach(eventName=>drop.addEventListener(eventName,e=>{e.preventDefault();if(eventName==='drop')addFiles([...e.dataTransfer.files])}));render();
});
</script>
@endpush
