@extends('frontpanel.layout.app')

@section('content')
@section('select_dashboard', 'active')

<link rel="stylesheet" href="{{ asset('frontpanel/assets/css/dashboard.css') }}">

<div class="container-fluid dashboard-container">
    <!-- start page title -->
    <div class="row g-0">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h2 class="dashboard-title">
                    <strong>{{ __('messages.dashboard') }}</strong> -
                    {{ __('messages.welcome_back') }}, {{ LoginUser()->fName }} {{ LoginUser()->lName }}
                    {{ __('messages.from') }} {{ LoginUser()->agency_name }}!
                </h2>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dashboard">
            {{ session('success') }}
        </div>
    @endif

    <!-- Account Setup Section -->
    @if (empty($authUser->agency_name))
        <div class="account-setup-container">
            <div class="row justify-content-center">
                <div class="col-md-10 setup-box">
                    <h1 class="setup-title">{{ __('messages.setup_account') }}</h1>
                    <p class="setup-description">{{ __('messages.account_setup_description') }}</p>

                    <div class="setup-video-container">
                        <iframe class="setup-video" src="{{ get_default_settings('setup_video') }}"
                                title="{{ __('messages.setup_video_title') }}" allowfullscreen></iframe>
                    </div>

                    @if ($authUser->account_type != 'starter')
                        <div class="api-connect-section">
                            <h3>{{ __('messages.step') }} 1: {{ __('messages.connect_api') }}</h3>
                            <a class="btn btn-warning api-connect-btn" href="{{ $connecturl }}" target="_blank">
                                {{ __('messages.connect_api_v2') }}
                            </a>
                            <p class="api-connect-note">{{ __('messages.api_connect_description') }}</p>

                            <a class="video-instruction-link" data-toggle="modal" data-target="#apiVideoModal">
                                <i class="bi bi-play-circle"></i> {{ __('messages.watch_instructions') }}
                            </a>

                            <!-- API Video Modal -->
                            <div class="modal fade" id="apiVideoModal" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ __('messages.api_authorization_guide') }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="embed-responsive embed-responsive-16by9">
                                                <iframe class="embed-responsive-item"
                                                    src="{{ get_default_settings('authorize_v2_video') }}"
                                                    allowfullscreen></iframe>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                {{ __('messages.close') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="section-divider">
                    @endif

                    <form action="{{ route('frontend.agencyUpdate') }}" method="POST" class="agency-setup-form">
                        @csrf
                        <h3>{{ __('messages.step') }} 2: {{ __('messages.add_agency_details') }}</h3>
                        <p class="form-description">{{ __('messages.fill_form_below') }}</p>

                        <div class="form-group">
                            <label for="agency_name">{{ __('messages.agency_name') }}</label>
                            <input type="text" class="form-control" name="agency_name" id="agency_name"
                                   value="{{ $authUser->agency_name ?? '' }}">
                            <small class="form-text text-muted">{{ __('messages.agency_name_help') }}</small>
                        </div>

                        <div class="form-group">
                            <label for="accountType">{{ __('messages.ghl_account_type') }}</label>
                            <select name="accountType" id="accountT" class="form-control" required>
                                <option value="select">{{ __('messages.select_account_type') }}</option>
                                <option value="agency_pro" {{ $authUser->account_type == 'agency_pro' ? 'selected' : '' }}>
                                    {{ __('messages.agency_pro') }}
                                </option>
                                <option value="freelancer" {{ $authUser->account_type == 'freelancer' ? 'selected' : '' }}>
                                    {{ __('messages.freelancer') }}
                                </option>
                                <option value="starter" {{ $authUser->account_type == 'starter' ? 'selected' : '' }}>
                                    {{ __('messages.starter_account') }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="agency_app_url">{{ __('messages.agency_app_url') }}</label>
                            <input type="text" class="form-control" name="agency_app_url" id="agency_app_url"
                                   value="{{ $authUser->agency_url ?? '' }}">
                            <small class="form-text text-muted">{{ __('messages.agency_url_help') }}</small>
                        </div>

                        <input type="hidden" name="id" value="{{ $authUser->id }}">
                        <button type="submit" name="submit_setup" class="btn btn-primary submit-btn">
                            {{ __('messages.submit') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Dashboard Content -->
    <div class="row dashboard-content">
        <!-- Left Column - Tools and News -->
        <div class="col-lg-7 col-xl-8">
            <!-- Tools Section -->
            <div class="dashboard-card tools-section">
                <h2 class="section-title">{{ __('messages.tools_quick_access') }}</h2>
                <div class="row tools-grid">
                    @foreach ($projects as $project)
                        <div class="col-6 col-md-4 tool-item">
                            <a href="{{ $project['url'] }}?licensekey={{ LoginUser()->licensekey }}"
                               class="btn btn-grad tool-btn">
                                {{ $project['name'] }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- News & Updates Section -->
            <div class="dashboard-card news-section">
                <h2 class="section-title">{{ __('messages.news_updates') }}</h2>
                <div class="row news-grid">
                    @foreach ($articles as $index => $article)
                        <div class="col-md-6 col-lg-4 news-item">
                            <div class="news-card">
                                <div class="card-body">
                                    <h4 class="news-title">{{ $article['content']['title'] }}</h4>
                                    <p class="news-excerpt">
                                        {{ Str::limit(strip_tags($article['content']['description']), 200) }}...
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Review Stats (Conditional) -->
            @if (in_array(LoginUser(true), [10, 1]) && LoginUser()->SMART_Reviews)
                @php
                    $accountLocations = getAccountLocations(LoginUser(true), 1);
                    $totalRevReq = 0;
                    foreach ($accountLocations as $location) {
                        $totalRevReq += getRevReqByLoc($location['loc_id']);
                    }
                @endphp

                <div class="dashboard-card review-stats">
                    <h2 class="section-title">{{ __('messages.review_stats') }}</h2>
                    <div class="stats-container">
                        <div class="stat-card">
                            <div class="stat-title">{{ __('messages.review_requests') }}</div>
                            <div class="stat-period">{{ __('messages.last_30_days') }}</div>
                            <div class="stat-value">{{ $totalRevReq }}</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column - Sidebar -->
        <div class="col-lg-5 col-xl-4 sidebar">
            <!-- What's New Section -->
            <div class="sidebar-card">
                <h2 class="sidebar-title">{{ __('messages.whats_new') }}</h2>

                <!-- Slack Card -->
                <div class="feature-card slack-card">
                    <h4>{{ __('messages.join_slack') }}</h4>
                    <a href="{{ get_default_settings('slack_join_link') }}" target="_blank">
                        <img src="https://a.slack-edge.com/bv1-13/slack_logo-ebd02d1.svg"
                             alt="Slack Logo" class="slack-logo">
                    </a>
                </div>

                <!-- Support Calls Card -->
                <div class="feature-card support-card">
                    <h3>{{ __('messages.join_support_calls') }}</h3>
                    <div class="support-schedule">
                        <strong>{{ get_default_settings('weekly_call_day') }}</strong><br>
                        <strong>{{ get_default_settings('weekly_call_time') }}</strong>
                    </div>
                    <a href="{{ get_default_settings('weekly_support_link') }}" target="_blank" class="support-link">
                        {{ __('messages.join_call') }}
                    </a>
                </div>

                <!-- API V2 Section (Conditional) -->
                @if ( LoginUser()->account_type != 'starter')
                    <div class="feature-card api-card">
                        <h4>{{ __('messages.authorize_api_v2') }}</h4>
                        <div class="video-container">
                            <iframe src="https://www.youtube.com/embed/arYSCfQZlP4?si=RbTlRim8KPoWL_-l"
                                    title="{{ __('messages.api_video_title') }}" allowfullscreen></iframe>
                        </div>
                        <a class="btn btn-warning api-btn" href="{{ $connecturl }}" target="_blank">
                            {{ __('messages.connect_api_v2') }}
                        </a>
                        <p class="api-note">{{ __('messages.api_v2_description') }}</p>
                    </div>
                @endif

                <!-- Power Tools Bundle (Conditional) -->
                @if (!LoginUser()->isMember)
                    <div class="feature-card bundle-card">
                        <h2>{{ __('messages.get_it_all') }}</h2>
                        <img src="{{ asset('frontpanel/assets/img/SmartPowerToolsBundle.jpg') }}"
                             alt="Power Tools Bundle" class="bundle-image">
                        <h3 class="bundle-title">{{ __('messages.smart_scripts') }}</h3>
                        <h4 class="bundle-subtitle">{{ __('messages.power_tools_bundle') }}</h4>
                        @if (LoginUser()->SMART_Reviews)
                            <h3 class="bundle-discount">{{ __('messages.reviews_ltd_discount') }}</h3>
                        @endif
                        <p class="bundle-description">{{ __('messages.bundle_description') }}</p>
                        <a href="https://get.thinkbigstudios.ca/power-tools" class="btn btn-primary bundle-btn" target="_blank">
                            {{ __('messages.get_access') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@section('js-script-add')
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <script>
        $(function() {
            $("body").on('hidden.bs.modal', function(e) {
                var $iframes = $(e.target).find("iframe");
                $iframes.each(function(index, iframe) {
                    $(iframe).attr("src", $(iframe).attr("src"));
                });
            });
        });
    </script>
@endsection

@endsection
