@extends('layouts.guest')

@section('title', 'Sajili Kampuni')

@section('content')
<div class="max-w-md mx-auto">
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
          <div class="w-6 h-6 rounded-full flex items-center justify-center border-2 
            {{ $step == 1 ? 'bg-yellow-500 border-yellow-500 text-white' : 'border-gray-600 text-gray-400' }}
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

  <form id="multiStepForm" method="POST" action="{{ route('register.post') }}" class="space-y-3">
    @csrf

    {{-- Step 1 --}}
    <div class="step" data-step="1">
      <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-4 border border-gray-700 shadow-lg">
        <div class="text-center mb-3">
          <h2 class="text-base font-bold text-yellow-400">Taarifa za Msingi</h2>
        </div>

        <div class="space-y-3">
          <div>
            <label class="block text-xs font-semibold text-gray-200 mb-1">Jina la Kampuni</label>
            <input name="company_name" value="{{ old('company_name') }}" required
                   placeholder="Jina la kampuni"
                   class="w-full rounded-lg py-2 px-3 bg-gray-700/50 border border-gray-600 text-white placeholder-gray-400 outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 text-sm">
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-200 mb-1">Jina la Mmiliki</label>
            <input name="owner_name" value="{{ old('owner_name') }}" required
                   placeholder="Jina la mmiliki"
                   class="w-full rounded-lg py-2 px-3 bg-gray-700/50 border border-gray-600 text-white placeholder-gray-400 outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 text-sm">
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-200 mb-1">Jinsia</label>
              <select name="owner_gender" required 
                      class="w-full rounded-lg py-2 px-3 bg-gray-700/50 border border-gray-600 text-white outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 text-sm">
                <option value="">Chagua</option>
                <option value="male" {{ old('owner_gender')=='male'?'selected':'' }}>Mwanaume</option>
                <option value="female" {{ old('owner_gender')=='female'?'selected':'' }}>Mwanamke</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-200 mb-1">Tarehe ya Kuzaliwa</label>
              <input name="owner_dob" type="date" value="{{ old('owner_dob') }}" required
                     class="w-full rounded-lg py-2 px-3 bg-gray-700/50 border border-gray-600 text-white outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 text-sm">
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
    <div class="step hidden" data-step="2">
      <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-4 border border-gray-700 shadow-lg">
        <div class="text-center mb-3">
          <h2 class="text-base font-bold text-yellow-400">Mawasiliano</h2>
        </div>

        <div class="space-y-3">
          <div>
            <label class="block text-xs font-semibold text-gray-200 mb-1">Mahali</label>
            <input name="location" value="{{ old('location') }}" required
                   placeholder="Eneo la kampuni"
                   class="w-full rounded-lg py-2 px-3 bg-gray-700/50 border border-gray-600 text-white placeholder-gray-400 outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 text-sm">
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-200 mb-1">Mkoa</label>
            <select name="region" required 
                    class="w-full rounded-lg py-2 px-3 bg-gray-700/50 border border-gray-600 text-white outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 text-sm">
              <option value="">Chagua mkoa</option>
              @foreach($regions ?? [] as $region)
                <option value="{{ $region }}" {{ old('region')== $region ? 'selected' : '' }}>{{ $region }}</option>
              @endforeach
            </select>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-200 mb-1">Simu</label>
              <input name="phone" value="{{ old('phone') }}" required
                     placeholder="07XXXXXXXX"
                     class="w-full rounded-lg py-2 px-3 bg-gray-700/50 border border-gray-600 text-white placeholder-gray-400 outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 text-sm">
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-200 mb-1">Barua Pepe</label>
              <input name="company_email" value="{{ old('company_email') }}"
                     placeholder="example@kampuni.com"
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
    <div class="step hidden" data-step="3">
      <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-4 border border-gray-700 shadow-lg">
        <div class="text-center mb-3">
          <h2 class="text-base font-bold text-yellow-400">Akaunti</h2>
        </div>

        <div class="space-y-3">
          <div>
            <label class="block text-xs font-semibold text-gray-200 mb-1">Jina la Mtumiaji</label>
            <input name="username" value="{{ old('username') }}" required
                   placeholder="Jina la kutumia"
                   class="w-full rounded-lg py-2 px-3 bg-gray-700/50 border border-gray-600 text-white placeholder-gray-400 outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 text-sm">
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-200 mb-1">Neno la Siri</label>
              <input name="password" type="password" required
                     placeholder="Neno la siri"
                     class="w-full rounded-lg py-2 px-3 bg-gray-700/50 border border-gray-600 text-white placeholder-gray-400 outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 text-sm">
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

    {{-- Validation errors --}}
    @if ($errors->any())
      <div class="mt-3 p-2 rounded-lg bg-red-900/50 border border-red-700 text-red-200 text-xs">
        <ul class="space-y-1">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
  </form>
</div>

@push('scripts')
<script>
  (function () {
    const form = document.getElementById('multiStepForm');
    const steps = Array.from(form.querySelectorAll('.step'));
    let index = 0;

    const update = () => {
      steps.forEach((s, i) => s.classList.toggle('hidden', i !== index));
      document.querySelectorAll('[data-step]').forEach((step, i) => {
        const stepNumber = step.querySelector('.w-6');
        if (stepNumber) {
          if (i === index) {
            stepNumber.classList.add('bg-yellow-500', 'border-yellow-500', 'text-white');
            stepNumber.classList.remove('border-gray-600', 'text-gray-400');
          } else {
            stepNumber.classList.remove('bg-yellow-500', 'border-yellow-500', 'text-white');
            stepNumber.classList.add('border-gray-600', 'text-gray-400');
          }
        }
      });
    };

    form.addEventListener('click', function (e) {
      const btn = e.target.closest('button[data-action]');
      if (!btn) return;
      const action = btn.dataset.action;

      if (action === 'next') {
        const inputs = steps[index].querySelectorAll('input[required], select[required]');
        let valid = true;
        
        for (const el of inputs) {
          if (!el.value || !el.value.toString().trim()) {
            el.classList.add('border', 'border-red-500');
            el.focus();
            valid = false;
          } else {
            el.classList.remove('border', 'border-red-500');
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

    update();
  })();
</script>
@endpush
@endsection


