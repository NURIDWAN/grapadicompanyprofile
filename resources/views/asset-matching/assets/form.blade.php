@extends('layouts.app')
@php($editing = isset($asset))
@section('title', ($editing ? 'Edit' : 'Daftarkan').' Aset | Grapadi')
@section('content')
<section class="pt-28 pb-20 px-6"><div class="mx-auto max-w-4xl">
    <a href="{{ route('matching.dashboard') }}" class="text-sm text-primary">← Kembali ke dashboard</a><h1 class="mt-4 font-display text-4xl font-semibold text-white">{{ $editing ? 'Edit Aset' : 'Daftarkan Aset' }}</h1><p class="mt-2 text-gray-400">Lengkapi data dasar. Valuasi, appraisal, dan studi kelayakan tidak dilakukan pada tahap ini.</p>
    <div class="mt-7">@include('asset-matching.partials.alerts')</div>
    @if($editing && $asset->status === \App\Enums\AssetStatus::Published)<div class="mb-6 rounded-xl border border-amber-500/30 bg-amber-500/10 p-4 text-amber-200">Perubahan akan menyembunyikan aset dari katalog dan mengirimkannya kembali untuk review.</div>@endif
    <form method="POST" enctype="multipart/form-data" action="{{ $editing ? route('matching.assets.update', $asset) : route('matching.assets.store') }}" class="space-y-7">@csrf @if($editing)@method('PUT')@endif
        <div class="rounded-2xl border border-border-dark bg-surface-dark p-6"><h2 class="font-display text-2xl font-semibold text-white">Informasi Aset</h2><div class="mt-5 grid gap-5 sm:grid-cols-2">
            <label class="block text-sm text-gray-300 sm:col-span-2">Nama aset<input name="name" value="{{ old('name', $asset->name ?? '') }}" required class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white"></label>
            <label class="block text-sm text-gray-300">Kategori<select name="asset_category_id" required class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white"><option value="">Pilih kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('asset_category_id', $asset->asset_category_id ?? '')==$category->id)>{{ $category->name }}</option>@endforeach</select></label>
            <label class="block text-sm text-gray-300">Luas (m²)<input name="area_sqm" type="number" min="0.01" step="0.01" value="{{ old('area_sqm', $asset->area_sqm ?? '') }}" required class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white"></label>
            <label class="block text-sm text-gray-300">Provinsi<input name="province" value="{{ old('province', $asset->province ?? '') }}" required class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white"></label>
            <label class="block text-sm text-gray-300">Kota/Kabupaten<input name="city" value="{{ old('city', $asset->city ?? '') }}" required class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white"></label>
            <label class="block text-sm text-gray-300 sm:col-span-2">Alamat lengkap <span class="text-gray-500">(tidak ditampilkan publik)</span><textarea name="full_address" rows="3" required class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white">{{ old('full_address', $asset->full_address ?? '') }}</textarea></label>
        </div></div>
        <div class="rounded-2xl border border-border-dark bg-surface-dark p-6">
            <h2 class="font-display text-2xl font-semibold text-white">Legalitas Dasar</h2>
            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <label class="block text-sm text-gray-300">Jenis sertifikat<input name="certificate_type" value="{{ old('certificate_type', $asset->certificate_type ?? '') }}" placeholder="Contoh: SHM, HGB" required class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white"></label>
                <label class="block text-sm text-gray-300">Nomor sertifikat <span class="text-gray-500">(privat)</span><input name="certificate_number" value="{{ old('certificate_number', $asset->certificate_number ?? '') }}" required class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white"></label>

                <div id="certificate-uploader" class="sm:col-span-2">
                    <label for="certificate-file" class="block text-sm text-gray-300">
                        Scan sertifikat (PDF/JPG/PNG, maks. 10 MB)
                        @if($editing)<span class="text-gray-500"> — kosongkan jika tidak diganti</span>@endif
                    </label>
                    <input
                        id="certificate-file"
                        name="certificate_file"
                        type="file"
                        accept="application/pdf,image/jpeg,image/png"
                        {{ $editing ? '' : 'required' }}
                        class="mt-2 block w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-gray-300 file:mr-4 file:rounded-lg file:border-0 file:bg-primary file:px-4 file:py-2 file:text-xs file:font-semibold file:text-background-dark"
                    >
                    <p class="mt-2 flex items-center gap-2 text-xs text-gray-500">
                        <span class="material-icons-outlined text-sm">lock</span>
                        Dokumen bersifat privat dan hanya dapat dilihat oleh pemilik serta admin Grapadi.
                    </p>
                    <p id="certificate-preview-error" class="mt-3 hidden rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-xs text-red-300"></p>

                    <div id="certificate-new-preview" class="mt-4 hidden overflow-hidden rounded-xl border border-border-dark bg-background-dark">
                        <div class="flex items-center justify-between gap-3 border-b border-border-dark px-4 py-3">
                            <div class="min-w-0">
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-primary">Preview dokumen baru</p>
                                <p id="certificate-preview-name" class="mt-1 truncate text-xs text-gray-300"></p>
                            </div>
                            <button id="certificate-preview-remove" type="button" class="shrink-0 rounded-lg border border-red-500/30 px-3 py-2 text-xs text-red-300 hover:bg-red-500/10">Hapus</button>
                        </div>
                        <img id="certificate-image-preview" alt="Preview sertifikat" class="hidden max-h-96 w-full object-contain p-4">
                        <iframe id="certificate-pdf-preview" title="Preview PDF sertifikat" class="hidden h-96 w-full bg-white"></iframe>
                    </div>

                    @if($editing)
                        <div class="mt-4 overflow-hidden rounded-xl border border-border-dark bg-background-dark">
                            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-border-dark px-4 py-3">
                                <div>
                                    <p class="text-[10px] font-semibold uppercase tracking-wider text-primary">Dokumen tersimpan</p>
                                    <p class="mt-1 text-xs text-gray-400">Preview hanya dapat diakses oleh pengguna yang berwenang.</p>
                                </div>
                                <div class="flex gap-2">
                                    <a target="_blank" rel="noopener" class="rounded-lg border border-primary/40 px-3 py-2 text-xs text-primary hover:bg-primary/10" href="{{ route('matching.assets.certificate.preview', $asset) }}">Buka</a>
                                    <a class="rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-background-dark" href="{{ route('matching.assets.certificate', $asset) }}">Unduh</a>
                                </div>
                            </div>
                            <iframe src="{{ route('matching.assets.certificate.preview', $asset) }}" title="Dokumen sertifikat tersimpan" loading="lazy" class="h-80 w-full bg-white"></iframe>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-border-dark bg-surface-dark p-6"><h2 class="font-display text-2xl font-semibold text-white">Status dan Tujuan</h2><div class="mt-5 grid gap-5 sm:grid-cols-2">
            @foreach([['condition','Kondisi aset',$conditions,'condition_notes','Keterangan kondisi'],['ownership_status','Status kepemilikan',$ownerships,'ownership_notes','Keterangan kepemilikan'],['utilization_status','Status pemanfaatan',$utilizations,'utilization_notes','Keterangan pemanfaatan']] as [$field,$label,$options,$notes,$notesLabel])
            <label class="block text-sm text-gray-300">{{ $label }}<select name="{{ $field }}" required class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white"><option value="">Pilih</option>@foreach($options as $value=>$text)<option value="{{ $value }}" @selected(old($field, isset($asset) ? $asset->{$field}->value : '')===$value)>{{ $text }}</option>@endforeach</select></label>
            <label class="block text-sm text-gray-300">{{ $notesLabel }} <span class="text-gray-500">(opsional)</span><textarea name="{{ $notes }}" rows="2" class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white">{{ old($notes, $asset->{$notes} ?? '') }}</textarea></label>@endforeach
            <label class="block text-sm text-gray-300 sm:col-span-2">Tujuan aset<select name="objective" required class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white"><option value="">Pilih satu tujuan</option>@foreach($objectives as $value=>$text)<option value="{{ $value }}" @selected(old('objective', isset($asset) ? $asset->objective->value : '')===$value)>{{ $text }}</option>@endforeach</select></label>
        </div></div>
        <div
            id="asset-photo-uploader"
            data-existing-count="{{ $editing ? $asset->photos->count() : 0 }}"
            class="rounded-2xl border border-border-dark bg-surface-dark p-6"
        >
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="font-display text-2xl font-semibold text-white">Foto Aset</h2>
                    <p class="mt-1 text-sm text-gray-400">JPG, PNG, atau WebP; maksimal 5 MB per foto dan total 10 foto.</p>
                </div>
                <span id="asset-photo-count" class="rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">0/10 foto</span>
            </div>

            @if($editing && $asset->photos->isNotEmpty())
                <div class="mt-5">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Foto tersimpan</p>
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                        @foreach($asset->photos as $photo)
                            <label data-existing-photo class="group relative cursor-pointer overflow-hidden rounded-xl border border-border-dark bg-background-dark">
                                <img src="{{ asset('storage/'.$photo->path) }}" alt="Foto aset tersimpan" class="h-32 w-full object-cover transition group-hover:opacity-70">
                                <span class="flex items-center justify-center gap-2 border-t border-border-dark p-2 text-xs text-red-300">
                                    <input data-delete-existing type="checkbox" name="delete_photo_ids[]" value="{{ $photo->id }}" class="rounded border-border-dark bg-background-dark text-red-500 focus:ring-red-500">
                                    Tandai untuk dihapus
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <label
                id="asset-photo-dropzone"
                for="asset-photos"
                class="mt-5 flex min-h-32 cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-[#28513b] bg-background-dark/60 px-5 py-6 text-center transition hover:border-primary hover:bg-primary/5"
            >
                <span class="material-icons-outlined text-4xl text-primary">add_photo_alternate</span>
                <span class="mt-2 text-sm font-semibold text-white">Pilih atau tarik foto ke sini</span>
                <span class="mt-1 text-xs text-gray-500">Anda dapat memilih beberapa foto sekaligus</span>
                <input
                    id="asset-photos"
                    name="photos[]"
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    multiple
                    {{ $editing ? '' : 'required' }}
                    class="sr-only"
                >
            </label>

            <p id="asset-photo-error" class="mt-3 hidden rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-xs text-red-300"></p>

            <div id="asset-new-photo-section" class="mt-5 hidden">
                <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Preview foto baru</p>
                <div id="asset-photo-previews" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4"></div>
            </div>
        </div>
        <div class="flex justify-end gap-3"><a href="{{ route('matching.dashboard') }}" class="rounded-xl border border-border-dark px-6 py-3 text-gray-300">Batal</a><button class="rounded-xl bg-primary px-6 py-3 font-semibold text-background-dark">{{ $editing ? 'Simpan Perubahan' : 'Simpan Draft' }}</button></div>
    </form>
</div></section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const uploader = document.getElementById('asset-photo-uploader');
    if (!uploader) return;

    const input = document.getElementById('asset-photos');
    const dropzone = document.getElementById('asset-photo-dropzone');
    const previewSection = document.getElementById('asset-new-photo-section');
    const previewGrid = document.getElementById('asset-photo-previews');
    const counter = document.getElementById('asset-photo-count');
    const errorBox = document.getElementById('asset-photo-error');
    const deleteCheckboxes = [...uploader.querySelectorAll('[data-delete-existing]')];
    const existingCount = Number(uploader.dataset.existingCount || 0);
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    const maxFileSize = 5 * 1024 * 1024;
    let selectedFiles = [];

    const activeExistingCount = () => existingCount - deleteCheckboxes.filter(checkbox => checkbox.checked).length;

    const showError = message => {
        errorBox.textContent = message;
        errorBox.classList.remove('hidden');
    };

    const clearError = () => {
        errorBox.textContent = '';
        errorBox.classList.add('hidden');
    };

    const syncInput = () => {
        const transfer = new DataTransfer();
        selectedFiles.forEach(file => transfer.items.add(file));
        input.files = transfer.files;
    };

    const formatSize = bytes => bytes >= 1024 * 1024
        ? `${(bytes / (1024 * 1024)).toFixed(1)} MB`
        : `${Math.ceil(bytes / 1024)} KB`;

    const render = () => {
        previewGrid.innerHTML = '';
        previewSection.classList.toggle('hidden', selectedFiles.length === 0);

        selectedFiles.forEach((file, index) => {
            const card = document.createElement('div');
            card.className = 'relative overflow-hidden rounded-xl border border-border-dark bg-background-dark';

            const image = document.createElement('img');
            const objectUrl = URL.createObjectURL(file);
            image.src = objectUrl;
            image.alt = `Preview ${file.name}`;
            image.className = 'h-32 w-full object-cover';
            image.addEventListener('load', () => URL.revokeObjectURL(objectUrl), { once: true });

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-black/75 text-white transition hover:bg-red-600';
            remove.setAttribute('aria-label', `Hapus ${file.name}`);
            remove.innerHTML = '<span class="material-icons-outlined text-base">close</span>';
            remove.addEventListener('click', () => {
                selectedFiles.splice(index, 1);
                syncInput();
                clearError();
                render();
            });

            const meta = document.createElement('div');
            meta.className = 'p-3';
            const name = document.createElement('p');
            name.className = 'truncate text-xs font-medium text-gray-200';
            name.textContent = file.name;
            const size = document.createElement('p');
            size.className = 'mt-1 text-[10px] text-gray-500';
            size.textContent = formatSize(file.size);
            meta.append(name, size);

            card.append(image, remove, meta);
            previewGrid.append(card);
        });

        const total = activeExistingCount() + selectedFiles.length;
        counter.textContent = `${total}/10 foto`;
        counter.classList.toggle('text-red-300', total > 10);
    };

    const addFiles = files => {
        clearError();
        const errors = [];

        for (const file of files) {
            if (!allowedTypes.includes(file.type)) {
                errors.push(`${file.name}: format tidak didukung`);
                continue;
            }
            if (file.size > maxFileSize) {
                errors.push(`${file.name}: ukuran melebihi 5 MB`);
                continue;
            }
            const duplicate = selectedFiles.some(selected => selected.name === file.name && selected.size === file.size && selected.lastModified === file.lastModified);
            if (!duplicate) selectedFiles.push(file);
        }

        const availableSlots = Math.max(0, 10 - activeExistingCount());
        if (selectedFiles.length > availableSlots) {
            selectedFiles = selectedFiles.slice(0, availableSlots);
            errors.push(`Maksimal 10 foto. Hanya ${availableSlots} foto baru yang dapat ditambahkan.`);
        }

        syncInput();
        render();
        if (errors.length) showError(errors.join(' · '));
    };

    input.addEventListener('change', event => addFiles([...event.target.files]));

    deleteCheckboxes.forEach(checkbox => checkbox.addEventListener('change', () => {
        const availableSlots = Math.max(0, 10 - activeExistingCount());
        if (selectedFiles.length > availableSlots) {
            selectedFiles = selectedFiles.slice(0, availableSlots);
            syncInput();
            showError('Sebagian preview foto baru dihapus agar total tidak melebihi 10 foto.');
        }
        render();
    }));

    ['dragenter', 'dragover'].forEach(eventName => dropzone.addEventListener(eventName, event => {
        event.preventDefault();
        dropzone.classList.add('border-primary', 'bg-primary/10');
    }));

    ['dragleave', 'drop'].forEach(eventName => dropzone.addEventListener(eventName, event => {
        event.preventDefault();
        dropzone.classList.remove('border-primary', 'bg-primary/10');
    }));

    dropzone.addEventListener('drop', event => addFiles([...event.dataTransfer.files]));
    render();
});

document.addEventListener('DOMContentLoaded', () => {
    const uploader = document.getElementById('certificate-uploader');
    if (!uploader) return;

    const input = document.getElementById('certificate-file');
    const preview = document.getElementById('certificate-new-preview');
    const imagePreview = document.getElementById('certificate-image-preview');
    const pdfPreview = document.getElementById('certificate-pdf-preview');
    const previewName = document.getElementById('certificate-preview-name');
    const removeButton = document.getElementById('certificate-preview-remove');
    const errorBox = document.getElementById('certificate-preview-error');
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
    const maxFileSize = 10 * 1024 * 1024;
    let objectUrl = null;

    const formatSize = bytes => bytes >= 1024 * 1024
        ? `${(bytes / (1024 * 1024)).toFixed(1)} MB`
        : `${Math.ceil(bytes / 1024)} KB`;

    const clearObjectUrl = () => {
        if (objectUrl) URL.revokeObjectURL(objectUrl);
        objectUrl = null;
        imagePreview.removeAttribute('src');
        pdfPreview.removeAttribute('src');
    };

    const hidePreview = () => {
        clearObjectUrl();
        preview.classList.add('hidden');
        imagePreview.classList.add('hidden');
        pdfPreview.classList.add('hidden');
        previewName.textContent = '';
    };

    const showError = message => {
        errorBox.textContent = message;
        errorBox.classList.remove('hidden');
    };

    const clearError = () => {
        errorBox.textContent = '';
        errorBox.classList.add('hidden');
    };

    input.addEventListener('change', () => {
        clearError();
        hidePreview();
        const file = input.files[0];
        if (!file) return;

        if (!allowedTypes.includes(file.type)) {
            input.value = '';
            showError('Format sertifikat tidak didukung. Gunakan PDF, JPG, atau PNG.');
            return;
        }
        if (file.size > maxFileSize) {
            input.value = '';
            showError('Ukuran dokumen sertifikat melebihi batas 10 MB.');
            return;
        }

        objectUrl = URL.createObjectURL(file);
        previewName.textContent = `${file.name} · ${formatSize(file.size)}`;
        preview.classList.remove('hidden');

        if (file.type === 'application/pdf') {
            pdfPreview.src = objectUrl;
            pdfPreview.classList.remove('hidden');
        } else {
            imagePreview.src = objectUrl;
            imagePreview.classList.remove('hidden');
        }
    });

    removeButton.addEventListener('click', () => {
        input.value = '';
        clearError();
        hidePreview();
    });

    window.addEventListener('beforeunload', clearObjectUrl);
});
</script>
@endpush
