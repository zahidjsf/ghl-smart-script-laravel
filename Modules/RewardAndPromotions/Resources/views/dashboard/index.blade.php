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
        <header style="padding:0 15px 0 15px; width:90%; margin:35px auto;">
            <h2 style="font-size:30px; font-weight:500;">Dashboard</h2>
            <div class="description" style="font-size:16px; font-weight:400;">View and manage all referral partners.</div>
        </header>


    </div>
</div>

@endsection
