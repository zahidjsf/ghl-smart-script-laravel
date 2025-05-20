@extends('adminpanel.layout.app')

@section('content')
@section('select_account', 'active')

<div class="row">
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row card-tools-still-right">
                    <h4 class="card-title">Add new Accounts</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="content-block">
                            <a href="{{ route('admin.accounts') }}">Go Back To Accounts</a>
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

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="content-block">
                            <form action="{{ route('admin.accountstore') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="form-group col-md-6 col-lg-4">
                                        <label for="fname">First Name</label>
                                        <input type="text" class="form-control" id="fname" name="fname"
                                            value="{{ old('fname') }}" required>
                                    </div>

                                    <div class="form-group col-md-6 col-lg-4">
                                        <label for="lname">Last Name</label>
                                        <input type="text" class="form-control" id="lname" name="lname"
                                            value="{{ old('lname') }}" required>
                                    </div>

                                    <div class="form-group col-md-6 col-lg-4">
                                        <label for="username">Username</label>
                                        <input type="text" class="form-control" id="username" name="username"
                                            value="{{ old('username') }}" required>
                                    </div>

                                    <div class="form-group col-md-6 col-lg-4">
                                        <label for="password">Password</label>
                                        <input type="text" class="form-control" id="password" name="password"
                                            required>
                                    </div>

                                    <div class="form-group col-md-6 col-lg-4">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ old('email') }}" required>
                                    </div>

                                    <div class="form-group col-md-6 col-lg-4">
                                        <label for="activation_code">Activation Code</label>
                                        <input type="text" class="form-control" id="activation_code"
                                            name="activation_code" value="{{ old('activation_code') }}">
                                    </div>

                                    <div class="form-group col-md-6 col-lg-4">
                                        <label for="rememberme">Remember Me Code</label>
                                        <input type="text" class="form-control" id="rememberme" name="rememberme"
                                            value="{{ old('rememberme') }}">
                                    </div>

                                    <div class="form-group col-md-6 col-lg-4">
                                        <label for="apikey">Api Key</label>
                                        <input type="text" class="form-control" id="apikey" name="apikey"
                                            value="{{ old('apikey') }}">
                                    </div>

                                    <div class="form-group col-md-6 col-lg-4">
                                        <label for="agency_name">Agency Name</label>
                                        <input type="text" class="form-control" id="agency_name" name="agency_name"
                                            value="{{ old('agency_name') }}">
                                    </div>

                                    <div class="form-group col-md-6 col-lg-4">
                                        <label for="agency_url">Agency Url</label>
                                        <input type="text" class="form-control" id="agency_url" name="agency_url"
                                            value="{{ old('agency_url') }}">
                                    </div>

                                    <div class="form-group col-md-6 col-lg-4">
                                        <label for="account_type">Agency Type</label>
                                        <select class="form-control" id="account_type" name="account_type">
                                            <option value="">Select Option</option>
                                            <option value="starter">Starter</option>
                                            <option value="agency_pro">Agency Pro</option>
                                            <option value="freelancer">Freelancer</option>
                                        </select>
                                    </div>

                                    @if (auth()->id() == 1)
                                        <div class="form-group col-md-6 col-lg-4">
                                            <label for="role">Role</label>
                                            <select class="form-control" id="role" name="role">
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role }}">{{ $role }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        <input type="hidden" name="role" value="User">
                                    @endif



                                    @if (auth()->id() == 1)
                                        <div class="form-group col-md-6 col-lg-4">
                                            <h3>SMART SCRIPTS BUNDLE MEMBER</h3>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="SSBM"
                                                    id="SSBM" value="yes">
                                                <label class="form-check-label" for="SSBM">Yes</label>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-6 col-lg-4">
                                            <h3>Suspend Account</h3>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="suspend"
                                                    id="suspend" value="yes">
                                                <label class="form-check-label" for="suspend">Yes</label>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="form-group col-md-6 col-lg-4">
                                        <label for="license">License Key</label>
                                        <input type="text" class="form-control" name="license" id="license"
                                            value="{{ $license }}">
                                    </div>

                                    <div class="form-group ">
                                        <h3>System Project Access</h3>
                                        @foreach ($systemProjects as $project)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox"
                                                    name="projectAccess[]" id="project_{{ $project->id }}"
                                                    value="{{ $project->id }}"
                                                    {{ $project->a_id != auth()->id() && auth()->id() != 1 ? 'disabled' : '' }}>
                                                <label class="form-check-label" for="project_{{ $project->id }}">
                                                    {{ $project->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="form-group col-md-6 col-lg-4">
                                        <button type="submit" class="btn btn-primary">Create Account</button>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
