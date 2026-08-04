@extends('layouts.app')
@section('title', 'Reset Password | Grapadi')
@section('content')
<section class="min-h-screen pt-32 pb-20 px-6"><div class="mx-auto max-w-md rounded-2xl border border-border-dark bg-surface-dark p-8">
    <h1 class="font-display text-4xl font-semibold text-white">Password baru</h1><div class="mt-6">@include('asset-matching.partials.alerts')</div>
    <form method="POST" action="{{ route('matching.password.update') }}" class="space-y-5">@csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <label class="block text-sm text-gray-300">Email<input name="email" type="email" value="{{ old('email', $email) }}" required class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white"></label>
        <label class="block text-sm text-gray-300">Password baru<input name="password" type="password" required class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white"></label>
        <label class="block text-sm text-gray-300">Konfirmasi password<input name="password_confirmation" type="password" required class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white"></label>
        <button class="w-full rounded-xl bg-primary px-5 py-3 font-semibold text-background-dark">Simpan password</button>
    </form>
</div></section>
@endsection
