@extends('layouts.app')
@section('title', 'Lupa Password | Grapadi')
@section('content')
<section class="min-h-screen pt-32 pb-20 px-6"><div class="mx-auto max-w-md rounded-2xl border border-border-dark bg-surface-dark p-8">
    <h1 class="font-display text-4xl font-semibold text-white">Atur ulang password</h1><p class="mt-2 text-gray-400">Kami akan mengirim tautan reset ke email Anda.</p>
    <div class="mt-6">@include('asset-matching.partials.alerts')</div>
    <form method="POST" action="{{ route('matching.password.email') }}" class="space-y-5">@csrf
        <label class="block text-sm text-gray-300">Email<input name="email" type="email" value="{{ old('email') }}" required class="mt-2 w-full rounded-xl border border-border-dark bg-background-dark px-4 py-3 text-white"></label>
        <button class="w-full rounded-xl bg-primary px-5 py-3 font-semibold text-background-dark">Kirim tautan reset</button>
    </form><a href="{{ route('matching.login') }}" class="mt-6 block text-center text-sm text-primary">Kembali ke login</a>
</div></section>
@endsection
