@extends('layouts.app')
@section('title', 'Daftar Capital Connect | Grapadi')
@section('content')
<section class="min-h-screen pt-32 pb-20 px-6">
    <div class="mx-auto max-w-xl rounded-2xl border border-border-dark bg-surface-dark p-8 shadow-2xl">
        <p class="text-sm font-semibold uppercase tracking-widest text-primary">Capital Connect</p>
        <h1 class="mt-2 font-display text-4xl font-semibold text-white">Buat akun</h1>
        <p class="mt-2 text-gray-400">Satu akun untuk mendaftarkan aset dan menyatakan minat.</p>
        <div class="mt-6">@include('asset-matching.partials.alerts')</div>
        <form method="POST" action="{{ route('matching.register.store') }}" class="grid gap-5 sm:grid-cols-2">@csrf
            <label class="block text-sm text-gray-300 sm:col-span-2">Nama lengkap<input name="name" value="{{ old('name') }}" required class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white"></label>
            <label class="block text-sm text-gray-300 sm:col-span-2">Email<input name="email" type="email" value="{{ old('email') }}" required class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white"></label>
            <label class="block text-sm text-gray-300">Password<input name="password" type="password" required class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white"></label>
            <label class="block text-sm text-gray-300">Konfirmasi password<input name="password_confirmation" type="password" required class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white"></label>
            <button class="rounded-xl bg-primary px-5 py-3 font-semibold text-background-dark hover:bg-primary-400 sm:col-span-2">Daftar dan masuk</button>
        </form>
        <p class="mt-6 text-center text-sm text-gray-400">Sudah punya akun? <a class="text-primary hover:underline" href="{{ route('matching.login', request()->only('redirect')) }}">Masuk</a></p>
    </div>
</section>
@endsection
