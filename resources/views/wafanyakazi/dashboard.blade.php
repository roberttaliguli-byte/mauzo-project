@extends('layouts.app')

@section('title','Mfanyakazi Dashboard')

@section('content')
  <div class="p-6">
    <h1 class="text-xl font-bold">Karibu, {{ auth()->user()->name }}</h1>
    <p class="text-sm text-gray-500">Tazama mauzo na matumizi yako hapa.</p>

    {{-- Example quick cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
      <div class="p-4 bg-white rounded shadow">Mauzo Leo: TZS 0</div>
      <div class="p-4 bg-white rounded shadow">Matumizi Leo: TZS 0</div>
    </div>
  </div>
@endsection
