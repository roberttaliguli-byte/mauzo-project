@extends('layouts.guest')

@section('title', 'Badilisha Neno la Siri')

@section('content')
<!-- Header -->
<div class="text-center mb-6">
  <h1 class="auth-title">Badilisha Neno la Siri</h1>
  <p class="text-gray-300 text-sm">Tuma kiungo cha kubadilisha neno la siri kwenye barua pepe yako</p>
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

<form method="POST" action="{{ route('password.email') }}" class="space-y-4">
  @csrf
  
  <!-- Email Field -->
  <div class="field">
    <label for="email">Barua Pepe</label>
    <input name="email" id="email" type="email" value="{{ old('email') }}" required
           placeholder="example@kampuni.com"
           class="form-input @error('email') invalid @enderror"
           autocomplete="email" autofocus>
    <div class="field-error" id="email_message">@error('email'){{ $message }}@enderror</div>
  </div>

  <!-- Submit Button -->
  <div class="actions">
    <button type="submit" id="submitBtn" class="btn-primary">
      <span class="btn-label">Tuma Kiungo cha Kubadilisha</span>
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

    // Form submission loading state
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');

    if (form && submitBtn) {
      form.addEventListener('submit', function(e) {
        const email = document.getElementById('email');
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
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
          email.classList.add('invalid');
          const msg = document.getElementById('email_message');
          if (msg) {
            msg.textContent = 'Tafadhali weka barua pepe sahihi.';
            msg.classList.add('show');
          }
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
      });
    });

    console.log('✅ Forgot Password page loaded');
  })();
</script>
@endsection