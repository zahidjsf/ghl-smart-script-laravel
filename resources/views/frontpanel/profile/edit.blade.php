@extends('frontpanel.layout.app')

@section('content')
@section('select_account_setting', 'active')


<div class="container-fluid" style="padding:0 1.5rem">
    <!-- start page title -->
    <div class="row g-0">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h2><strong>{{ __('messages.profile_edit') }}</strong></h2>
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

            <div class="content-block">
                <form action="{{ route('frontend.profile-update', $account->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fname">{{ __('messages.first_name') }}</label>
                                <input type="text" class="form-control" id="fname" name="fname"
                                    value="{{ $account->fName }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lname">{{ __('messages.last_name') }}</label>
                                <input type="text" class="form-control" id="lname" name="lname"
                                    value="{{ $account->lName }}" required>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username">{{ __('messages.username') }}</label>
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

                    <br>

                    <div class="row">


                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">{{ __('messages.email') }}</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ $account->email }}" required>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="agency_name">{{ __('messages.agency_name') }}</label>
                            <input type="text" class="form-control" id="agency_name" name="agency_name"
                                value="{{ $account->agency_name }}">
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="agency_url">{{ __('messages.agency_url') }}</label>
                            <input type="text" class="form-control" id="agency_url" name="agency_url"
                                value="{{ $account->agency_url }}">
                            <span>IE: https://app.yourdomain.com or https://app.gohighlevel.com</span>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="locale">{{ __('messages.language') }}</label>
                                <select class="form-control" id="locale" name="locale" required>
                                    <option value="en" {{ $account->locale == 'en' ? 'selected' : '' }}>English</option>
                                    <option value="es" {{ $account->locale == 'es' ? 'selected' : '' }}>Spanish</option>
                                    <option value="fr" {{ $account->locale == 'fr' ? 'selected' : '' }}>French</option>
                                </select>
                            </div>
                        </div>
                    </div>
            </div>
            <div id="license-modal-container"></div>
            <br>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">{{ __('messages.update_account') }}</button>
            </div>
            </form>

        </div>


    </div>
</div>

</div>
</div>

@section('js-script-add')

@endsection

@endsection
