@extends('adminpanel.layout.app')

@section('content')
@section('select_setting', 'active')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-head-row card-tools-still-right">
                        <h4 class="card-title">Settings</h4>
                    </div>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">

                            <form action="{{ route('admin.setting-save') }}" method="POST" class="card-box"
                        enctype="multipart/form-data">
                        @csrf
                    <div class="row">

                        <div class="form-group col-md-4 mt-3">
                            <label for="setup_video">Setup Video Link </label>
                            <input type="text" placeholder="Enter Setup Video Link" class="form-control" name="setup_video"
                                value="{{ get_default_settings('setup_video') }}" id="setup_video" autocomplete="off">
                        </div>

                        <div class="form-group col-md-4 mt-3">
                            <label for="authorize_v2_video">Authorize API V2 Vifeo Link</label>
                            <input type="text" placeholder="Enter Authorize API V2 Video Link" class="form-control" name="authorize_v2_video"
                                value="{{ get_default_settings('authorize_v2_video') }}" id="authorize_v2_video" autocomplete="off">
                        </div>

                        <div class="form-group col-md-4 mt-3">
                            <label for="slack_join_link">Slack Join Link</label>
                            <input type="text" placeholder="Enter Slack Join Link" class="form-control" name="slack_join_link"
                                value="{{ get_default_settings('slack_join_link') }}" id="slack_join_link" autocomplete="off">
                        </div>

                        <div class="form-group col-md-4 mt-3">
                            <label for="weekly_support_link">Weekly Support Call Link</label>
                            <input type="text" placeholder="Weekly Support Call Link" class="form-control" name="weekly_support_link"
                                value="{{ get_default_settings('weekly_support_link') }}" id="weekly_support_link" autocomplete="off">
                        </div>

                        <div class="form-group col-md-4 mt-3">
                            <label for="weekly_call_day">Weekly Call Day</label>
                            <input type="text" placeholder="Weekly Call Day" class="form-control" name="weekly_call_day"
                                value="{{ get_default_settings('weekly_call_day') }}" id="weekly_call_day" autocomplete="off">
                        </div>

                        <div class="form-group col-md-4 mt-3">
                            <label for="weekly_call_time">Weekly Call Times</label>
                            <input type="text" placeholder="Weekly Support Call Link" class="form-control" name="weekly_call_time"
                                value="{{ get_default_settings('weekly_call_time') }}" id="weekly_call_time" autocomplete="off">
                        </div>


                        <div class="form-group col-md-4 mt-3">
                            <label for="crm_client_id">CRM Client Id</label>
                            <input type="text" placeholder="Enter CRM Client Id" class="form-control" name="crm_client_id"
                                value="{{ get_default_settings('crm_client_id') }}" id="crm_client_id" autocomplete="off">
                        </div>

                        <div class="form-group col-md-4 mt-3">
                            <label for="crm_client_secret">CRM Client Secret Id</label>
                            <input type="text" placeholder="CRM Client Secret Id" class="form-control" name="crm_client_secret"
                                value="{{ get_default_settings('crm_client_secret') }}" id="crm_client_secret" autocomplete="off">
                        </div>


                    </div>

                        <div class="form-group mt-3">
                            <a href="{{ route('admin.accounts') }}"
                                class="btn btn-danger btn-sm text-light px-4 mt-3  mb-0 ml-2">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm text-light px-4 mt-3  mb-0"
                                style="background-color: black">Save</button>
                        </div>

                </div>
            </div>

        </div>
    </div>
    </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

