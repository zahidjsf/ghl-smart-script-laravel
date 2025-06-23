@extends('frontpanel.layout.app')

@section('content')
@section('select_account_setting', 'active')

<style>
    .detail-row {
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #eee;
    }

    .detail-label {
        font-weight: 600;
        color: #555;
    }

    .detail-value {
        font-family: monospace;
        word-break: break-all;
    }

    .card-title {
        color: #333;
        font-weight: 600;
    }
</style>


<div class="container-fluid" style="padding:0 1.5rem">
    <!-- start page title -->
    <div class="row g-0">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h2><strong>{{ __('messages.profile_setting') }}</strong></h2>
            </div>
        </div>
    </div>
    <hr>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">{{ __('messages.account_details') }}</h5>

            <div class="account-details">
                <div class="row detail-row">
                    <div class="col-md-3 detail-label">{{ __('messages.account_id') }}</div>
                    <div class="col-md-9 detail-value">{{ $authUser->id }}</div>
                </div>

                <div class="row detail-row">
                    <div class="col-md-3 detail-label">{{ __('messages.first_name') }}</div>
                    <div class="col-md-9 detail-value">{{ __('messages.account_id') }} {{ $authUser->fName }}</div>
                </div>

                <div class="row detail-row">
                    <div class="col-md-3 detail-label">{{ __('messages.last_name') }}</div>
                    <div class="col-md-9 detail-value">{{ $authUser->lName }}</div>
                </div>

                <div class="row detail-row">
                    <div class="col-md-3 detail-label">{{ __('messages.user_name') }}</div>
                    <div class="col-md-9 detail-value">{{ $authUser->username }}</div>
                </div>

                <div class="row detail-row">
                    <div class="col-md-3 detail-label">{{ __('messages.email') }}</div>
                    <div class="col-md-9 detail-value">{{ $authUser->email }}</div>
                </div>

                <div class="row detail-row">
                    <div class="col-md-3 detail-label">{{ __('messages.agency_url') }} </div>
                    <div class="col-md-9 detail-value">{{ $authUser->fName }}</div>
                </div>

                <div class="row detail-row">
                    <div class="col-md-3 detail-label">{{ __('messages.role') }}</div>
                    <div class="col-md-9 detail-value">{{ $authUser->role }}</div>
                </div>

                <div class="row detail-row">
                    <div class="col-md-3 detail-label">{{ __('messages.agency_type') }}</div>
                    <div class="col-md-9 detail-value">{{ $authUser->account_type }}</div>
                </div>

                <div class="row detail-row">
                    <div class="col-md-3 detail-label">{{ __('messages.api_key') }}</div>
                    <div class="col-md-9 detail-value text-muted">{{ $authUser->apikey }}</div>
                </div>

                <div class="row detail-row">
                    <div class="col-md-3 detail-label">{{ __('messages.license_key') }}</div>
                    <div class="col-md-9 detail-value">{{ $authUser->licensekey }}</div>
                </div>

                <?php
                    if($authUser->locale == 'en'){
                        $langaugeSelected = 'English';
                    }elseif($authUser->locale == 'es'){
                        $langaugeSelected = 'Spanish';
                    }else{
                        $langaugeSelected = 'French';
                    }
                ?>
                <div class="row detail-row">
                    <div class="col-md-3 detail-label">{{ __('messages.language') }} </div>
                    <div class="col-md-9 detail-value">{{ $langaugeSelected }}</div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('frontend.edit-profile',$authUser->id ) }}" class="btn btn-primary">{{ __('messages.edit_account') }}</a>
                <a href="{{ route('frontend.dashboard') }}" class="btn btn-outline-secondary ml-2">{{ __('messages.dashboard_goto') }}</a>
            </div>
        </div>
    </div>

</div>
</div>

@section('js-script-add')

@endsection

@endsection
