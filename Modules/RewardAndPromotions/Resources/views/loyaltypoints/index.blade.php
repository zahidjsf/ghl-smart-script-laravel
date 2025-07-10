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
        <h1>Loyalty {{ $pointsName }} Leaders - {{ $locationName }}</h1>

        <form method="GET" action="{{ route('rewards.point-leaders', ['location' => $locationId]) }}">
            <div class="search-box">
                <input type="text" name="Search" value="{{ $search }}" placeholder="Search by name, phone or ID">
                <button type="submit">Search</button>
            </div>
        </form>

        @if($showMessage)
        <div class="alert-message" style="background-color: {{ $msgBgColor }};">
            {{ $message }}
        </div>
        @endif

        <div class="leaderboard-container">
            <table class="leaderboard-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Total {{ $pointsName }}</th>
                        <th>Available {{ $pointsName }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaderBoard as $leader)
                    <tr>
                        <td>{{ $leader['pos'] }}</td>
                        <td>{{ $leader['name'] }}</td>
                        <td>{{ $leader['phone'] }}</td>
                        <td>{{ $leader['email'] }}</td>
                        <td>{{ $leader['points'] }}</td>
                        <td>{{ $leader['available'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $leaderBoard->links() }}
        </div>
    </div>
</div>

<style>
    .leaderboard-table {
        width: 100%;
        border-collapse: collapse;
    }
    .leaderboard-table th, .leaderboard-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    .leaderboard-table tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    .search-box {
        margin: 20px 0;
    }
    .alert-message {
        padding: 10px;
        margin: 10px 0;
        border-radius: 4px;
    }
</style>

@endsection
