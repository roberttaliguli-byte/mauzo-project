<!-- Complete History Section -->
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="p-4 border-b border-gray-200 bg-amber-soft">
        <h2 class="text-sm font-bold flex items-center text-gray-800">
            <i class="fas fa-history text-amber-600 mr-2"></i>
            <span class="amber-label">Historia Kamili ya Shughuli</span>
        </h2>
        <p class="text-xs text-gray-700 mt-1 font-medium">Mauzo, Manunuzi, Matumizi, Marejesho na Kuingia Mfumo</p>
    </div>
    
    <div class="p-4">
        <!-- Filter Buttons -->
        <div class="flex flex-wrap gap-2 mb-4" id="filterButtons">
            <button onclick="filterActivities('all')" id="filterAllBtn" class="px-3 py-1.5 text-xs rounded-lg bg-amber-600 text-white font-semibold">
                <i class="fas fa-list mr-1"></i> Zote
            </button>
            <button onclick="filterActivities('sale')" id="filterSaleBtn" class="px-3 py-1.5 text-xs rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                <i class="fas fa-shopping-cart text-emerald-600 mr-1"></i> Mauzo
            </button>
            <button onclick="filterActivities('purchase')" id="filterPurchaseBtn" class="px-3 py-1.5 text-xs rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                <i class="fas fa-truck text-blue-600 mr-1"></i> Manunuzi
            </button>
            <button onclick="filterActivities('expense')" id="filterExpenseBtn" class="px-3 py-1.5 text-xs rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                <i class="fas fa-receipt text-red-600 mr-1"></i> Matumizi
            </button>
            <button onclick="filterActivities('repayment')" id="filterRepaymentBtn" class="px-3 py-1.5 text-xs rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                <i class="fas fa-hand-holding-usd text-purple-600 mr-1"></i> Marejesho
            </button>
            <button onclick="filterActivities('login')" id="filterLoginBtn" class="px-3 py-1.5 text-xs rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                <i class="fas fa-sign-in-alt text-amber-600 mr-1"></i> Kuingia
            </button>
        </div>
        
        <!-- Activities Table -->
        <div class="overflow-x-auto border border-gray-200 rounded-lg">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 border-b-2 border-gray-200">
                    <tr>
                        <th class="px-3 py-2 text-left">Aina</th>
                        <th class="px-3 py-2 text-left">Maelezo</th>
                        <th class="px-3 py-2 text-left hidden sm:table-cell">Mtumiaji</th>
                        <th class="px-3 py-2 text-right hidden md:table-cell">Kiasi (Tsh)</th>
                        <th class="px-3 py-2 text-left">Tarehe</th>
                    </tr>
                </thead>
                <tbody id="activitiesTable">
                    @php
                        // Combine and sort activities by created_at (newest first)
                        $allCombined = collect();
                        
                        // Add activities
                        foreach($recentActivities ?? [] as $activity) {
                            $allCombined->push([
                                'type' => 'activity',
                                'data' => $activity,
                                'sort_date' => $activity->created_at
                            ]);
                        }
                        
                        // Add login histories
                        foreach($loginHistories ?? [] as $login) {
                            $allCombined->push([
                                'type' => 'login',
                                'data' => $login,
                                'sort_date' => $login->logout_at ?? $login->login_at
                            ]);
                        }
                        
                        // Sort by date (newest first)
                        $allCombined = $allCombined->sortByDesc('sort_date');
                    @endphp
                    
                    @forelse($allCombined as $item)
                        @if($item['type'] == 'activity')
                            @php $activity = $item['data']; @endphp
                            <tr class="hover:bg-gray-50 border-b activity-row" data-type="{{ $activity->activity_type }}">
                                <td class="px-3 py-2">
                                    @if($activity->activity_type == 'sale')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                            <i class="fas fa-shopping-cart mr-1"></i> Mauzo
                                        </span>
                                    @elseif($activity->activity_type == 'purchase')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                            <i class="fas fa-truck mr-1"></i> Manunuzi
                                        </span>
                                    @elseif($activity->activity_type == 'expense')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            <i class="fas fa-receipt mr-1"></i> Matumizi
                                        </span>
                                    @elseif($activity->activity_type == 'repayment')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                                            <i class="fas fa-hand-holding-usd mr-1"></i> Marejesho
                                        </span>
                                    @elseif($activity->activity_type == 'login')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                            <i class="fas fa-sign-in-alt mr-1"></i> Kuingia
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                            <i class="fas fa-info-circle mr-1"></i> Nyingine
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">{{ $activity->description }}</td>
                                <td class="px-3 py-2 hidden sm:table-cell">
                                    <span class="text-xs">
                                        <i class="fas {{ $activity->user_role == 'Boss' ? 'fa-user-tie' : 'fa-user' }} mr-1"></i>
                                        {{ $activity->user_name }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-right hidden md:table-cell">
                                    @if($activity->amount)
                                        <span class="font-semibold {{ $activity->activity_type == 'expense' ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $activity->activity_type == 'expense' ? '-' : '+' }} Tsh {{ number_format($activity->amount, 0) }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-xs">{{ $activity->created_at ? $activity->created_at->format('d/m/Y H:i:s') : '-' }}</td>
                            </tr>
                        @else
                            @php $login = $item['data']; @endphp
                            <tr class="hover:bg-gray-50 border-b activity-row" data-type="login">
                                <td class="px-3 py-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                        <i class="fas fa-sign-in-alt mr-1"></i> Kuingia
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    {{ $login->user_name }} 
                                    @if($login->logout_at)
                                        alitoka mfumo
                                    @else
                                        aliingia mfumo
                                    @endif
                                </td>
                                <td class="px-3 py-2 hidden sm:table-cell">
                                    <span class="text-xs">
                                        <i class="fas {{ $login->user_role == 'Boss' ? 'fa-user-tie' : 'fa-user' }} mr-1"></i>
                                        {{ $login->user_name }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-right hidden md:table-cell">-</td>
                                <td class="px-3 py-2 text-xs">
                                    @if($login->logout_at)
                                        {{ $login->logout_at ? $login->logout_at->format('d/m/Y H:i:s') : '-' }}
                                    @else
                                        {{ $login->login_at ? $login->login_at->format('d/m/Y H:i:s') : '-' }}
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-6 text-center text-gray-500">Hakuna shughuli bado</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- View All Button -->
        <div class="mt-4 text-center">
            <button onclick="loadAllActivities()" class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-semibold hover:bg-amber-700 transition-all duration-200">
                <i class="fas fa-eye mr-2"></i> Tazama Shughuli Zote
            </button>
        </div>
    </div>
</div>

<!-- Modal for All Activities - Centered with proper positioning -->
<div id="allActivitiesModal" class="hidden fixed inset-0 z-50" style="display: none;">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeAllActivitiesModal()"></div>
    
    <!-- Modal Container - Centered -->
    <div class="relative min-h-screen flex items-start justify-center p-4">
        <div class="bg-white rounded-xl max-w-6xl w-full shadow-xl relative mt-8 mb-8 mx-auto">
            <!-- Close button at top right -->
            <button onclick="closeAllActivitiesModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 z-10 bg-white rounded-full w-8 h-8 flex items-center justify-center shadow-md">
                <i class="fas fa-times text-xl"></i>
            </button>
            
            <!-- Header -->
            <div class="p-4 border-b border-gray-200 bg-amber-soft rounded-t-xl pr-12">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-history mr-2"></i> Shughuli Zote za Mfumo
                </h3>
            </div>
            
            <!-- Content -->
            <div class="p-4 max-h-[70vh] overflow-y-auto">
                <!-- Filter Buttons -->
                <div class="flex flex-wrap gap-2 mb-4 sticky top-0 bg-white pb-2 z-10" id="modalFilterButtons">
                    <button onclick="filterModalActivities('all')" id="modalFilterAllBtn" class="px-3 py-1.5 text-xs rounded-lg bg-amber-600 text-white">Zote</button>
                    <button onclick="filterModalActivities('sale')" id="modalFilterSaleBtn" class="px-3 py-1.5 text-xs rounded-lg bg-gray-100 text-gray-700">Mauzo</button>
                    <button onclick="filterModalActivities('purchase')" id="modalFilterPurchaseBtn" class="px-3 py-1.5 text-xs rounded-lg bg-gray-100 text-gray-700">Manunuzi</button>
                    <button onclick="filterModalActivities('expense')" id="modalFilterExpenseBtn" class="px-3 py-1.5 text-xs rounded-lg bg-gray-100 text-gray-700">Matumizi</button>
                    <button onclick="filterModalActivities('repayment')" id="modalFilterRepaymentBtn" class="px-3 py-1.5 text-xs rounded-lg bg-gray-100 text-gray-700">Marejesho</button>
                    <button onclick="filterModalActivities('login')" id="modalFilterLoginBtn" class="px-3 py-1.5 text-xs rounded-lg bg-gray-100 text-gray-700">Kuingia</button>
                    <span id="modalCount" class="ml-auto text-xs text-gray-500 self-center"></span>
                </div>
                
                <!-- Table -->
                <div class="overflow-x-auto border rounded-lg">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 sticky top-0">
                            <tr>
                                <th class="px-3 py-2 text-left">Aina</th>
                                <th class="px-3 py-2 text-left">Maelezo</th>
                                <th class="px-3 py-2 text-left">Mtumiaji</th>
                                <th class="px-3 py-2 text-right">Kiasi (Tsh)</th>
                                <th class="px-3 py-2 text-left">Tarehe</th>
                            </tr>
                        </thead>
                        <tbody id="allActivitiesTable">
                            <tr><td colspan="5" class="text-center py-4">Inapakia...</td</tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Store all activities data for modal
let allActivitiesData = [];
let currentModalFilter = 'all';
let loginHistoriesData = @json($loginHistories ?? []);

// Function to filter activities in the main table
function filterActivities(type) {
    const rows = document.querySelectorAll('.activity-row');
    
    // Update button styles
    const buttons = ['all', 'sale', 'purchase', 'expense', 'repayment', 'login'];
    buttons.forEach(t => {
        const btn = document.getElementById(`filter${t.charAt(0).toUpperCase() + t.slice(1)}Btn`);
        if (btn) {
            if (t === type) {
                btn.className = 'px-3 py-1.5 text-xs rounded-lg bg-amber-600 text-white font-semibold';
            } else {
                btn.className = 'px-3 py-1.5 text-xs rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200';
            }
        }
    });
    
    // Filter rows
    rows.forEach(row => {
        if (type === 'all' || row.dataset.type === type) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Function to load all activities for modal
async function loadAllActivities() {
    const modal = document.getElementById('allActivitiesModal');
    const tableBody = document.getElementById('allActivitiesTable');
    
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-4"><i class="fas fa-spinner fa-spin mr-2"></i>Inapakia...</td></tr>';
    
    try {
        const response = await fetch('{{ route("uchambuzi.all-activities") }}');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const activityData = await response.json();
        
        // Combine activities and login histories
        allActivitiesData = [...activityData];
        
        // Add login histories to the data
        loginHistoriesData.forEach(login => {
            let description = '';
            if (login.logout_at) {
                description = `${login.user_name} alitoka mfumo`;
            } else {
                description = `${login.user_name} aliingia mfumo`;
            }
            
            let loginDate = login.logout_at ? login.logout_at : login.login_at;
            // Format date to string for sorting consistency
            let formattedDate = loginDate ? new Date(loginDate).toISOString() : new Date().toISOString();
            
            allActivitiesData.push({
                activity_type: 'login',
                description: description,
                user_name: login.user_name,
                user_role: login.user_role,
                amount: null,
                created_at: formattedDate,
                raw_date: loginDate
            });
        });
        
        // Sort by created_at descending (newest first)
        allActivitiesData.sort((a, b) => {
            const dateA = new Date(a.created_at);
            const dateB = new Date(b.created_at);
            return dateB - dateA;
        });
        
        filterModalActivities('all');
    } catch (error) {
        console.error('Error loading activities:', error);
        tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-red-500">Hitilafu katika kupakia data. Tafadhali jaribu tena.</td></tr>';
    }
}

// Function to filter activities in modal
function filterModalActivities(type) {
    currentModalFilter = type;
    
    // Update button styles in modal
    const buttons = ['all', 'sale', 'purchase', 'expense', 'repayment', 'login'];
    buttons.forEach(t => {
        const btn = document.getElementById(`modalFilter${t.charAt(0).toUpperCase() + t.slice(1)}Btn`);
        if (btn) {
            if (t === type) {
                btn.className = 'px-3 py-1.5 text-xs rounded-lg bg-amber-600 text-white';
            } else {
                btn.className = 'px-3 py-1.5 text-xs rounded-lg bg-gray-100 text-gray-700';
            }
        }
    });
    
    let filtered = allActivitiesData;
    if (type !== 'all') {
        filtered = allActivitiesData.filter(a => a.activity_type === type);
    }
    
    // Sort filtered results by created_at descending (newest first)
    filtered.sort((a, b) => {
        const dateA = new Date(a.created_at);
        const dateB = new Date(b.created_at);
        return dateB - dateA;
    });
    
    const countSpan = document.getElementById('modalCount');
    if (countSpan) {
        countSpan.innerText = `Jumla: ${filtered.length}`;
    }
    
    const tableBody = document.getElementById('allActivitiesTable');
    
    if (filtered.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-gray-500">Hakuna shughuli za aina hii</td></tr>';
        return;
    }
    
    let html = '';
    filtered.forEach(activity => {
        let badgeClass = '', icon = '', badgeText = '';
        
        switch(activity.activity_type) {
            case 'sale':
                badgeClass = 'bg-emerald-100 text-emerald-700';
                icon = 'fa-shopping-cart';
                badgeText = 'Mauzo';
                break;
            case 'purchase':
                badgeClass = 'bg-blue-100 text-blue-700';
                icon = 'fa-truck';
                badgeText = 'Manunuzi';
                break;
            case 'expense':
                badgeClass = 'bg-red-100 text-red-700';
                icon = 'fa-receipt';
                badgeText = 'Matumizi';
                break;
            case 'repayment':
                badgeClass = 'bg-purple-100 text-purple-700';
                icon = 'fa-hand-holding-usd';
                badgeText = 'Marejesho';
                break;
            case 'login':
                badgeClass = 'bg-amber-100 text-amber-700';
                icon = 'fa-sign-in-alt';
                badgeText = 'Kuingia';
                break;
            default:
                badgeClass = 'bg-gray-100 text-gray-700';
                icon = 'fa-info-circle';
                badgeText = 'Nyingine';
        }
        
        const amountDisplay = activity.amount 
            ? `<span class="font-semibold ${activity.activity_type === 'expense' ? 'text-red-600' : 'text-green-600'}">
                ${activity.activity_type === 'expense' ? '-' : '+'} Tsh ${Number(activity.amount).toLocaleString()}
               </span>`
            : '-';
        
        // Format date properly
        let formattedDate = '-';
        if (activity.created_at) {
            try {
                const date = new Date(activity.created_at);
                if (!isNaN(date.getTime())) {
                    formattedDate = date.toLocaleString('sw-TZ', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });
                }
            } catch(e) {
                formattedDate = activity.created_at;
            }
        }
        
        html += `
            <tr class="hover:bg-gray-50 border-b">
                <td class="px-3 py-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold ${badgeClass}">
                        <i class="fas ${icon} mr-1"></i> ${badgeText}
                    </span>
                </td>
                <td class="px-3 py-2">${escapeHtml(activity.description)}</td>
                <td class="px-3 py-2">
                    <span class="text-xs">
                        <i class="fas ${activity.user_role === 'Boss' ? 'fa-user-tie' : 'fa-user'} mr-1"></i>
                        ${escapeHtml(activity.user_name)}
                    </span>
                </td>
                <td class="px-3 py-2 text-right">${amountDisplay}</td>
                <td class="px-3 py-2 text-xs">${formattedDate}</td>
             </tr>
        `;
    });
    tableBody.innerHTML = html;
}

// Helper function to escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Function to close modal
function closeAllActivitiesModal() {
    const modal = document.getElementById('allActivitiesModal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAllActivitiesModal();
    }
});

// Initial filter setup
document.addEventListener('DOMContentLoaded', function() {
    filterActivities('all');
});
</script>