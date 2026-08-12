@extends('layouts.guest')

@section('title', 'Badilisha Neno la Siri')

@section('content')
<!-- Header -->
<div class="text-center mb-6">
  <h1 class="auth-title">Badilisha Neno la Siri</h1>
  <p class="text-gray-300 text-sm">Jaza fomu hapo chini kubadilisha neno la siri lako</p>
</div>

@if(session('success'))
<div id="success-alert" class="alert alert-ok">
  <span>{{ session('success') }}</span>
  <button type="button" onclick="this.closest('.alert').remove()">&times;</button>
</div>
@endif

@if($errors->any())
<div id="error-alert" class="alert alert-bad">
  <span>
    @foreach ($errors->all() as $error)
      {{ $error }}@if(!$loop->last)<br>@endif
    @endforeach
  </span>
  <button type="button" onclick="this.closest('.alert').remove()">&times;</button>
</div>
@endif

<form method="POST" action="{{ route('password.update.auth') }}" class="space-y-4">
  @csrf

  <!-- Current Password -->
  <div class="field">
    <label for="current_password">Neno la Siri la Sasa</label>
    <div class="input-wrapper">
      <input name="current_password" id="current_password" type="password" required
             placeholder="Weka neno la siri la sasa"
             class="form-input @error('current_password') invalid @enderror"
             autocomplete="current-password">
      <button type="button" class="pw-toggle" id="toggleCurrent" aria-label="Onyesha nenosiri">
        <svg viewBox="0 0 24 24">
          <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
          <circle cx="12" cy="12" r="3"/>
        </svg>
      </button>
    </div>
    <div class="field-error" id="current_password_message">@error('current_password'){{ $message }}@enderror</div>
  </div>

  <!-- New Password -->
  <div class="field">
    <label for="password">Neno la Siri Jipya</label>
    <div class="input-wrapper">
      <input name="password" id="password" type="password" required
             placeholder="Weka neno la siri jipya"
             class="form-input @error('password') invalid @enderror"
             autocomplete="new-password">
      <button type="button" class="pw-toggle" id="toggleNew" aria-label="Onyesha nenosiri">
        <svg viewBox="0 0 24 24">
          <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
          <circle cx="12" cy="12" r="3"/>
        </svg>
      </button>
    </div>
    <div id="password_hint" class="pw-hint">Angalau herufi 6</div>
    <div class="field-error" id="password_message">@error('password'){{ $message }}@enderror</div>
  </div>

  <!-- Confirm Password -->
  <div class="field">
    <label for="password_confirmation">Thibitisha Neno la Siri</label>
    <div class="input-wrapper">
      <input name="password_confirmation" id="password_confirmation" type="password" required
             placeholder="Andika tena neno la siri"
             class="form-input"
             autocomplete="new-password">
      <button type="button" class="pw-toggle" id="toggleConfirm" aria-label="Onyesha nenosiri">
        <svg viewBox="0 0 24 24">
          <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
          <circle cx="12" cy="12" r="3"/>
        </svg>
      </button>
    </div>
    <div id="match_status" class="field-error"></div>
  </div>

  <!-- Submit Button -->
  <div class="actions">
    <button type="submit" id="submitBtn" class="btn-primary">
      <span class="btn-label">Badilisha Neno la Siri</span>
      <span class="spinner"></span>
    </button>
  </div>
</form>

<!-- Navigation Links -->
<div class="auth-footer">
  <a href="{{ route('login') }}">← Rudi Kwenye Ingia</a>
</div>

<script>
  (function() {
    'use strict';

    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
      const successAlert = document.getElementById('success-alert');
      const errorAlert = document.getElementById('error-alert');
      
      if (successAlert) {
        successAlert.style.opacity = '0';
        successAlert.style.transition = 'opacity 0.5s ease';
        setTimeout(() => successAlert.remove(), 500);
      }
      
      if (errorAlert) {
        errorAlert.style.opacity = '0';
        errorAlert.style.transition = 'opacity 0.5s ease';
        setTimeout(() => errorAlert.remove(), 500);
      }
    }, 5000);

    // Toggle password visibility
    function setupToggle(buttonId, inputId) {
      const btn = document.getElementById(buttonId);
      const input = document.getElementById(inputId);
      if (btn && input) {
        btn.addEventListener('click', function() {
          const isPassword = input.type === 'password';
          input.type = isPassword ? 'text' : 'password';
          const svg = this.querySelector('svg');
          if (svg) {
            if (isPassword) {
              svg.innerHTML = `
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
              `;
            } else {
              svg.innerHTML = `
                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
                <circle cx="12" cy="12" r="3"/>
              `;
            }
          }
        });
      }
    }

    setupToggle('toggleCurrent', 'current_password');
    setupToggle('toggleNew', 'password');
    setupToggle('toggleConfirm', 'password_confirmation');

    // Password validation and confirmation check
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const matchStatus = document.getElementById('match_status');
    const passwordHint = document.getElementById('password_hint');

    passwordInput.addEventListener('input', function() {
      const value = this.value;
      if (value.length > 0 && value.length < 6) {
        passwordHint.classList.add('show');
        passwordHint.style.color = '#fca5a5';
      } else if (value.length >= 6) {
        passwordHint.classList.remove('show');
      } else {
        passwordHint.classList.remove('show');
      }
      checkMatch();
    });

    confirmInput.addEventListener('input', checkMatch);

    function checkMatch() {
      const password = passwordInput.value;
      const confirm = confirmInput.value;
      
      if (confirm.length === 0) {
        matchStatus.classList.remove('show');
        return;
      }
      
      if (password === confirm && password.length >= 6) {
        matchStatus.textContent = '✓ Nenosiri linalingana';
        matchStatus.style.color = '#86efac';
        matchStatus.classList.add('show');
      } else {
        matchStatus.textContent = '✗ Nenosiri halilingani';
        matchStatus.style.color = '#fca5a5';
        matchStatus.classList.add('show');
      }
    }

    // Form submission validation
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');

    if (form && submitBtn) {
      form.addEventListener('submit', function(e) {
        const currentPw = document.getElementById('current_password');
        const newPw = document.getElementById('password');
        const confirmPw = document.getElementById('password_confirmation');
        let hasError = false;

        // Clear previous errors
        document.querySelectorAll('.form-input').forEach(el => el.classList.remove('invalid'));
        document.querySelectorAll('.field-error').forEach(el => el.classList.remove('show'));

        // Validate current password
        if (!currentPw.value.trim()) {
          currentPw.classList.add('invalid');
          const msg = document.getElementById('current_password_message');
          if (msg) {
            msg.textContent = 'Tafadhali weka neno la siri la sasa.';
            msg.classList.add('show');
          }
          hasError = true;
        }

        // Validate new password
        if (!newPw.value.trim()) {
          newPw.classList.add('invalid');
          const msg = document.getElementById('password_message');
          if (msg) {
            msg.textContent = 'Tafadhali weka neno la siri jipya.';
            msg.classList.add('show');
          }
          hasError = true;
        } else if (newPw.value.length < 6) {
          newPw.classList.add('invalid');
          const msg = document.getElementById('password_message');
          if (msg) {
            msg.textContent = 'Neno la siri lazima liwe na angalau herufi 6.';
            msg.classList.add('show');
          }
          hasError = true;
        }

        // Validate confirmation
        if (newPw.value !== confirmPw.value) {
          confirmPw.classList.add('invalid');
          matchStatus.textContent = '✗ Nenosiri halilingani';
          matchStatus.style.color = '#fca5a5';
          matchStatus.classList.add('show');
          hasError = true;
        }

        if (hasError) {
          e.preventDefault();
          return;
        }

        // Show loading state
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
      });
    }

    // Clear error state on input
    document.querySelectorAll('.form-input').forEach(input => {
      input.addEventListener('input', function() {
        this.classList.remove('invalid');
        const msgId = this.id + '_message';
        const msg = document.getElementById(msgId);
        if (msg) {
          msg.textContent = '';
          msg.classList.remove('show');
        }
        if (this.id === 'password' || this.id === 'password_confirmation') {
          checkMatch();
        }
      });
    });

    console.log('✅ Change Password page loaded');
  })();
</script>
@endsection