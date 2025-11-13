@extends('layouts.admin')

@section('title', 'Orodha ya Makampuni')
@section('page-title', 'Orodha ya Makampuni')
@section('page-subtitle', 'Makampuni yote yaliyosajiliwa ndani ya mfumo')

@section('content')
<div class="space-y-6">

    <!-- Success Alert - Pop-up at center-top -->
    @if(session('success'))
    <div id="success-notification" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 animate-slide-down">
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 px-6 py-4 rounded-lg shadow-lg max-w-md">
            <div class="flex items-center">
                <span class="text-2xl mr-3">✅</span>
                <div>
                    <p class="font-semibold">Mafanikio!</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
                <button onclick="closeNotification()" class="ml-4 text-emerald-600 hover:text-emerald-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
    @endif
<!-- Companies Table -->
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-emerald-700">
            📋 Orodha ya Makampuni Yaliyosajiliwa
        </h3>
        <span class="text-sm text-gray-500">
            Jumla: <strong>{{ $total }}</strong>
        </span>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse">
            <thead class="bg-emerald-700 text-white">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold">No.</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Kampuni</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Mmiliki</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Kifurushi</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Database</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Db Ipo?</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Usajili</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold">Hali</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold">Kitendo</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold">Taarifa</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold">Futa</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @foreach ($companies as $index => $company)
                <tr class="hover:bg-emerald-50 transition">
                    <!-- No -->
                    <td class="px-4 py-2 text-sm text-gray-700">{{ $index + 1 }}</td>

                    <!-- Company -->
                    <td class="px-4 py-2 font-semibold text-gray-800">
                        {{ $company->company_name }}
                    </td>

                    <!-- Owner -->
                    <td class="px-4 py-2 text-gray-700">
                        {{ $company->owner_name }}
                    </td>

                    <!-- Package -->
                    <td class="px-4 py-2 text-center">
                        <span class="inline-block px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700 font-medium">
                            {{ $company->package ?? 'Hakuna' }}
                        </span>
                    </td>

                    <!-- Database name -->
                    <td class="px-4 py-2 text-gray-600">
                        {{ $company->database_name ?? '-' }}
                    </td>

                    <!-- Database exists -->
                    <td class="px-4 py-2 text-center">
                        @if($company->database_name)
                            <span class="inline-block px-2 py-1 text-xs rounded-full bg-emerald-200 text-emerald-800 font-semibold">
                                Ndiyo
                            </span>
                        @else
                            <span class="inline-block px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-800 font-semibold">
                                Hapana
                            </span>
                        @endif
                    </td>

                    <!-- Registration date -->
                    <td class="px-4 py-2 text-gray-600">
                        {{ $company->created_at->format('d M, Y') }}
                    </td>

                    <!-- Status -->
                    <td class="px-4 py-2 text-center">
                        <span class="inline-block px-2 py-1 text-xs rounded-full 
                            {{ $company->is_verified ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-700' }} 
                            font-semibold">
                            {{ $company->is_verified ? 'Kampuni Imeidhinishwa' : 'Haijathibitishwa' }}
                        </span>
                        <br>
                        <span class="inline-block px-2 py-1 text-xs rounded-full 
                            {{ $company->is_user_approved ? 'bg-emerald-100 text-emerald-800' : 'bg-yellow-100 text-yellow-800' }} 
                            font-semibold">
                            {{ $company->is_user_approved ? 'Mtumiaji Ameidhinishwa' : 'Mtumiaji Hajaidhinishwa' }}
                        </span>
                    </td>

                    <!-- Approve button -->
                    <td class="px-4 py-2 text-center">
                        @if(!$company->is_user_approved)
                    <form action="{{ route('admin.approveUser', $company->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-3 py-1 rounded-md">
                            <i class="fas fa-user-check mr-1"></i> Idhinisha
                        </button>
                    </form>

                        @else
                            <span class="inline-block px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full font-semibold">
                                👤 Ameidhinishwa
                            </span>
                        @endif
                    </td>

                    <!-- Info button -->
                    <td class="px-4 py-2 text-center">
                        <button
                            class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-3 py-1 rounded-md info-btn transition"
                            data-target="modal-{{ $company->id }}">
                            <i class="fas fa-info-circle mr-1"></i> Taarifa
                        </button>
                    </td>

                    <!-- Delete button -->
                    <td class="px-4 py-2 text-center">
                        <form action="{{ route('admin.destroy', $company->id) }}" method="POST" class="delete-form inline">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                class="bg-red-500 hover:bg-red-600 text-white text-sm px-3 py-1 rounded-md delete-btn transition"
                                data-company="{{ $company->company_name }}">
                                <i class="fas fa-trash mr-1"></i> Futa
                            </button>
                        </form>
                    </td>
                </tr>

                <!-- Info Modal -->
                <div id="modal-{{ $company->id }}" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50">
                    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl overflow-hidden animate-scale-in">
                        <div class="bg-emerald-700 text-white px-4 py-3 flex justify-between items-center">
                            <h5 class="font-semibold">Taarifa za {{ $company->company_name }}</h5>
                            <button class="text-white hover:text-gray-200 close-btn" data-target="modal-{{ $company->id }}">✖</button>
                        </div>

                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                            <div>
                                <p><strong>Kampuni:</strong> {{ $company->company_name }}</p>
                                <p><strong>Mmiliki:</strong> {{ $company->owner_name }}</p>
                                <p><strong>Simu:</strong> {{ $company->phone }}</p>
                                <p><strong>Email:</strong> {{ $company->email }}</p>
                                <p><strong>Region:</strong> {{ $company->region }}</p>
                            </div>
                            <div>
                                <p><strong>Kifurushi:</strong> {{ $company->package ?? '-' }}</p>
                                <p><strong>Database:</strong> {{ $company->database_name ?? '-' }}</p>
                                <p><strong>Usajili:</strong> {{ $company->created_at->format('d/m/Y H:i') }}</p>
                                <p><strong>Status:</strong>
                                    @if($company->is_verified)
                                        <span class="inline-block px-2 py-1 text-xs bg-emerald-100 text-emerald-800 rounded-full font-semibold">Imethibitishwa</span>
                                    @else
                                        <span class="inline-block px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full font-semibold">Haijathibitishwa</span>
                                    @endif
                                </p>
                                <p><strong>Mtumiaji:</strong> 
                                @if($company->is_user_approved)
                                    <span class="text-green-700 font-semibold">Ameidhinishwa</span>
                                @else
                                    <span class="text-red-600 font-semibold">Hajaidhinishwa</span>
                                @endif
                            </p>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 text-right">
                            <button
                                class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 rounded-md close-btn"
                                data-target="modal-{{ $company->id }}">
                                Funga
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden animate-scale-in">
        <div class="bg-red-600 text-white px-4 py-3">
            <h5 class="font-semibold flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Thibitisha Ufutaji
            </h5>
        </div>

        <div class="p-6">
            <p class="text-gray-700 mb-4">Una uhakika unataka kufuta kampuni:</p>
            <p class="font-bold text-lg text-gray-900 mb-4" id="delete-company-name"></p>
            <p class="text-sm text-red-600">⚠️ Hatua hii haiwezi kurudishwa!</p>
        </div>

        <div class="bg-gray-50 px-4 py-3 flex justify-end gap-2">
            <button
                id="cancel-delete"
                class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 rounded-md transition">
                Ghairi
            </button>
            <button
                id="confirm-delete"
                class="px-4 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-md transition">
                <i class="fas fa-trash mr-1"></i> Futa
            </button>
        </div>
    </div>
</div>

<!-- Styles for animations -->
<style>
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translate(-50%, -100%);
    }
    to {
        opacity: 1;
        transform: translate(-50%, 0);
    }
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-slide-down {
    animation: slideDown 0.3s ease-out;
}

.animate-scale-in {
    animation: scaleIn 0.2s ease-out;
}
</style>

<!-- JavaScript for Modals and Delete Confirmation -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Auto-hide success notification after 5 seconds
    const notification = document.getElementById('success-notification');
    if (notification) {
        setTimeout(() => {
            closeNotification();
        }, 5000);
    }

    // Open info modal
    document.querySelectorAll('.info-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.getAttribute('data-target');
            document.getElementById(target).classList.remove('hidden');
            document.getElementById(target).classList.add('flex');
        });
    });

    // Close info modal
    document.querySelectorAll('.close-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.getAttribute('data-target');
            document.getElementById(target).classList.add('hidden');
            document.getElementById(target).classList.remove('flex');
        });
    });

    // Delete confirmation
    let deleteForm = null;
    
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const companyName = btn.getAttribute('data-company');
            deleteForm = btn.closest('.delete-form');
            
            document.getElementById('delete-company-name').textContent = companyName;
            document.getElementById('delete-modal').classList.remove('hidden');
            document.getElementById('delete-modal').classList.add('flex');
        });
    });

    // Confirm delete
    document.getElementById('confirm-delete').addEventListener('click', () => {
        if (deleteForm) {
            deleteForm.submit();
        }
    });

    // Cancel delete
    document.getElementById('cancel-delete').addEventListener('click', () => {
        closeDeleteModal();
    });

    // Close delete modal on backdrop click
    document.getElementById('delete-modal').addEventListener('click', (e) => {
        if (e.target.id === 'delete-modal') {
            closeDeleteModal();
        }
    });

    // Close info modals on backdrop click
    document.querySelectorAll('[id^="modal-"]').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        });
    });
});

function closeNotification() {
    const notification = document.getElementById('success-notification');
    if (notification) {
        notification.style.animation = 'slideDown 0.3s ease-out reverse';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
    document.getElementById('delete-modal').classList.remove('flex');
    deleteForm = null;
}
</script>
@endsection