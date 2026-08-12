@extends('layouts.guest')

@section('title', 'Weka Neno la Siri Jipya')

@section('content')
<!-- Header -->
<div class="text-center mb-6">
  <h1 class="auth-title">Weka Neno la Siri Jipya</h1>
  <p class="text-gray-300 text-sm">Weka neno la siri jipya la akaunti yako</p>
</div>

@if(session('status'))
<div id="status-alert" class="alert alert-ok">
  <span>{{ session('status') }}</span>
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

<form method="POST" action="{{ route('password.update') }}" class="space-y-4">
  @csrf
  <input type="hidden" name="token" value="{{ $token }}">

  <!-- Email -->
  <div class="field">
    <label for="email">Barua Pepe</label>
    <input name="email" id="email" type="email" value="{{ $email ?? old('email') }}" required
           placeholder="example@kampuni.com"
           class="form-input @error('email') invalid @enderror"
           autocomplete="email">
    <div class="field-error" id="email_message">@error('email'){{ $message }}@enderror</div>
  </div>

  <!-- New Password -->
  <div class="field">
    <label for="password">Neno la Siri Jipya</label>
    <div class="input-wrapper">
      <input name="password" id="password" type="password" required
             placeholder="Weka neno la siri jipya"
             class="form-input @error('password') invalid @enderror"
             autocomplete="new-password">
      <button type="button" class="pw-toggle" id="togglePassword" aria-label="Onyesha nenosiri">
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

  <!-- Submit -->
  <div class="actions">
    <button type="submit" id="submitBtn" class="btn-primary">
      <span class="btn-label">Badilisha Neno la Siri</span>
      <span class="spinner"></span>
    </button>
  </div>
</form>

<!-- Navigation -->
<div class="auth-footer">
  <a href="{{ route('login') }}">← Rudi Kwenye Ingia</a>
</div>

<script>
  (function() {
    'use strict';

    // Auto-hide alerts after 5 seconds
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

    setupToggle('togglePassword', 'password');
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
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const confirm = document.getElementById('password_confirmation');
        let hasError = false;

        // Clear previous errors
        document.querySelectorAll('.form-input').forEach(el => el.classList.remove('invalid'));
        document.querySelectorAll('.field-error').forEach(el => el.classList.remove('show'));

        // Validate email
        if (!email.value.trim()) {
          email.classList.add('invalid');
          const msg = document.getElementById('email_message');
          if (msg) {
            msg.textContent = 'Tafadhali weka barua pepe yako.';
            msg.classList.add('show');
          }
          hasError = true;
        }

        // Validate password
        if (!password.value.trim()) {
          password.classList.add('invalid');
          const msg = document.getElementById('password_message');
          if (msg) {
            msg.textContent = 'Tafadhali weka neno la siri jipya.';
            msg.classList.add('show');
          }
          hasError = true;
        } else if (password.value.length < 6) {
          password.classList.add('invalid');
          const msg = document.getElementById('password_message');
          if (msg) {
            msg.textContent = 'Neno la siri lazima liwe na angalau herufi 6.';
            msg.classList.add('show');
          }
          hasError = true;
        }

        // Validate confirmation
        if (password.value !== confirm.value) {
          confirm.classList.add('invalid');
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

    console.log('✅ Reset Password page loaded');
  })();
</script>
@endsection