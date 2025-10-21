@extends('layouts.guest')

@section('title', 'Sajili Kampuni')

@section('content')
  <div class="max-w-2xl mx-auto">
    <div class="text-center mb-6">
      <h1 class="text-2xl font-semibold text-yellow-300">Sajili Kampuni</h1>
      <p class="text-gray-300 text-sm mt-1">Hatua 1/3 — Toa taarifa za msingi</p>
    </div>

    <form id="multiStepForm" method="POST" action="{{ route('register.post') }}">
      @csrf

      {{-- Step 1 --}}
      <div class="step" data-step="1">
        <div class="space-y-4">
          <div>
            <label class="block text-sm text-gray-200 mb-1">Jina la Kampuni</label>
            <input name="company_name" value="{{ old('company_name') }}" required
                   class="w-full rounded-full py-3 px-4 bg-gray-100 text-black outline-none focus:ring-2 focus:ring-yellow-400">
          </div>

          <div>
            <label class="block text-sm text-gray-200 mb-1">Jina la Mmiliki</label>
            <input name="owner_name" value="{{ old('owner_name') }}" required
                   class="w-full rounded-full py-3 px-4 bg-gray-100 text-black outline-none focus:ring-2 focus:ring-yellow-400">
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="block text-sm text-gray-200 mb-1">Jinsia</label>
              <select name="owner_gender" required class="w-full rounded-full py-3 px-4 bg-gray-100 text-black">
                <option value="">Chagua</option>
                <option value="male" {{ old('owner_gender')=='male'?'selected':'' }}>Mwanaume</option>
                <option value="female" {{ old('owner_gender')=='female'?'selected':'' }}>Mwanamke</option>
                <option value="other" {{ old('owner_gender')=='other'?'selected':'' }}>Nyingine</option>
              </select>
            </div>

            <div>
              <label class="block text-sm text-gray-200 mb-1">Tarehe ya Kuzaliwa</label>
              <input name="owner_dob" type="date" value="{{ old('owner_dob') }}" required
                     class="w-full rounded-full py-3 px-4 bg-gray-100 text-black">
            </div>
          </div>
        </div>

        <div class="mt-6 text-right">
          <button type="button" data-action="next"
                  class="py-3 px-6 rounded-full bg-yellow-500 text-black font-semibold hover:bg-yellow-400">
            Endelea
          </button>
        </div>
        {{-- ✅ Add this section --}}
  <div class="mt-6 text-center text-gray-300 text-sm">
    Tayari una akaunti? 
    <a href="{{ route('login') }}" class="text-yellow-300 font-semibold hover:underline">
      Ingia kwa Kampuni
    </a>
  </div>
      </div>

      {{-- Step 2 --}}
      <div class="step hidden" data-step="2">
        <p class="text-gray-300 text-sm mb-4">Hatua 2/3 — Mahali na mawasiliano</p>
        <div class="space-y-4">
          <div>
            <label class="block text-sm text-gray-200 mb-1">Mahali ilipo Kampuni</label>
            <input name="location" value="{{ old('location') }}" required
                   class="w-full rounded-full py-3 px-4 bg-gray-100 text-black">
          </div>

          <div>
            <label class="block text-sm text-gray-200 mb-1">Mkoa</label>
            <select name="region" required class="w-full rounded-full py-3 px-4 bg-gray-100 text-black">
              <option value="">Chagua</option>
              @foreach($regions ?? [] as $region)
                <option value="{{ $region }}" {{ old('region')== $region ? 'selected' : '' }}>{{ $region }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="block text-sm text-gray-200 mb-1">Simu</label>
            <input name="phone" value="{{ old('phone') }}" required
                   class="w-full rounded-full py-3 px-4 bg-gray-100 text-black">
          </div>

          <div>
            <label class="block text-sm text-gray-200 mb-1">Barua pepe (hiari)</label>
            <input name="company_email" value="{{ old('company_email') }}"
                   class="w-full rounded-full py-3 px-4 bg-gray-100 text-black">
          </div>
        </div>

        <div class="mt-6 flex items-center justify-between">
          <button type="button" data-action="prev"
                  class="py-3 px-6 rounded-full bg-gray-700 text-white hover:bg-gray-600">Rudi</button>

          <button type="button" data-action="next"
                  class="py-3 px-6 rounded-full bg-yellow-500 text-black font-semibold hover:bg-yellow-400">Endelea</button>
        </div>
        {{-- ✅ Add this section --}}
  <div class="mt-6 text-center text-gray-300 text-sm">
    Tayari una akaunti? 
    <a href="{{ route('login') }}" class="text-yellow-300 font-semibold hover:underline">
      Ingia kwa Kampuni
    </a>
  </div>
      </div>

      {{-- Step 3 --}}
      <div class="step hidden" data-step="3">
        <p class="text-gray-300 text-sm mb-4">Hatua 3/3 — Taarifa za akaunti</p>

        <div class="space-y-4">
          <div>
            <label class="block text-sm text-gray-200 mb-1">Jina la Kuingia (username)</label>
            <input name="username" value="{{ old('username') }}" required
                   class="w-full rounded-full py-3 px-4 bg-gray-100 text-black">
          </div>

          <div>
            <label class="block text-sm text-gray-200 mb-1">Neno la Siri</label>
            <input name="password" type="password" required
                   class="w-full rounded-full py-3 px-4 bg-gray-100 text-black">
          </div>

          <div>
            <label class="block text-sm text-gray-200 mb-1">Thibitisha Neno la Siri</label>
            <input name="password_confirmation" type="password" required
                   class="w-full rounded-full py-3 px-4 bg-gray-100 text-black">
          </div>
        </div>

        <div class="mt-6 flex items-center justify-between">
          <button type="button" data-action="prev"
                  class="py-3 px-6 rounded-full bg-gray-700 text-white hover:bg-gray-600">Rudi</button>

          <button type="submit"
                  class="py-3 px-6 rounded-full bg-yellow-500 text-black font-semibold hover:bg-yellow-400">Sajili</button>
        </div>

        <div class="mt-4 text-center text-gray-300 text-sm">
          Tayari una akaunti? <a href="{{ route('login') }}" class="text-yellow-300 hover:underline">Ingia</a>
        </div>
      </div>

      {{-- Validation errors summary --}}
      @if ($errors->any())
        <div class="mt-6 text-red-400">
          <ul class="list-disc list-inside text-sm">
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
        // update small status if present
        const status = document.getElementById('stepCounter');
        if (status) status.textContent = (index + 1) + '/3';
      };

      form.addEventListener('click', function (e) {
        const btn = e.target.closest('button[data-action]');
        if (!btn) return;
        const action = btn.dataset.action;

        if (action === 'next') {
          // basic required input check
          const inputs = steps[index].querySelectorAll('input[required], select[required]');
          for (const el of inputs) {
            if (!el.value || !el.value.toString().trim()) {
              el.classList.add('border', 'border-red-500');
              el.focus();
              return;
            } else {
              el.classList.remove('border', 'border-red-500');
            }
          }
          if (index < steps.length - 1) index++;
          update();
        } else if (action === 'prev') {
          if (index > 0) index--;
          update();
        }
      });

      // initial
      update();
    })();
  </script>
  @endpush
@endsection
