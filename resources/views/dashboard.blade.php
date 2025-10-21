@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="flex flex-col p-8">

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <!-- Bidhaa Card -->
        <div class="bg-white rounded-lg shadow p-6 flex items-center space-x-4">
            <div class="bg-blue-500 text-white rounded-full p-4 text-2xl flex items-center justify-center">📦</div>
            <div>
                <p class="text-gray-500 font-medium">Bidhaa</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalBidhaa ?? 0 }}</p>
            </div>
        </div>

        <!-- Wafanyakazi Card -->
        <div class="bg-white rounded-lg shadow p-6 flex items-center space-x-4">
            <div class="bg-green-500 text-white rounded-full p-4 text-2xl flex items-center justify-center">👔</div>
            <div>
                <p class="text-gray-500 font-medium">Wafanyakazi</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalWafanyakazi ?? 0 }}</p>
            </div>
        </div>

        <!-- Masaplaya Card -->
        <div class="bg-white rounded-lg shadow p-6 flex items-center space-x-4">
            <div class="bg-yellow-500 text-white rounded-full p-4 text-2xl flex items-center justify-center">🏆</div>
            <div>
                <p class="text-gray-500 font-medium">Masaplaya</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalMasaplaya ?? 0 }}</p>
            </div>
        </div>

        <!-- Wateja Card -->
        <div class="bg-white rounded-lg shadow p-6 flex items-center space-x-4">
            <div class="bg-purple-500 text-white rounded-full p-4 text-2xl flex items-center justify-center">👥</div>
            <div>
                <p class="text-gray-500 font-medium">Wateja</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalWateja ?? 0 }}</p>
            </div>
        </div>

    </div>

    <!-- Main Home Message -->
    <div class="bg-white rounded-lg shadow p-6">
        <p>Hii ni ukurasa wa kwanza (home page). Chagua module kutoka kwenye sidebar ili kuendelea.</p>
    </div>

</div>
@endsection
