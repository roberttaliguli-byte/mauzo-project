@extends('layouts.guest')

@section('title', 'Badilisha Neno la Siri')

@section('content')
<div class="max-w-md mx-auto mt-10">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-semibold text-yellow-300">BADILISHA NENO LA SIRI</h1>
        <p class="text-gray-300 text-sm mt-1">Jaza fomu hapo chini kubadilisha neno la siri lako</p>
    </div>

    @if(session('success'))
        <div class="mb-4 flex items-center gap-2 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <div class="space-y-4">
            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-sm text-gray-200 mb-1">Neno la Siri la sasa</label>
                <input id="current_password" name="current_password" type="password" required
                       class="w-full rounded-full py-3 px-4 bg-gray-100 text-black focus:ring-2 focus:ring-yellow-400 outline-none">
                @error('current_password') 
                    <div class="text-red-400 text-sm mt-1">{{ $message }}</div> 
                @enderror
            </div>

            <!-- New Password -->
            <div>
                <label for="password" class="block text-sm text-gray-200 mb-1">Neno la Siri jipya</label>
                <input id="password" name="password" type="password" required
                       class="w-full rounded-full py-3 px-4 bg-gray-100 text-black focus:ring-2 focus:ring-yellow-400 outline-none">
                @error('password') 
                    <div class="text-red-400 text-sm mt-1">{{ $message }}</div> 
                @enderror
            </div>

            <!-- Confirm New Password -->
            <div>
                <label for="password_confirmation" class="block text-sm text-gray-200 mb-1">Hakiki Neno la Siri jipya</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                       class="w-full rounded-full py-3 px-4 bg-gray-100 text-black focus:ring-2 focus:ring-yellow-400 outline-none">
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full py-3 rounded-full bg-yellow-500 text-black font-semibold hover:bg-yellow-400 transition">
                Badilisha
            </button>
        </div>
    </form>
</div>
@endsection
