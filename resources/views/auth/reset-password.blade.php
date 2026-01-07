@extends('layouts.guest')

@section('title', 'Badilisha Neno la Siri')

@section('content')
<div class="max-w-sm mx-auto">
  <!-- Header -->
  <div class="text-center mb-6">
    <div class="flex justify-center mb-3">
      <div class="relative">
        <div class="absolute inset-0 bg-amber-400/20 rounded-full blur-lg"></div>
        <div class="relative h-12 w-12 rounded-full bg-gradient-to-br from-amber-600 to-amber-800 flex items-center justify-center shadow-md">
          <i class="fas fa-key text-white text-lg"></i>
        </div>
      </div>
    </div>
    <h1 class="text-2xl font-bold bg-gradient-to-r from-amber-400 to-amber-500 bg-clip-text text-transparent">
      Weka Neno la Siri Jipya
    </h1>
  </div>

  @if(session('status'))
  <div id="status-alert" class="relative p-3 rounded-xl bg-gradient-to-r from-amber-600 to-amber-800 text-white shadow-lg animate-fade-in mb-4">
    <div class="flex items-center gap-2">
      <i class="fas fa-info-circle text-white text-sm"></i>
      <p class="text-sm font-medium">{{ session('status') }}</p>
    </div>
  </div>
  @endif

  @if($errors->any())
  <div id="error-alert" class="relative p-3 rounded-xl bg-gradient-to-r from-amber-600 to-amber-800 text-white shadow-lg animate-fade-in mb-4">
    <div class="flex items-center gap-2">
      <i class="fas fa-exclamation-triangle text-white text-sm"></i>
      <div>
        @foreach ($errors->all() as $error)
          <p class="text-sm font-medium">{{ $error }}</p>
        @endforeach
      </div>
    </div>
  </div>
  @endif

  <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    
    <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-gray-700 shadow-lg">
      <div class="text-center mb-4">
        <h2 class="text-lg font-bold text-amber-400">Neno la Siri Jipya</h2>
        <p class="text-gray-300 text-sm">Weka neno la siri jipya la akaunti yako</p>
      </div>

      <div class="space-y-4">
        <!-- Email -->
        <div class="group">
          <label class="block text-sm font-semibold text-gray-200 mb-2">
            <i class="fas fa-envelope text-amber-400 mr-2"></i>
            Barua Pepe
          </label>
          <input name="email" type="email" value="{{ $email ?? old('email') }}" required
                 placeholder="example@kampuni.com"
                 class="w-full rounded-lg py-3 px-4 bg-gray-700/50 border border-gray-600 text-white placeholder-gray-400 outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-300 text-sm">
        </div>

        <!-- New Password -->
        <div class="group">
          <label class="block text-sm font-semibold text-gray-200 mb-2">
            <i class="fas fa-lock text-amber-400 mr-2"></i>
            Neno la Siri Jipya
          </label>
          <input name="password" type="password" required
                 placeholder="Weka neno la siri jipya"
                 class="w-full rounded-lg py-3 px-4 bg-gray-700/50 border border-gray-600 text-white placeholder-gray-400 outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-300 text-sm">
        </div>

        <!-- Confirm Password -->
        <div class="group">
          <label class="block text-sm font-semibold text-gray-200 mb-2">
            <i class="fas fa-lock text-amber-400 mr-2"></i>
            Thibitisha Neno la Siri
          </label>
          <input name="password_confirmation" type="password" required
                 placeholder="Andika tena neno la siri"
                 class="w-full rounded-lg py-3 px-4 bg-gray-700/50 border border-gray-600 text-white placeholder-gray-400 outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-300 text-sm">
        </div>
      </div>

      <div class="mt-6">
        <button type="submit" 
                class="w-full py-3 rounded-lg bg-gradient-to-r from-amber-500 to-amber-600 text-white font-bold hover:from-amber-600 hover:to-amber-700 transition-all duration-300 shadow-md hover:shadow-lg text-sm">
          <i class="fas fa-sync-alt mr-2"></i> Badilisha Neno la Siri
        </button>
      </div>
    </div>
  </form>

  <!-- Navigation Links -->
  <div class="text-center mt-4">
    <a href="{{ route('login') }}" class="text-amber-400 hover:text-amber-300 text-sm">
      <i class="fas fa-arrow-left mr-1"></i> Rudi Kwenye Ingia
    </a>
  </div>
</div>

<script>
  // Auto-hide alerts after 3 seconds
  setTimeout(() => {
    const statusAlert = document.getElementById('status-alert');
    const errorAlert = document.getElementById('error-alert');
    
    if (statusAlert) {
      statusAlert.style.opacity = '0';
      statusAlert.style.transition = 'opacity 0.5s ease';
      setTimeout(() => statusAlert.remove(), 500);
    }
    
    if (errorAlert) {
      errorAlert.style.opacity = '0';
      errorAlert.style.transition = 'opacity 0.5s ease';
      setTimeout(() => errorAlert.remove(), 500);
    }
  }, 3000);
</script>

<style>
  .animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>
@endsection