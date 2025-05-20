@extends('frontpanel.layout.app')

@section('content')
@section('select_dashboard', 'active')


<div class="container-fluid" style="padding:0 1.5rem">
    <!-- start page title -->
    <div class="row g-0">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h2>Dashboard<strong> - Welcome back, {{ auth()->user()->fName }} {{ auth()->user()->lName }} From
                        {{ auth()->user()->agency_name }}!</strong></h2>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- end page title -->
    @if (empty($authUser->agency_name))
        <div class="container" style="margin-bottom:150px;">
            <div class="row justify-content-md-center">
                <div class="col-md-12 center">
                    <div class="col-md-10">
                        <!-- Put Form Here -->
                        <h1> Set Up Your GHL Power Tools Account </h1>
                        <p>Looks like there are a few things we need to do in order to finish setting up your account.
                        </p>

                        <div>
                            <iframe width="560" height="315" src="{{ get_default_settings('setup_video') }}"
                                title="YouTube video player" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                        </div>

                        @if ($authUser->oauth == 0 && $authUser->account_type != 'starter')
                            @php
                                $aid = $authUser->id ?? session('id');
                                $state = 'aid-' . $aid;
                            @endphp
                            <div class="col-12">
                                <h3>Step 1: Connect Your Agency API</h3>
                                <a class="btn btn-warning"
                                    style="width:300px; padding:15px; margin-bottom:10px; color:#9b3911; font-weight:bold; font-size:24px;"
                                    href="{{ $connecturl }}"
                                    target="_blank">Connect API V2</a>
                                <br /> By authorizing API V2 Your new accounts in Smart Scripts / Power Tools will use
                                the new API
                                <!-- Button trigger modal -->
                                <br>Click Here for instructions: <a class="" data-toggle="modal"
                                    data-target="#modelId">
                                    Open Video
                                </a>

                                <!-- Modal -->
                                <div class="modal fade" id="modelId" tabindex="-1" role="dialog"
                                    aria-labelledby="modelTitleId" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">How to Authorize API V2 for GHL Power Tools</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <iframe width="550" height="300"
                                                    src="{{ get_default_settings('authorize_v2_video') }}"
                                                    title="YouTube video player" frameborder="0"
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                    referrerpolicy="strict-origin-when-cross-origin"
                                                    allowfullscreen></iframe>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr style="border-bottom:2px dotted #000000;">
                        @endif

                        <form action="{{ route('frontend.agencyUpdate') }}" method="POST" role="form">
                            @csrf
                            <h3>Step 2: Add Agency Details</h3>
                            <p>Let's fill out the form below.</p>
                            <div class="form-group">
                                <label for="">Agency Name</label>
                                <input type="text" class="form-control" name="agency_name" id=""
                                    aria-describedby="helpId" placeholder="" value="{{ $authUser->agency_name ?? '' }}">
                                <small id="helpId" class="form-text text-muted">Agency Name</small>
                            </div>
                            <div class="form-group">
                                <label for="accountType">GHL Account Type</label>
                                <select name="accountType" id="accountT" class="form-control" required="required">
                                    <option value="select">Select Account Type</option>
                                    <option value="agency_pro"
                                        {{ $authUser->account_type == 'agency_pro' ? 'selected' : '' }}>SAAS / Agency
                                        Pro $497</option>
                                    <option value="freelancer"
                                        {{ $authUser->account_type == 'freelancer' ? 'selected' : '' }}>Freelancer /
                                        Agency Unlimited $297</option>
                                    <option value="starter"
                                        {{ $authUser->account_type == 'starter' ? 'selected' : '' }}>Starter Account $97
                                    </option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="">Agency App URL</label>
                                <input type="text" class="form-control" name="agency_app_url" id=""
                                    aria-describedby="helpId" placeholder="" value="{{ $authUser->agency_url ?? '' }}">
                                <small id="helpId" class="form-text text-muted">Agency App URL - Your whitelabel
                                    domain you use to log into high level with: IE: https://app.gohighlevel.com, or
                                    https://app.freshleads.com etc.</small>
                            </div>
                            <input type="hidden" name="id" value="{{ $authUser->id }}">

                            <button type="submit" name="submit_setup" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif


    <div class="row justify-content-md-center">
        <div class="col-md-5">
            <div class="row">
                <div class="col-md-12">
                    <h2>Tools - Quick Access</h2>
                </div>
            </div>
            <div class="row" style="margin-top:15px;">
                @foreach ($projects as $project)
                    <div class="col-md-4">
                        <a href="{{ $project['url'] }}?licensekey={{ auth()->user()->licensekey }}" type="button"
                            class="btn btn-grad "
                            style="padding:15px; font-size:16px; font-weight:700;">{{ $project['name'] }}</a>
                    </div>
                @endforeach
            </div>

            <div class="row" style="margin-top:25px; padding-top:10px; border-top:2px dashed #b4b4b4;">
                <div class="col-md-12">
                    <h2 style="border:none; padding-bottom:0px;"> News & Updates </h2>
                </div>

                @foreach ($articles as $index => $article)
                    @if ($index % 3 == 0)
                        <div class="row">
                    @endif

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>{{ $article['content']['title'] }}</h4>
                                </div>
                                <div class="card-body justify">
                                    {{ Str::limit(strip_tags($article['content']['description']), 200) }}...
                                </div>
                                <div class="card-footer" style="margin-top:15px;">
                                    <a href="{{ route('frontend.articles', ['id' => $article['_id'], 'licensekey' => auth()->user()->licensekey]) }}"
                                        type="button" class="btn btn-primary">Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (($index % 3 == 2) || ($loop->last))
                        </div>
                    @endif
                @endforeach
            </div>

            @if (in_array(auth()->id(), [10, 1]))
                @if (auth()->user()->SMART_Reviews)
                    @php
                        $accountLocations = getAccountLocations(auth()->id(), 1);
                        $totalRevReq = 0;

                        foreach ($accountLocations as $location) {
                            $totalRevReq += getRevReqByLoc($location['loc_id']);
                        }
                    @endphp

                    <div class="row block">
                        <div class="col">
                            <h2 style="padding-top:0px;">Review Stats</h2>
                        </div>

                        <div class="col-md-12" style="margin-top:25px;">
                            <div class="col-md-4">
                                <div class="card ">
                                    <div class="card-body">
                                        <div class="card-title"><span
                                                style="font-size:18px; margin-bottom:0px; padding-bottom:0px; font-weight:700;">Review
                                                Requests</span><br />Last 30 Days</div>
                                        <div class="card-body center">
                                            <h1 class="center">{{ $totalRevReq }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4"></div>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <div class="col-md-4">
            <h2>What's New</h2>
            <div class="card block">
                <div class="card-body text-center">
                    <h4>Join Our Slack Channel</h4>
                    <a href="{{  get_default_settings('slack_join_link') }}"
                        target="_blank"><img src="https://a.slack-edge.com/bv1-13/slack_logo-ebd02d1.svg"
                            style="width:100px;"></a>
                </div>
            </div>
            <div class="card block">
                <div class="card-body">
                    <h3>Join Our Weekly Support Calls</h3>
                    <strong>{{  get_default_settings('weekly_call_day') }} <br /> {{  get_default_settings('weekly_call_time') }}</strong><br /><a
                        href="{{  get_default_settings('weekly_support_link') }}"
                        target="_blank">{{  get_default_settings('weekly_support_link') }} </a>
                </div>
            </div>

            @if (auth()->user()->oauth == 0 && auth()->user()->account_type != 'starter')
                @php
                    $aid = auth()->user()->id ?? session('id');
                    $state = 'aid-' . $aid;
                @endphp
                <div class="row">
                    <div class="col-md-6">
                        <h4>DO THIS: Authorize API V2 For High Level</h4>
                        <iframe width="350" height="196"
                            src="https://www.youtube.com/embed/arYSCfQZlP4?si=RbTlRim8KPoWL_-l"
                            title="YouTube video player" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                        <br />
                        <a class="btn btn-warning"
                            href="{{ $connecturl }}"
                            target="_blank">Connect API V2</a>
                        <br /> By authorizing API V2 Your new accounts in Smart Scripts / Power Tools will use the new
                        API
                    </div>
                    <div class="col-md-6"></div>
                </div>
            @endif

            @if (!auth()->user()->isMember)
                <h2>Get It All!</h2>
                {{-- <div class="card">
                    <div class="card-body"> --}}
                        <img style="width: 100%;margin-top: 10px;margin-bottom: 10px;border-radius: 0;border-bottom-right-radius: 0;border-top-left-radius: 0;"
                            src="{{ asset('frontpanel/assets/img/SmartPowerToolsBundle.jpg') }}">
                        <h3 class="card-title">SMART SCRIPTS</h3>
                        <h4 class="card-title">Power Tools Bundle</h4>
                        @if (auth()->user()->SMART_Reviews)
                            <h3>Reviews LTD Owners - Save 40%</h3>
                        @endif
                        <p class="card-text">Tools, Scripts, Snapshots, and Apps that give you more functionality and
                            automation inside of High Level. New Apps, Tools, Automations being added monthly.</p>
                        <a href="https://get.thinkbigstudios.ca/power-tools" class="btn btn-primary" target="_blank"
                            type="button" style="width: 100%;">Get Access</a>
                    {{-- </div>
                </div> --}}
            @endif
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
