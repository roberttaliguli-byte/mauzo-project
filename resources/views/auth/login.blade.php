@extends('layouts.guest')

@section('title', 'Ingia')

@section('content')
  <div class="max-w-md mx-auto">
    <div class="text-center mb-6">
      <h1 class="text-2xl font-semibold text-yellow-300">Ingia</h1>
      <p class="text-gray-300 text-sm mt-1">Tumia jina la kuingia na neno la siri</p>
    </div>

    <form method="POST" action="{{ route('login.post') }}">
      @csrf

      {{-- Alerts / errors --}}
@if(session('success'))
    <div id="success-alert" class="mb-4 flex items-center gap-2 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        <span>{{ session('success') }}</span>
    </div>

    <script>
        // Hide the alert after 3 seconds (3000ms)
        setTimeout(() => {
            const alert = document.getElementById('success-alert');
            if (alert) {
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = 0;
                setTimeout(() => alert.remove(), 500); // remove from DOM after fade out
            }
        }, 3000);
    </script>
@endif



      @if($errors->has('login'))
        <div class="mb-4 text-red-400 text-sm">{{ $errors->first('login') }}</div>
      @endif

      <div class="space-y-4">
        <div>
          <label for="username" class="block text-sm text-gray-200 mb-1">Jina la Kuingia</label>
          <input id="username" name="username" value="{{ old('username') }}" required autofocus
                 class="w-full rounded-full py-3 px-4 bg-gray-100 text-black focus:ring-2 focus:ring-yellow-400 outline-none">
          @error('username') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
          <label for="password" class="block text-sm text-gray-200 mb-1">Neno la Siri</label>
          <input id="password" name="password" type="password" required
                 class="w-full rounded-full py-3 px-4 bg-gray-100 text-black focus:ring-2 focus:ring-yellow-400 outline-none">
          @error('password') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="flex items-center justify-between text-sm text-gray-300">
          <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 text-yellow-500 focus:ring-yellow-400">
            <span>Kumbuka mimi</span>
          </label>

          <a href="{{ route('register') }}" class="text-yellow-300 hover:underline">Sajili Kampuni</a>
        </div>
      </div>

      <div class="mt-6">
        <button type="submit" class="w-full py-3 rounded-full bg-yellow-500 text-black font-semibold hover:bg-yellow-400 transition">
          Ingia
        </button>
      </div>

      <div class="mt-4 text-center text-gray-400 text-sm">
        Huwezi kuingia? <a href="#" class="text-yellow-300 hover:underline">Rudisha Neno la Siri</a>
      </div>
    </form>
  </div>
@endsection
