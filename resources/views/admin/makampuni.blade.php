@extends('layouts.admin')

@section('title', 'Makampuni - MauzoSheet Admin')

@section('content')
<div class="space-y-8">

    <!-- Notification -->
    @if(session('success'))
        <div id="notification" class="fixed top-6 left-1/2 transform -translate-x-1/2 bg-emerald-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('success') }}
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const note = document.getElementById('notification');
            if (note) setTimeout(() => note.remove(), 2000);

            // Confirm delete
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (confirm('Je, una uhakika unataka kufuta kampuni hii?')) {
                        e.target.closest('form').submit();
                    }
                });
            });
        });
    </script>

    <!-- Active Companies -->
    <section>
        <h2 class="text-2xl font-bold text-emerald-700 mb-4">Kampuni Zilizopo</h2>
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-emerald-600 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">Jina la Kampuni</th>
                        <th class="px-4 py-3 text-left">Namba</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-center">Vitendo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $company->name }}</td>
                            <td class="px-4 py-2">{{ $company->phone }}</td>
                            <td class="px-4 py-2 text-green-600 font-semibold">Hai</td>
                            <td class="px-4 py-2 flex justify-center gap-2">
                                <a href="{{ route('admin.kampuni.view', $company->id) }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.kampuni.delete', $company->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="delete-btn text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-3 text-gray-500">Hakuna kampuni zilizopo</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- Deleted Companies -->
    <section>
        <h2 class="text-2xl font-bold text-red-600 mb-4">Kampuni Zilizofutwa</h2>
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-red-600 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">Jina la Kampuni</th>
                        <th class="px-4 py-3 text-left">Sababu ya Kufutwa</th>
                        <th class="px-4 py-3 text-center">Tarehe</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deletedCompanies as $company)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $company->name }}</td>
                            <td class="px-4 py-2">{{ $company->reason ?? 'Haijawekwa' }}</td>
                            <td class="px-4 py-2 text-center">{{ $company->deleted_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center py-3 text-gray-500">Hakuna kampuni zilizofutwa</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

</div>
@endsection
