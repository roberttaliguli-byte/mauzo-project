{{-- resources/views/admin/sms/all-logs.blade.php --}}
@extends('layouts.admin')

@section('title', 'Historia Zote za SMS')
@section('page-title', 'Historia Zote za SMS')
@section('page-subtitle', 'Fuatilia na uchambue SMS zote zilizotumwa kwa makampuni')

@section('content')
<div class="container mx-auto px-3 sm:px-4 py-4 sm:py-6">
    
    <!-- Header with Back Button -->
    <div class="mb-5 sm:mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-history text-purple-600 text-lg sm:text-xl"></i>
                    <span>Historia Zote za SMS</span>
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Ripoti kamili ya SMS zote zilizotumwa kwenye mfumo</p>
            </div>
            <a href="{{ route('admin.sms.dashboard') }}" class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-800 transition text-sm">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Rudi kwenye Dashboard</span>
            </a>
        </div>
    </div>

    <!-- Summary Stats - Compact & Responsive -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-5 sm:mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-4 text-center hover:shadow-md transition">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Jumla</p>
            <p class="text-xl sm:text-2xl font-bold text-blue-600">{{ number_format($summary['total'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-4 text-center hover:shadow-md transition">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Leo</p>
            <p class="text-xl sm:text-2xl font-bold text-emerald-600">{{ number_format($summary['today'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-4 text-center hover:shadow-md transition">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Mwezi Huu</p>
            <p class="text-xl sm:text-2xl font-bold text-purple-600">{{ number_format($summary['month'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-4 text-center hover:shadow-md transition">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Zilizofanikiwa</p>
            <p class="text-xl sm:text-2xl font-bold text-emerald-600">{{ number_format($summary['delivered'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-4 text-center hover:shadow-md transition col-span-2 sm:col-span-1">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Zilizoshindwa</p>
            <p class="text-xl sm:text-2xl font-bold text-red-600">{{ number_format($summary['failed'] ?? 0) }}</p>
        </div>
    </div>

    <!-- Filter Form - Modern & Clean -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-5 mb-5 sm:mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
            <i class="fas fa-filter text-purple-500 text-xs"></i>
            <span>Chuja Rekodi</span>
        </h3>
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Kampuni</label>
                <select name="company_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white">
                    <option value="">Makampuni Yote</option>
                    @foreach($companies ?? [] as $comp)
                    <option value="{{ $comp->id }}" {{ request('company_id') == $comp->id ? 'selected' : '' }}>
                        {{ $comp->company_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tarehe ya Kuanza</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tarehe ya Mwisho</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Hali ya Ujumbe</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white">
                    <option value="">Zote</option>
                    <option value="DELIVERED" {{ request('status') == 'DELIVERED' ? 'selected' : '' }}>✓ Zilizofanikiwa</option>
                    <option value="FAILED" {{ request('status') == 'FAILED' ? 'selected' : '' }}>✗ Zilizoshindwa</option>
                    <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>⏳ Zinazosubiri</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-purple-700 text-white px-4 py-2 rounded-lg hover:from-purple-700 hover:to-purple-800 transition text-sm font-medium">
                    <i class="fas fa-search mr-2"></i> Chuja
                </button>
            </div>
        </form>
        
        <!-- Active Filters Display -->
        @if(request('company_id') || request('start_date') || request('end_date') || request('status'))
        <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap items-center gap-2">
            <span class="text-xs text-gray-500">Vichujio vya sasa:</span>
            @if(request('company_id'))
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-50 text-purple-700 text-xs rounded-full">
                    <i class="fas fa-building text-xs"></i> Kampuni
                    <a href="{{ request()->fullUrlWithQuery(['company_id' => null]) }}" class="ml-1 hover:text-purple-900">&times;</a>
                </span>
            @endif
            @if(request('start_date'))
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded-full">
                    <i class="fas fa-calendar text-xs"></i> Kuanza: {{ request('start_date') }}
                    <a href="{{ request()->fullUrlWithQuery(['start_date' => null]) }}" class="ml-1 hover:text-blue-900">&times;</a>
                </span>
            @endif
            @if(request('end_date'))
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded-full">
                    <i class="fas fa-calendar text-xs"></i> Mwisho: {{ request('end_date') }}
                    <a href="{{ request()->fullUrlWithQuery(['end_date' => null]) }}" class="ml-1 hover:text-blue-900">&times;</a>
                </span>
            @endif
            @if(request('status'))
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-50 text-green-700 text-xs rounded-full">
                    <i class="fas fa-check-circle text-xs"></i> {{ request('status') == 'DELIVERED' ? 'Zilizofanikiwa' : (request('status') == 'FAILED' ? 'Zilizoshindwa' : 'Zinazosubiri') }}
                    <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="ml-1 hover:text-green-900">&times;</a>
                </span>
            @endif
            <a href="{{ route('admin.sms.logs') }}" class="text-xs text-gray-500 hover:text-gray-700">
                <i class="fas fa-undo-alt mr-1"></i> Weka upya
            </a>
        </div>
        @endif
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kampuni</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Namba</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ujumbe</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Hali</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Tarehe</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse($logs ?? [] as $log)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-3 sm:px-6 py-3 sm:py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $log->company->company_name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500 sm:hidden mt-1">
                                {{ $log->recipient }}
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap hidden sm:table-cell">
                            <span class="text-sm font-mono text-gray-600">{{ $log->recipient }}</span>
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4">
                            <div class="text-sm text-gray-700 max-w-xs sm:max-w-md truncate" title="{{ $log->message }}">
                                {{ Str::limit($log->message, 50) }}
                            </div>
                            <div class="text-xs text-gray-400 md:hidden mt-1">
                                {{ $log->sent_at ? $log->sent_at->format('d/m/Y H:i') : '--' }}
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                            @php
                                $statusClass = match($log->status) {
                                    'DELIVERED' => 'bg-emerald-100 text-emerald-800',
                                    'FAILED', 'REJECTED' => 'bg-red-100 text-red-800',
                                    default => 'bg-yellow-100 text-yellow-800'
                                };
                                $statusIcon = match($log->status) {
                                    'DELIVERED' => '✓',
                                    'FAILED', 'REJECTED' => '✗',
                                    default => '⏳'
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                <span>{{ $statusIcon }}</span>
                                <span>{{ $log->status }}</span>
                            </span>
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap hidden md:table-cell">
                            <div class="text-sm text-gray-600">
                                {{ $log->sent_at ? $log->sent_at->format('d/m/Y H:i') : '--' }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-3 block opacity-30"></i>
                            <p class="text-sm">Hakuna SMS zilizopatikana</p>
                            <p class="text-xs mt-1">Jaribu kubadilisha vichujio au angalia tena baadaye</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(isset($logs) && method_exists($logs, 'links') && $logs->count() > 0)
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-t border-gray-100 bg-gray-50/30">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
    
    <!-- Export Option (if needed) -->
    @if(isset($logs) && $logs->count() > 0)
    <div class="mt-4 text-right">
        <button onclick="exportTable()" class="inline-flex items-center gap-2 px-3 py-1.5 text-xs text-gray-600 hover:text-gray-800 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
            <i class="fas fa-download text-xs"></i>
                            <span>Hamisha Data</span>
                        </button>
    </div>
    @endif
</div>

@push('scripts')
<script>
// Simple export function (you can enhance as needed)
function exportTable() {
    const table = document.querySelector('table');
    const rows = table.querySelectorAll('tr');
    let csv = [];
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('th, td');
        const rowData = Array.from(cols).map(col => {
            // Remove icons and extra elements for export
            let text = col.innerText.trim();
            text = text.replace(/[✓✗⏳]/g, '').trim();
            return `"${text}"`;
        }).join(',');
        csv.push(rowData);
    });
    
    const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `sms-logs-{{ date('Y-m-d') }}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
}

// Optional: Auto-refresh filters on enter key
document.querySelectorAll('input, select').forEach(el => {
    el.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            this.closest('form').submit();
        }
    });
});
</script>
@endpush
@endsection