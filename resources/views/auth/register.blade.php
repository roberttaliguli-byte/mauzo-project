@extends('layouts.guest')

@section('title', 'Sajili Kampuni')

@section('content')
<div class="max-w-md mx-auto">
  <!-- Success Notification -->
  @if(session('success'))
    <div id="success-notification" class="mb-4 p-3 rounded-lg bg-gradient-to-r from-green-900/50 to-emerald-900/30 border border-green-700 text-green-200 text-xs animate-fade-in">
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <i class="fas fa-check-circle mr-2 text-green-400"></i>
          <p>{{ session('success') }}</p>
        </div>
        <button onclick="document.getElementById('success-notification').remove()" class="text-green-300 hover:text-white">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>
    
    <script>
      // Auto-remove success notification after 5 seconds
      setTimeout(function() {
        const notification = document.getElementById('success-notification');
        if (notification) {
          notification.remove();
        }
      }, 5000);
    </script>
  @endif

  <!-- Error Notification -->
  @if(session('error'))
    <div id="error-notification" class="mb-4 p-3 rounded-lg bg-gradient-to-r from-red-900/50 to-rose-900/30 border border-red-700 text-red-200 text-xs animate-fade-in">
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <i class="fas fa-exclamation-circle mr-2 text-red-400"></i>
          <p>{{ session('error') }}</p>
        </div>
        <button onclick="document.getElementById('error-notification').remove()" class="text-red-300 hover:text-white">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>
    
    <script>
      // Auto-remove error notification after 5 seconds
      setTimeout(function() {
        const notification = document.getElementById('error-notification');
        if (notification) {
          notification.remove();
        }
      }, 5000);
    </script>
  @endif

  <!-- Header Section -->
  <div class="text-center mb-4">
    <div class="flex justify-center mb-2">
      <div class="relative">
        <div class="absolute inset-0 bg-yellow-400/20 rounded-full blur-lg"></div>
        <div class="relative h-10 w-10 rounded-full bg-gradient-to-br from-yellow-400 to-amber-500 flex items-center justify-center">
          <i class="fas fa-building text-white text-sm"></i>
        </div>
      </div>
    </div>
    <h1 class="text-xl font-bold bg-gradient-to-r from-yellow-400 to-amber-500 bg-clip-text text-transparent">
      Sajili Kampuni
    </h1>
    <p class="text-gray-300 text-xs mt-1">Anza kutumia MauzoSheet leo</p>
  </div>

  <!-- Progress Steps -->
  <div class="flex justify-center mb-4">
    <div class="flex items-center space-x-2">
      @foreach([1, 2, 3] as $step)
        <div class="flex items-center">
          <div class="step-indicator w-6 h-6 rounded-full flex items-center justify-center border-2 
            @if($step == ($currentStep ?? 1)) 
              bg-yellow-500 border-yellow-500 text-white
            @else
              border-gray-600 text-gray-400
            @endif
            font-semibold text-xs">
            {{ $step }}
          </div>
          @if($step < 3)
            <div class="w-6 h-1 bg-gray-600 mx-1"></div>
          @endif
        </div>
      @endforeach
    </div>
  </div>

  <!-- Validation Errors Alert -->
  @if ($errors->any())
    <div id="validation-errors" class="mb-4 p-3 rounded-lg bg-gradient-to-r from-red-900/50 to-rose-900/30 border border-red-700 text-red-200 text-xs animate-fade-in">
      <div class="flex items-center justify-between">
        <div class="flex items-start flex-1">
          <i class="fas fa-exclamation-circle mr-2 mt-0.5 text-red-400"></i>
          <div class="flex-1">
            @foreach ($errors->all() as $error)
              <p class="mb-1 last:mb-0">{{ $error }}</p>
            @endforeach
          </div>
        </div>
        <button onclick="document.getElementById('validation-errors').remove()" class="text-red-300 hover:text-white ml-2">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>
    
    <script>
      // Auto-remove validation errors after 10 seconds (longer since they're important)
      setTimeout(function() {
        const errors = document.getElementById('validation-errors');
        if (errors) {
          errors.remove();
        }
      }, 10000);
    </script>
  @endif

  <form id="multiStepForm" method="POST" action="{{ route('register.post') }}" class="space-y-3">
    @csrf

    {{-- Step 1 --}}
    <div class="step @if(($currentStep ?? 1) != 1) hidden @endif" data-step="1">
      <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-4 border border-gray-700 shadow-lg">
        <div class="text-center mb-3">
          <h2 class="text-base font-bold text-yellow-400">Taarifa za Msingi</h2>
        </div>

        <div class="space-y-3">
          <div>
            <label class="block text-xs font-semibold text-gray-200 mb-1">Jina la Kampuni</label>
            <input name="company_name" value="{{ old('company_name') }}" required
                   placeholder="Jina la kampuni"
                   class="w-full rounded-lg py-2 px-3 bg-gray-700/50 border @error('company_name') border-red-500 @else border-gray-600 @enderror text-white placeholder-gray-400 outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 text-sm">
            @error('company_name')
              <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-200 mb-1">Jina la Mmiliki</label>
            <input name="owner_name" value="{{ old('owner_name') }}" required
                   placeholder="Jina la mmiliki"
                   class="w-full rounded-lg py-2 px-3 bg-gray-700/50 border @error('owner_name') border-red-500 @else border-gray-600 @enderror text-white placeholder-gray-400 outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 text-sm">
            @error('owner_name')
              <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-200 mb-1">Jinsia</label>
              <select name="owner_gender" required 
                      class="w-full rounded-lg py-2 px-3 bg-gray-700/50 border @error('owner_gender') border-red-500 @else border-gray-600 @enderror text-white outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 text-sm">
                <option value="">Chagua</option>
                <option value="male" {{ old('owner_gender')=='male'?'selected':'' }}>Mwanaume</option>
                <option value="female" {{ old('owner_gender')=='female'?'selected':'' }}>Mwanamke</option>
              </select>
              @error('owner_gender')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-200 mb-1">Tarehe ya Kuzaliwa</label>
              <input name="owner_dob" type="date" value="{{ old('owner_dob') }}" required
                     class="w-full rounded-lg py-2 px-3 bg-gray-700/50 border @error('owner_dob') border-red-500 @else border-gray-600 @enderror text-white outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 text-sm">
              @error('owner_dob')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>
          </div>
        </div>

        <div class="mt-4 flex justify-between items-center">
          <div class="text-gray-400 text-xs">
            Hatua <span class="text-yellow-400">1</span>/3
          </div>
          <button type="button" data-action="next"
                  class="py-2 px-4 rounded-lg bg-gradient-to-r from-yellow-500 to-amber-500 text-black font-bold hover:from-yellow-600 hover:to-amber-600 transition-all duration-300 text-sm">
            Endelea
          </button>
        </div>
      </div>
    </div>

    {{-- Step 2 --}}
    <div class="step @if(($currentStep ?? 1) != 2) hidden @endif" data-step="2">
      <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-4 border border-gray-700 shadow-lg">
        <div class="text-center mb-3">
          <h2 class="text-base font-bold text-yellow-400">Mawasiliano</h2>
        </div>

        <div class="space-y-3">
          <div>
            <label class="block text-xs font-semibold text-gray-200 mb-1">Mahali</label>
            <input name="location" value="{{ old('location') }}" required
                   placeholder="Eneo la kampuni"
                   class="w-full rounded-lg py-2 px-3 bg-gray-700/50 border @error('location') border-red-500 @else border-gray-600 @enderror text-white placeholder-gray-400 outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 text-sm">
            @error('location')
              <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-200 mb-1">Mkoa</label>
            <select name="region" required 
                    class="w-full rounded-lg py-2 px-3 bg-gray-700/50 border @error('region') border-red-500 @else border-gray-600 @enderror text-white outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 text-sm">
              <option value="">Chagua mkoa</option>
              @foreach($regions ?? [] as $region)
                <option value="{{ $region }}" {{ old('region')== $region ? 'selected' : '' }}>{{ $region }}</option>
              @endforeach
            </select>
            @error('region')
              <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-200 mb-1">Simu</label>
              <input name="phone" value="{{ old('phone') }}" required
                     placeholder="07XXXXXXXX"
                     class="w-full rounded-lg py-2 px-3 bg-gray-700/50 border @error('phone') border-red-500 @else border-gray-600 @enderror text-white placeholder-gray-400 outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 text-sm">
              @error('phone')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-200 mb-1">Barua Pepe</label>
              <input name="company_email" type="email" required
                     value="{{ old('company_email') }}"
                     placeholder="example@kampuni.com"
                     class="w-full rounded-lg py-2 px-3 bg-gray-700/50 border @error('company_email') border-red-500 @else border-gray-600 @enderror text-white placeholder-gray-400 outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 text-sm">
              @error('company_email')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>
          </div>
        </div>

        <div class="mt-4 flex justify-between items-center">
          <button type="button" data-action="prev"
                  class="py-2 px-4 rounded-lg bg-gray-700 text-white font-bold hover:bg-gray-600 transition-all duration-300 text-sm">
            Rudi
          </button>
          
          <div class="text-gray-400 text-xs">
            Hatua <span class="text-yellow-400">2</span>/3
          </div>
          
          <button type="button" data-action="next"
                  class="py-2 px-4 rounded-lg bg-gradient-to-r from-yellow-500 to-amber-500 text-black font-bold hover:from-yellow-600 hover:to-amber-600 transition-all duration-300 text-sm">
            Endelea
          </button>
        </div>
      </div>
    </div>

    {{-- Step 3 --}}
    <div class="step @if(($currentStep ?? 1) != 3) hidden @endif" data-step="3">
      <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-4 border border-gray-700 shadow-lg">
        <div class="text-center mb-3">
          <h2 class="text-base font-bold text-yellow-400">Akaunti</h2>
        </div>

        <div class="space-y-3">
          <div>
            <label class="block text-xs font-semibold text-gray-200 mb-1">Jina la Mtumiaji</label>
            <input name="username" value="{{ old('username') }}" required
                   placeholder="Jina la kutumia"
                   class="w-full rounded-lg py-2 px-3 bg-gray-700/50 border @error('username') border-red-500 @else border-gray-600 @enderror text-white placeholder-gray-400 outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 text-sm">
            @error('username')
              <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-200 mb-1">Neno la Siri</label>
              <input name="password" type="password" required
                     placeholder="Neno la siri"
                     class="w-full rounded-lg py-2 px-3 bg-gray-700/50 border @error('password') border-red-500 @else border-gray-600 @enderror text-white placeholder-gray-400 outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 text-sm">
              @error('password')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-200 mb-1">Thibitisha</label>
              <input name="password_confirmation" type="password" required
                     placeholder="Andika tena"
                     class="w-full rounded-lg py-2 px-3 bg-gray-700/50 border border-gray-600 text-white placeholder-gray-400 outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 text-sm">
            </div>
          </div>
        </div>

        <div class="mt-4 flex justify-between items-center">
          <button type="button" data-action="prev"
                  class="py-2 px-4 rounded-lg bg-gray-700 text-white font-bold hover:bg-gray-600 transition-all duration-300 text-sm">
            Rudi
          </button>
          
          <div class="text-gray-400 text-xs">
            Hatua <span class="text-yellow-400">3</span>/3
          </div>
          
          <button type="submit"
                  class="py-2 px-4 rounded-lg bg-gradient-to-r from-green-500 to-emerald-500 text-white font-bold hover:from-green-600 hover:to-emerald-600 transition-all duration-300 text-sm">
            Sajili
          </button>
        </div>
      </div>
    </div>

    {{-- Login Link --}}
    <div class="text-center mt-3">
      <p class="text-gray-400 text-xs">
        Una akaunti? 
        <a href="{{ route('login') }}" class="text-yellow-400 font-semibold hover:text-yellow-300">
          Ingia
        </a>
      </p>
    </div>
  </form>
</div>

@push('styles')
<style>
  @keyframes fade-in {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .animate-fade-in {
    animation: fade-in 0.3s ease-out;
  }
</style>
@endpush

@push('scripts')
<script>
  (function () {
    const form = document.getElementById('multiStepForm');
    const steps = Array.from(form.querySelectorAll('.step'));
    
    // Get current step from server-side or default to 1
    let index = {{ $currentStep ?? 1 }} - 1;
    
    const update = () => {
      // Update step visibility
      steps.forEach((s, i) => {
        s.classList.toggle('hidden', i !== index);
      });
      
      // Update step indicators
      document.querySelectorAll('.step-indicator').forEach((indicator, i) => {
        if (i === index) {
          indicator.classList.add('bg-yellow-500', 'border-yellow-500', 'text-white');
          indicator.classList.remove('border-gray-600', 'text-gray-400');
        } else {
          indicator.classList.remove('bg-yellow-500', 'border-yellow-500', 'text-white');
          indicator.classList.add('border-gray-600', 'text-gray-400');
        }
      });
    };

    // Initialize on page load
    update();

    form.addEventListener('click', function (e) {
      const btn = e.target.closest('button[data-action]');
      if (!btn) return;
      const action = btn.dataset.action;

      if (action === 'next') {
        const currentStep = steps[index];
        const inputs = currentStep.querySelectorAll('input[required], select[required]');
        let valid = true;
        
        for (const el of inputs) {
          if (!el.value || !el.value.toString().trim()) {
            el.classList.add('border-red-500');
            el.focus();
            valid = false;
            break;
          } else {
            el.classList.remove('border-red-500');
          }
        }
        
        if (valid && index < steps.length - 1) {
          index++;
          update();
        }
      } else if (action === 'prev') {
        if (index > 0) {
          index--;
          update();
        }
      }
    });

    // Clear red borders on input
    form.addEventListener('input', function(e) {
      if (e.target.hasAttribute('required')) {
        e.target.classList.remove('border-red-500');
      }
    });
  })();
</script>
@endpush
@endsection