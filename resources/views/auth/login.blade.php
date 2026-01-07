@extends('layouts.guest')

@section('title', 'Ingia')

@section('content')
<div class="max-w-sm mx-auto">
  <!-- Header Section -->
  <div class="text-center mb-6">
    <div class="flex justify-center mb-3">
      <div class="relative">
        <div class="absolute inset-0 bg-green-400/20 rounded-full blur-lg"></div>
        <div class="relative h-12 w-12 rounded-full bg-gradient-to-br from-amber-600 to-amber-800 flex items-center justify-center shadow-md">
          <i class="fas fa-lock text-white text-lg"></i>
        </div>
      </div>
    </div>
    <h1 class="text-2xl font-bold bg-gradient-to-r from-yellow-400 to-amber-500 bg-clip-text text-transparent mb-1">
      Karibu Tena
    </h1>
    <p class="text-gray-300 text-sm">Ingia kwenye mfumo</p>
  </div>

  <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
    @csrf

    {{-- Success Alert --}}
    @if(session('success'))
    <div id="success-alert" class="relative p-3 rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 text-white shadow-lg animate-fade-in">
      <div class="flex items-center gap-2">
        <div class="flex-shrink-0">
          <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center">
            <i class="fas fa-check text-white text-xs"></i>
          </div>
        </div>
        <div class="flex-1">
          <p class="text-xs font-medium">{{ session('success') }}</p>
        </div>
      </div>
      <div class="absolute bottom-0 left-0 h-1 bg-white/30 rounded-full animate-progress"></div>
    </div>

    <script>
      setTimeout(() => {
        const alert = document.getElementById('success-alert');
        if (alert) {
          alert.style.transition = "all 0.5s ease";
          alert.style.opacity = "0";
          alert.style.transform = "translateY(-10px)";
          setTimeout(() => alert.remove(), 500);
        }
      }, 3000);
    </script>
    @endif

    {{-- Error Alert --}}
    @if($errors->has('login'))
    <div class="relative p-3 rounded-xl bg-gradient-to-r from-red-500 to-rose-600 text-white shadow-lg animate-fade-in">
      <div class="flex items-center gap-2">
        <div class="flex-shrink-0">
          <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center">
            <i class="fas fa-exclamation-triangle text-white text-xs"></i>
          </div>
        </div>
        <div class="flex-1">
          <p class="text-xs font-medium">{{ $errors->first('login') }}</p>
        </div>
      </div>
    </div>
    @endif

    <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-gray-700 shadow-lg">
      <div class="space-y-4">
        <!-- Username Field -->
        <div class="group">
          <label for="username" class="block text-xs font-semibold text-gray-200 mb-2 flex items-center gap-2">
            <i class="fas fa-user-circle text-yellow-400 text-xs"></i>
            Jina la Mtumiaji
          </label>
          <div class="relative">
            <input id="username" name="username" value="{{ old('username') }}" required autofocus
                   placeholder="Weka jina la kuingia"
                   class="w-full rounded-lg py-3 px-10 bg-gray-700/50 border border-gray-600 text-white placeholder-gray-400 outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 group-hover:border-yellow-400 text-sm">
            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 group-focus-within:text-yellow-400 transition-colors text-sm">
              <i class="fas fa-user"></i>
            </div>
          </div>
          @error('username') 
          <div class="flex items-center gap-1 text-red-400 text-xs mt-1">
            <i class="fas fa-exclamation-circle text-xs"></i>
            {{ $message }}
          </div>
          @enderror
        </div>

        <!-- Password Field -->
        <div class="group">
          <div class="flex items-center justify-between mb-2">
            <label for="password" class="text-xs font-semibold text-gray-200 flex items-center gap-2">
              <i class="fas fa-lock text-yellow-400 text-xs"></i>
              Neno la Siri
            </label>
            <!-- Forget Password Link -->
            <a href="{{ route('password.request') }}" 
               class="text-xs text-amber-400 hover:text-amber-300 font-medium transition-colors duration-300 flex items-center gap-1 group">
              <i class="fas fa-key text-xs"></i>
              Umesahau?
            </a>
          </div>
          <div class="relative">
            <input id="password" name="password" type="password" required
                   placeholder="Weka neno la siri"
                   class="w-full rounded-lg py-3 px-10 bg-gray-700/50 border border-gray-600 text-white placeholder-gray-400 outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 group-hover:border-yellow-400 text-sm">
            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 group-focus-within:text-yellow-400 transition-colors text-sm">
              <i class="fas fa-key"></i>
            </div>
          </div>
          @error('password') 
          <div class="flex items-center gap-1 text-red-400 text-xs mt-1">
            <i class="fas fa-exclamation-circle text-xs"></i>
            {{ $message }}
          </div>
          @enderror
        </div>

        <!-- Remember Me & Register -->
        <div class="flex items-center justify-between pt-2">
          <label class="inline-flex items-center gap-2 cursor-pointer group">
            <div class="relative">
              <input type="checkbox" name="remember" class="sr-only peer">
              <div class="w-4 h-4 bg-gray-700 border-2 border-gray-600 rounded peer-checked:bg-yellow-500 peer-checked:border-yellow-500 transition-all duration-200 group-hover:border-yellow-400"></div>
              <div class="absolute inset-0 flex items-center justify-center text-white text-xs opacity-0 peer-checked:opacity-100 transition-opacity">
                <i class="fas fa-check"></i>
              </div>
            </div>
            <span class="text-gray-300 text-xs group-hover:text-white transition-colors">Kumbuka mimi</span>
          </label>

          <a href="{{ route('register') }}" class="text-yellow-400 hover:text-yellow-300 font-semibold text-xs transition-colors duration-300 flex items-center gap-1 group">
            Sajili
            <i class="fas fa-arrow-right group-hover:translate-x-0.5 transition-transform text-xs"></i>
          </a>
        </div>
      </div>

      <!-- Login Button -->
      <div class="mt-6">
        <button type="submit" 
                class="w-full py-3 rounded-lg bg-gradient-to-r from-yellow-500 to-amber-500 text-black font-bold hover:from-yellow-600 hover:to-amber-600 transition-all duration-300 transform hover:-translate-y-0.5 shadow-md hover:shadow-lg flex items-center justify-center gap-2 group text-sm">
          <i class="fas fa-sign-in-alt group-hover:scale-110 transition-transform text-xs"></i>
          Ingia Sasa
        </button>
      </div>
    </div>
  </form>

  <!-- Additional Links -->
  <div class="flex justify-center gap-6 text-xs mt-6">
    <a href="{{ route('landing') }}" class="text-white/90 hover:text-yellow-400 font-semibold transition-colors duration-300 flex items-center gap-1 group">
      <i class="fas fa-home group-hover:scale-110 transition-transform text-xs"></i> Rudi Home
    </a>
  </div>
</div>

<style>
  .animate-fade-in {
    animation: fadeIn 0.5s ease-in-out;
  }
  .animate-progress {
    animation: progress 3s linear forwards;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  @keyframes progress {
    from { width: 100%; }
    to { width: 0%; }
  }
</style>
@endsection