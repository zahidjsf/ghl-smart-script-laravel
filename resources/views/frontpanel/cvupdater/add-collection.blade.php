@extends('frontpanel.layout.app')

@section('content')
@section('smart_apps', 'active')

<div class="container-fluid" style="padding:0 1.5rem">
    <!-- start page title -->
    <div class="row g-0">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h2><strong>{{ __('messages.create_collection') }}</strong></h2>
            </div>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif


    <form class="add-location" method="POST" action="{{ route('frontend.smart_reward.createcollection') }}">
        @csrf
        <div class="row">
            <div class="form-group col-md-6">
                <label for='locations'>{{ __('messages.location_cv') }}</label><br />
                <select id='locations' name='locations' class="form-control">
                    <option value=""> {{ __('messages.select_location') }}</option>
                    @foreach ($agencyLocations as $location)
                    <option data-user-id="{{$location->a_id}}" data-location-id="{{$location->loc_id}}" value="{{ $location->id }}|{{ $location->loc_id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for='cf_loc'> {{ __('messages.location_cf') }}</label><br />
                <select id='cf_loc' name='cf_loc' class="form-control">
                    <option data-cf-location-id="0" value="0" selected >{{ __('messages.same_cv_above') }}</option>
                    @foreach ($agencyLocations as $location)
                    <option data-cf-location-id="{{$location->loc_id}}" value="{{ $location->loc_id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
                {{ __('messages.location_cf_desc') }}

            </div>
        </div>
        <br>
        <div class="form-group text-end" id="getFieldsBtnWrapper" hidden>
            <a class="btn btn-primary" id="get-custom-values">{{ __('messages.get_cvs') }}</a>
        </div>
        <div class="form-group">
            <label for="col_name">{{ __('messages.collection_name') }}</label>
            <input type="text" class="form-control" id="col_name" name="collection_name" value="" placeholder="Enter Collection Name">
        </div>
        <br>
        <div class="form-group">
            <label for="textarea" class=" control-label">{{ __('messages.description') }}</label>
            <textarea name="collection_description" id="textarea" class="form-control" rows="3"></textarea>
        </div>
        <br>
        <div id="customValuesContainer"></div>
        <div class="modal-footer">
            <button type="submit" name="insert" class="btn btn-primary add-location">{{ __('messages.save') }}</button>
        </div>
    </form>


</div>
@endsection

@section('js-script-add')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const locationSelect = document.getElementById('locations');
        const getFieldsBtnWrapper = document.getElementById('getFieldsBtnWrapper');

        function toggleButtonVisibility() {
            const locationSelected = locationSelect.value.trim() !== "";
            getFieldsBtnWrapper.hidden = !(locationSelected);
        }
        locationSelect.addEventListener('change', toggleButtonVisibility);
        // cfLocSelect.addEventListener('change', toggleButtonVisibility);
    });
    $(document).ready(function() {
        $('#get-custom-values').on('click', function() {
            var selectedOption = $('#locations option:selected'); // Get selected <option>
            var cfSelectedOption = $('#cf_loc option:selected'); // Get selected <option>
            var locationId = selectedOption.data('location-id');
            var cfLocationId = cfSelectedOption.data('cf-location-id');
            if(cfLocationId == 0 )
            {
                cfLocationId = locationId;
            }
            var userId = selectedOption.data('user-id');

            var url = '{{ url("smart-reward/get-customvalues") }}/' + locationId + '?user_id=' + userId+'&cf_location_id='+cfLocationId;
            $.ajax({
                url:url,
                method: 'GET',
                success: function(response) {
                    $('#customValuesContainer').html(response);
                },
                error: function() {
                    alert('Failed to load custom fields.');
                }
            });
        });
    });
</script>
@endsection
