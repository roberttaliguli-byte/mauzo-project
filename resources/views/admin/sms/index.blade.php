{{-- resources/views/admin/sms/index.blade.php --}}
@extends('layouts.admin')
@section('page-title', 'Usimamizi wa SMS')
@section('page-subtitle', 'Fuatilia na usimamie SMS za makampuni yote')

@section('content')
<div class="container mx-auto px-3 sm:px-4 py-4 sm:py-6">
    <!-- Stats Cards - Same structure but compact on mobile -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 mb-5 sm:mb-6">
        <div class="bg-white rounded-lg shadow p-3 sm:p-4 border-l-4 border-blue-500">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Jumla ya SMS</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ number_format($totalSmsAll ?? 0) }}</p>
                </div>
                <i class="fas fa-envelope text-blue-400 text-lg sm:text-2xl"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-3 sm:p-4 border-l-4 border-green-500">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">SMS za Leo</p>
                    <p class="text-xl sm:text-2xl font-bold text-green-600">{{ number_format($totalSmsToday ?? 0) }}</p>
                </div>
                <i class="fas fa-calendar-day text-green-400 text-lg sm:text-2xl"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-3 sm:p-4 border-l-4 border-purple-500">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">SMS za Wiki</p>
                    <p class="text-xl sm:text-2xl font-bold text-purple-600">{{ number_format($totalSmsWeek ?? 0) }}</p>
                </div>
                <i class="fas fa-calendar-week text-purple-400 text-lg sm:text-2xl"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-3 sm:p-4 border-l-4 border-orange-500">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">SMS za Mwezi</p>
                    <p class="text-xl sm:text-2xl font-bold text-orange-600">{{ number_format($totalSmsMonth ?? 0) }}</p>
                </div>
                <i class="fas fa-calendar-alt text-orange-400 text-lg sm:text-2xl"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-3 sm:p-4 border-l-4 border-teal-500">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Makampuni Yanayotumia</p>
                    <p class="text-xl sm:text-2xl font-bold text-teal-600">{{ number_format($activeCompanies ?? 0) }}</p>
                </div>
                <i class="fas fa-building text-teal-400 text-lg sm:text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Action Buttons + Search -->
    <div class="bg-white rounded-lg shadow p-3 sm:p-4 mb-5 sm:mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.sms.bulk') }}" class="px-3 sm:px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition inline-flex items-center gap-2">
                    <i class="fas fa-paper-plane text-xs"></i> <span>Tuma SMS kwa Makampuni Mengi</span>
                </a>
                <a href="{{ route('admin.sms.logs') }}" class="px-3 sm:px-4 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition inline-flex items-center gap-2">
                    <i class="fas fa-history text-xs"></i> <span>Angalia Historia Zote</span>
                </a>
            </div>
            
            <!-- Search Input -->
            <div class="relative w-full sm:w-64">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="searchCompany" placeholder="Tafuta kampuni..." class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
        </div>
    </div>

    <!-- Companies Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-base sm:text-lg font-semibold text-gray-800">Makampuni na Takwimu za SMS</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kampuni</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Mwenye</th>
                        <th class="px-2 sm:px-6 py-2 sm:py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jumla</th>
                        <th class="px-2 sm:px-6 py-2 sm:py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Leo</th>
                        <th class="px-2 sm:px-6 py-2 sm:py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Wiki</th>
                        <th class="px-2 sm:px-6 py-2 sm:py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Mwezi</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Vitendo</th>
                    </tr>
                </thead>
                <tbody id="companiesTableBody" class="bg-white divide-y divide-gray-200">
                    @forelse($companies ?? [] as $company)
                    <tr class="hover:bg-gray-50 transition company-row" data-name="{{ strtolower($company->company_name) }}" data-owner="{{ strtolower($company->owner_name ?? '') }}">
                        <td class="px-3 sm:px-6 py-3 sm:py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $company->company_name }}</div>
                            <div class="text-xs text-gray-500 sm:hidden mt-1">
                                @if($company->owner_name){{ $company->owner_name }}@endif
                                @if($company->phone) | {{ $company->phone }}@endif
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap hidden sm:table-cell">
                            <div class="text-sm text-gray-900">{{ $company->owner_name ?? '--' }}</div>
                            <div class="text-xs text-gray-500">{{ $company->phone ?? '--' }}</div>
                        </td>
                        <td class="px-2 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-center">
                            <span class="text-sm font-semibold text-blue-600">{{ number_format($company->total_sms ?? 0) }}</span>
                        </td>
                        <td class="px-2 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-center">
                            <span class="text-sm text-green-600">{{ number_format($company->today_sms ?? 0) }}</span>
                        </td>
                        <td class="px-2 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-center hidden md:table-cell">
                            <span class="text-sm text-purple-600">{{ number_format($company->week_sms ?? 0) }}</span>
                        </td>
                        <td class="px-2 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-center hidden lg:table-cell">
                            <span class="text-sm text-orange-600">{{ number_format($company->month_sms ?? 0) }}</span>
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('admin.sms.send-to-company', $company->id) }}" 
                                   class="text-purple-600 hover:text-purple-900 p-1 inline-block" title="Tuma SMS">
                                    <i class="fas fa-envelope"></i>
                                </a>
                                <a href="{{ route('admin.sms.company-report', $company->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-1 inline-block" title="Ripoti">
                                    <i class="fas fa-chart-bar"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-building text-4xl mb-2 text-gray-300"></i>
                            <p>Hakuna makampuni yaliyopatikana</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(isset($companies) && method_exists($companies, 'links'))
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-t border-gray-200">
            {{ $companies->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Live search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchCompany');
    const tableRows = document.querySelectorAll('#companiesTableBody .company-row');
    
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            tableRows.forEach(row => {
                const companyName = row.getAttribute('data-name') || '';
                const ownerName = row.getAttribute('data-owner') || '';
                
                if (companyName.includes(searchTerm) || ownerName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});

// Optional: Auto refresh every 30 seconds (you can keep or remove)
let autoRefresh = setInterval(function() {
    if (!document.hidden) {
        // location.reload(); // Uncomment if you want auto-refresh
    }
}, 30000);

document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        clearInterval(autoRefresh);
    } else {
        autoRefresh = setInterval(function() {
            // location.reload(); // Uncomment if you want auto-refresh
        }, 30000);
    }
});
</script>
@endpush