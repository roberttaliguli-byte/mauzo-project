@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-2xl font-bold text-emerald-700 mb-6">Badili Neno la Siri</h2>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update.auth') }}" class="space-y-6">
        @csrf

        <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">Neno la Siri la Sasa</label>
            <input type="password" 
                   name="current_password" 
                   required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">Neno la Siri Jipya</label>
            <input type="password" 
                   name="new_password" 
                   required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">Thibitisha Neno la Siri Jipya</label>
            <input type="password" 
                   name="new_password_confirmation" 
                   required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
        </div>

        <div class="flex justify-end">
            <button type="submit" 
                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
                Badili Neno la Siri
            </button>
        </div>
    </form>
</div>
@endsection