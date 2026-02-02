@extends('layouts.app')

@section('title', 'Ripoti za Biashara')
@section('page-title', 'Ripoti za Biashara')
@section('page-subtitle', 'Tengeneza ripoti kwa urahisi na uwezo mkubwa')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">

  <div class="px-4 mx-auto max-w-7xl md:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8 text-center">
      <h1 class="text-2xl font-bold text-gray-900 md:text-3xl">📊 Ripoti za Biashara</h1>
      <p class="max-w-2xl mx-auto mt-2 text-gray-600">Chagua aina ya ripoti na muda, halafu pakua kwa urahisi</p>
    </div>

    <!-- Main Selection Card -->
    <div class="overflow-hidden bg-white rounded-2xl shadow-xl">
      <div class="p-6 md:p-8">
        <form id="reportForm" method="POST" action="{{ route('user.reports.download') }}" target="_blank">
          @csrf
          
          <!-- Selection Grid -->
          <div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-3">
            
            <!-- Report Type Card -->
            <div class="relative p-6 bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl">
              <div class="absolute top-4 right-4">
                <span class="px-3 py-1 text-xs font-semibold text-amber-800 bg-amber-200 rounded-full">Hatua 1</span>
              </div>
              <div class="flex items-center mb-4">
                <div class="flex items-center justify-center w-12 h-12 mr-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600">
                  <span class="text-xl">📊</span>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-gray-900">Aina ya Ripoti</h3>
                  <p class="text-sm text-gray-600">Chagua ripoti unayotaka</p>
                </div>
              </div>
              <div class="relative">
                <select name="report_type" id="report_type" class="w-full px-4 py-4 text-gray-800 bg-white border-2 border-gray-200 rounded-xl appearance-none focus:border-amber-500 focus:ring-4 focus:ring-amber-100 focus:outline-none">
                  <option value="sales">📊 Ripoti ya Mauzo</option>
                  <option value="manunuzi">🛒 Ripoti ya Manunuzi</option>
                  <option value="general">📋 Ripoti ya Jumla</option>
                </select>
              </div>
            </div>

            <!-- Time Period Card -->
            <div class="relative p-6 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl">
              <div class="absolute top-4 right-4">
                <span class="px-3 py-1 text-xs font-semibold text-blue-800 bg-blue-200 rounded-full">Hatua 2</span>
              </div>
              <div class="flex items-center mb-4">
                <div class="flex items-center justify-center w-12 h-12 mr-4 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600">
                  <span class="text-xl">⏰</span>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-gray-900">Muda wa Ripoti</h3>
                  <p class="text-sm text-gray-600">Weka muda wa ripoti</p>
                </div>
              </div>
              <div class="relative">
                <select name="time_period" id="time_period" class="w-full px-4 py-4 text-gray-800 bg-white border-2 border-gray-200 rounded-xl appearance-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none">
                  <option value="today">Leo</option>
                  <option value="yesterday">Jana</option>
                  <option value="week">Wiki hii</option>
                  <option value="month">Mwezi huu</option>
                  <option value="year">Mwaka huu</option>
                  <option value="custom">Tarehe Maalum</option>
                </select>
              </div>
            </div>

            <!-- Custom Dates Card -->
            <div id="customDates" class="relative hidden p-6 bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl">
              <div class="absolute top-4 right-4">
                <span class="px-3 py-1 text-xs font-semibold text-emerald-800 bg-emerald-200 rounded-full">Hatua 3</span>
              </div>
              <div class="flex items-center mb-4">
                <div class="flex items-center justify-center w-12 h-12 mr-4 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600">
                  <span class="text-xl">📅</span>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-gray-900">Tarehe Maalum</h3>
                  <p class="text-sm text-gray-600">Weka tarehe maalum</p>
                </div>
              </div>
              <div class="space-y-4">
                <div>
                  <label class="block mb-2 text-sm font-medium text-gray-700">Kuanzia Tarehe</label>
                  <input type="date" name="from" id="dateFrom" class="w-full px-4 py-3 text-gray-800 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 focus:outline-none">
                </div>
                <div>
                  <label class="block mb-2 text-sm font-medium text-gray-700">Mpaka Tarehe</label>
                  <input type="date" name="to" id="dateTo" class="w-full px-4 py-3 text-gray-800 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 focus:outline-none">
                </div>
              </div>
            </div>

          </div>

          <!-- Error Message -->
          <div id="errorMessage" class="hidden p-4 mb-6 text-red-700 bg-red-50 border-l-4 border-red-500 rounded-lg">
            <div class="flex">
              <svg class="w-6 h-6 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <div>
                <p id="errorText" class="font-medium"></p>
              </div>
            </div>
          </div>

          <!-- Success Message -->
          <div id="successMessage" class="hidden p-4 mb-6 text-green-700 bg-green-50 border-l-4 border-green-500 rounded-lg">
            <div class="flex">
              <svg class="w-6 h-6 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <div>
                <p id="successText" class="font-medium"></p>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col gap-4 md:flex-row">
            <!-- Download Button -->
            <button type="submit" id="generateBtn" class="flex-1 relative flex items-center justify-center gap-3 px-8 py-5 font-bold text-white transition-all duration-300 transform rounded-2xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 hover:scale-[1.02] hover:shadow-xl active:scale-[0.98]">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
              </svg>
              <span class="text-lg">📥 Pakua Ripoti</span>
            </button>

            <!-- Reset Button -->
            <button type="button" id="resetBtn" class="flex-1 relative flex items-center justify-center gap-3 px-8 py-5 font-bold transition-all duration-300 transform border-2 border-gray-300 rounded-2xl bg-gradient-to-r from-white to-gray-50 hover:border-gray-400 hover:from-gray-50 hover:to-gray-100 hover:scale-[1.02] hover:shadow-xl active:scale-[0.98] text-rose-600 hover:text-rose-700">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              <span class="text-lg">🔄 Anzisha Upya</span>
            </button>

            <!-- Back Button - Changed to uchambuzi page -->
            <a href="{{ route('uchambuzi.index') }}" class="flex-1 relative flex items-center justify-center gap-3 px-8 py-5 font-bold transition-all duration-300 transform border-2 border-gray-300 rounded-2xl bg-gradient-to-r from-white to-gray-50 hover:border-gray-400 hover:from-gray-50 hover:to-gray-100 hover:scale-[1.02] hover:shadow-xl active:scale-[0.98] text-blue-600 hover:text-blue-700">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
              </svg>
              <span class="text-lg">↩ Rudi Uchambuzi</span>
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const timePeriodSelect = document.getElementById('time_period');
    const customDatesDiv = document.getElementById('customDates');
    const dateFromInput = document.getElementById('dateFrom');
    const dateToInput = document.getElementById('dateTo');
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    const successMessage = document.getElementById('successMessage');
    const successText = document.getElementById('successText');
    const generateBtn = document.getElementById('generateBtn');
    const resetBtn = document.getElementById('resetBtn');
    const form = document.getElementById('reportForm');

    // Set default dates
    function setDefaultDates() {
        const today = new Date();
        const weekAgo = new Date();
        weekAgo.setDate(weekAgo.getDate() - 7);
        
        const todayStr = today.toISOString().split('T')[0];
        const weekAgoStr = weekAgo.toISOString().split('T')[0];
        
        dateFromInput.value = weekAgoStr;
        dateToInput.value = todayStr;
    }

    // Toggle custom dates visibility
    function toggleCustomDates() {
        if (timePeriodSelect.value === 'custom') {
            customDatesDiv.classList.remove('hidden');
            setDefaultDates();
        } else {
            customDatesDiv.classList.add('hidden');
        }
        hideError();
        hideSuccess();
    }

    // Validate dates
    function validateDates() {
        if (timePeriodSelect.value !== 'custom') {
            return true;
        }

        const from = new Date(dateFromInput.value);
        const to = new Date(dateToInput.value);
        const today = new Date();

        if (!dateFromInput.value || !dateToInput.value) {
            showError('Tafadhali chagua tarehe zote mbili');
            return false;
        }

        if (from > to) {
            showError('Tarehe ya kuanzia haiwezi kuwa baada ya tarehe ya mwisho');
            return false;
        }

        if (from > today || to > today) {
            showError('Tarehe haiwezi kuwa baada ya leo');
            return false;
        }

        hideError();
        return true;
    }

    // Show error message
    function showError(message) {
        errorText.textContent = message;
        errorMessage.classList.remove('hidden');
        errorMessage.classList.add('flex');
        hideSuccess();
    }

    // Hide error message
    function hideError() {
        errorMessage.classList.add('hidden');
        errorMessage.classList.remove('flex');
    }

    // Show success message
    function showSuccess(message) {
        successText.textContent = message;
        successMessage.classList.remove('hidden');
        successMessage.classList.add('flex');
        hideError();
    }

    // Hide success message
    function hideSuccess() {
        successMessage.classList.add('hidden');
        successMessage.classList.remove('flex');
    }

    // Generate report via form submission
    function generateReport() {
        if (!validateDates()) {
            return;
        }

        // Show success message
        showSuccess('Ripoti inatengenezwa, inapakua...');
        
        // Change button text
        const originalText = generateBtn.innerHTML;
        generateBtn.innerHTML = `
            <svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span class="text-lg">Inatengenezwa...</span>
        `;
        generateBtn.disabled = true;
        
        // Submit the form
        setTimeout(() => {
            form.submit();
            
            // Reset button after 5 seconds
            setTimeout(() => {
                generateBtn.innerHTML = originalText;
                generateBtn.disabled = false;
                hideSuccess();
            }, 5000);
        }, 1000);
    }

    // Reset form
    function resetForm() {
        form.reset();
        setDefaultDates();
        toggleCustomDates();
        hideError();
        hideSuccess();
        
        // Show notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-xl bg-blue-100 text-blue-800 border-l-4 border-blue-500 transform transition-all duration-300 translate-x-0';
        notification.innerHTML = `
            <div class="flex items-center">
                <span class="ml-3 font-medium">Fomu imeanzishwa upya.</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Event listeners
    timePeriodSelect.addEventListener('change', toggleCustomDates);
    
    // Handle generate button click
    generateBtn.addEventListener('click', function(e) {
        e.preventDefault();
        generateReport();
    });

    resetBtn.addEventListener('click', resetForm);

    // Initialize
    setDefaultDates();
    toggleCustomDates();
});
</script>

<style>
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>
@endpush