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
            <h2 style="font-size:30px; font-weight:500;">Leaderboard - Referrals</h2>
            <div class="description" style="font-size:16px; font-weight:400;">View and manage all referral partners.</div>
        </header>

        <section style="padding-left: 15px;padding-right: 15px;">
            <div class="row card" style="width:90%; margin:0 auto;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Name</th>
                                <th>Leads</th>
                                <th>Referrals<br /><small>(serviced)</small></th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaderBoard as $entry)
                            <tr>
                                <td>{{ $entry['pos'] }}</td>
                                <td>{{ $entry['name'] }}</td>
                                <td>
                                    <a class="link-primary" href="/ReviewStats/leaderboardContacts.php?type=1&contact={{ $entry['id'] }}&location={{ $locationId }}">
                                        {{ $entry['leads'] }}
                                    </a>
                                </td>
                                <td>
                                    <a class="link-primary" href="/ReviewStats/leaderboardContacts.php?type=2&contact={{ $entry['id'] }}&location={{ $locationId }}">
                                        {{ $entry['referrals'] }}
                                    </a>
                                </td>
                                <td>
                                    <a type="button" class="btn btn-primary" href="{{ $parentURL }}/v2/location/{{ $locationId }}/contacts/detail/{{ $entry['id'] }}" target="_blank">
                                        View Contact
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

@endsection
