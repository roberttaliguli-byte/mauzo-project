@extends('layouts.app')

@section('title', 'Shift Yangu')

@section('page-title', 'Shift Yangu')
@section('page-subtitle', 'Ripoti ya mauzo, matumizi na salio la shift zako')

@section('content')
    @include('uchambuzi.partials.vikao', [
        'showProfit' => $showProfit ?? false,
        'bossUsers' => $bossUsers ?? [],
        'employeeUsers' => $employeeUsers ?? [],
    ])
@endsection
