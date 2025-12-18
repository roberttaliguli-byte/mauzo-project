@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
            <header class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Chagua Kifurushi</h1>
                <p class="text-gray-600 mt-2">Chagua kifurushi kinachokufaa kulingana na mahitaji yako</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($packages as $pkg)
                    <article class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
                        <div class="mb-4">
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ $pkg['label'] }}</h2>
                            <div class="flex items-baseline">
                                <span class="text-2xl font-bold text-gray-900">Tsh {{ number_format($pkg['price']) }}</span>
                                <span class="text-gray-500 text-sm ml-2">/ kipindi</span>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('package.pay') }}" class="mt-6">
                            @csrf
                            <input type="hidden" name="package" value="{{ $pkg['id'] }}">
                            <button type="submit" 
                                    class="w-full px-5 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                Lipa Tsh {{ number_format($pkg['price']) }}
                            </button>
                        </form>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection