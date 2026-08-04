@extends('layouts.app')
@section('title', 'Login Asset Matching | Grapadi')
@section('content')
<section class="min-h-screen pt-32 pb-20 px-6">
    <div class="mx-auto max-w-md rounded-2xl border border-border-dark bg-surface-dark p-8 shadow-2xl">
        <p class="text-sm font-semibold uppercase tracking-widest text-primary">Asset Matching</p>
        <h1 class="mt-2 font-display text-4xl font-semibold text-white">Masuk ke dashboard</h1>
        <p class="mt-2 text-gray-400">Kelola aset dan pantau minat Anda.</p>
        <div class="mt-6">@include('asset-matching.partials.alerts')</div>
        <form method="POST" action="{{ route('matching.login.store') }}" class="space-y-5">@csrf
            <label class="block text-sm text-gray-300">Email
                <input name="email" type="email" value="{{ old('email') }}" required autofocus class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white focus:border-primary focus:outline-none">
            </label>
            <label class="block text-sm text-gray-300">Password
                <input name="password" type="password" required class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white focus:border-primary focus:outline-none">
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-400"><input type="checkbox" name="remember" value="1" class="rounded"> Ingat saya</label>
            <button class="w-full rounded-xl bg-primary px-5 py-3 font-semibold text-background-dark hover:bg-primary-400">Masuk</button>
        </form>
        <div class="mt-6 flex justify-between text-sm"><a class="text-primary hover:underline" href="{{ route('matching.password.request') }}">Lupa password?</a><a class="text-primary hover:underline" href="{{ route('matching.register', request()->only('redirect')) }}">Daftar akun</a></div>
    </div>
</section>
@endsection
