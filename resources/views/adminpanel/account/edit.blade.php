@extends('adminpanel.layout.app')

@section('content')
@section('select_account', 'active')

<div class="row">
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row card-tools-still-right">
                    <h4 class="card-title">Edit Account</h4>
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

                        @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                        @endif

                        <div class="content-block">


                            <form action="{{ route('admin.accountupdate', $account->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fname">First Name</label>
                                            <input type="text" class="form-control" id="fname" name="fname"
                                                value="{{ $account->fName }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lname">Last Name</label>
                                            <input type="text" class="form-control" id="lname" name="lname"
                                                value="{{ $account->lName }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="username">Username</label>
                                            <input type="text" class="form-control" id="username" name="username"
                                                value="{{ $account->username }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Password (leave blank to keep current)</label>
                                            <input type="password" class="form-control" id="password" name="password">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ $account->email }}" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="apikey">GHL API Key</label>
                                            <input type="text" class="form-control" id="apikey" name="apikey"
                                                value="{{ $apiKey }}" required>
                                        </div>
                                    </div>


                                    <div class="form-group col-md-6 col-lg-4">
                                        <label for="agency_name">Agency Name</label>
                                        <input type="text" class="form-control" id="agency_name" name="agency_name"
                                            value="{{$account->agency_name }}">
                                    </div>

                                    <div class="form-group col-md-6 col-lg-4">
                                        <label for="agency_url">Agency Url</label>
                                        <input type="text" class="form-control" id="agency_url" name="agency_url"
                                            value="{{ $account->agency_url }}">
                                    </div>

                                    <div class="form-group col-md-6 col-lg-4">
                                        <label for="account_type">Agency Type</label>
                                        <select class="form-control" id="account_type" name="account_type">
                                            <option value="starter" @if ($account->account_type == 'starter')
                                                @endif>Starter</option>
                                            <option value="agency_pro" @if ($account->account_type == 'agency_pro')
                                                @endif>Agency Pro</option>
                                            <option value="freelancer" @if ($account->account_type == 'freelancer')
                                                @endif>Freelancer</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="license">License Key</label>
                                            <input type="text" class="form-control" id="license"
                                                value="{{ $account->licensekey }}" readonly name="license">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="activation_code">Activation Code</label>
                                            <input type="text" class="form-control" id="activation_code"
                                                name="activation_code" value="{{ $account->activation_code }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="rememberme">Remember Me Code</label>
                                            <input type="text" class="form-control" id="rememberme" name="rememberme"
                                                value="{{ $account->rememberme }}">
                                        </div>
                                    </div>
                                </div>

                                @if(auth()->user()->id == 1)
                                <div class="form-group">
                                    <label for="role">Role</label>
                                    <select class="form-control" id="role" name="role" required>
                                        @foreach($roles as $role)
                                        <option value="{{ $role }}" {{ $account->role == $role ? 'selected' : '' }}>
                                            {{ $role }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @else
                                <input type="hidden" name="role" value="{{ $account->role }}">
                                @endif

                                <div class="form-group">
                                    <h4>System Project Access</h4>
                                    @foreach($systemProjects as $project)
                                    @php
                                    $checked = in_array($project->id, $systemAccess);
                                    $disabled = auth()->user()->id != 1 && $project->a_id != auth()->user()->id;

                                    $license = $account->projects->firstWhere('id', $project->id);
                                    $numLicenses = $license ? $license->pivot->numLicenses : 0;

                                    @endphp

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="projectAccess[]"
                                            id="project_{{ $project->id }}" value="{{ $project->id }}"
                                            {{ $checked ? 'checked' : '' }} {{ $disabled ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="project_{{ $project->id }}">
                                            {{ $project->name }}
                                            @if($checked)
                                            @if($numLicenses > 0)
                                            | Licenses = {{ $numLicenses }} |
                                            <button class="btn btn-sm btn-primary load-license-modal"
                                                data-url="{{ route('admin.licenseoperation', [$project->id, $account->id, 'edit']) }}">
                                                Edit License
                                            </button>
                                            @else
                                            <button class="btn btn-sm btn-primary load-license-modal"
                                                data-url="{{ route('admin.licenseoperation', [$project->id, $account->id, 'add']) }}">
                                                Add Licenses
                                            </button>
                                            @endif
                                            @endif
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                <div id="license-modal-container"></div>

                                @if(auth()->user()->id == 1)
                                <div class="form-group">
                                    <h4>SMART SCRIPTS BUNDLE MEMBER</h4>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="SSBM" id="SSBM"
                                            value="yes" {{ $isBundleMember }}>
                                        <label class="form-check-label" for="SSBM">Yes</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <h4>Suspend Account</h4>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="suspend" id="suspend"
                                            value="yes" {{ $isSuspended }}>
                                        <label class="form-check-label" for="suspend">Yes</label>
                                    </div>
                                </div>
                                @endif

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Update Account</button>
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


@section('js-script')
<script>

$(document).ready(function() {
    $('.load-license-modal').on('click', function(e) {
        e.preventDefault();
        let url = $(this).data('url');

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                // Inject the returned modal view into the container
                $('#license-modal-container').html(response.view);

                // Extract the modal ID from the response content
                let modalId = $(response.view).filter('.modal').attr('id');

                // Show the modal
                $('#' + modalId).modal('show');
            },
            error: function(xhr) {
                alert('Something went wrong while loading the license modal.');
            }
        });
    });

    // Optional: handle form submit inside modal
    $(document).on('click', '.modal .btn-primary', function(e) {
        e.preventDefault();
        let form = $(this).closest('.modal').find('form');
        form.submit();
    });
});


$(document).ready(function () {
    $('.license-form').on('submit', function (e) {
        e.preventDefault();
        alert('fsdfsdf');
        const form = $(this);
        const formData = new FormData(this);
        const projectId = form.data('project-id');
        const submitButton = form.find('button[type="submit"]');

        submitButton.prop('disabled', true).text('Saving...');

        $.ajax({
            url: "{{ route('admin.licenseUpdate') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                alert('Licenses updated successfully!');
                $('#modal-licenses-' + projectId).modal('hide');
                $('#modal-addlicenses-' + projectId).modal('hide');
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('Error occurred. Please try again.');
            },
            complete: function () {
                submitButton.prop('disabled', false).text('Save changes');
            }
        });
    });
});



$(document).on('click', '.modal .close', function() {
    $(this).closest('.modal').modal('hide');
});



</script>
@endsection
