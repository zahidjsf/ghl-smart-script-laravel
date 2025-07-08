@extends('rewardandpromotions::layout.app')

@section('content')
@section('select_dashboard', 'active')

<link rel="stylesheet" href="{{ asset('frontpanel/assets/css/dashboard.css') }}">

<div class="container-fluid dashboard-container">

    @if(session('success'))
    <div class="alert alert-success alert-dashboard">
        {{ session('success') }}
    </div>
    @endif

    <div class="row dashboard-content">
        dashboard
    </div>
    
</div>

@endsection
