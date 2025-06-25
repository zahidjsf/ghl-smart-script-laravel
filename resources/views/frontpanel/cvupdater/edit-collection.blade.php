@extends('frontpanel.layout.app')

@section('content')
@section('smart_apps', 'active')

<div class="container-fluid" style="padding:0 1.5rem">
    <!-- start page title -->
    <div class="row g-0">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h2><strong>{{ __('messages.edit_collection') }}: {{ $collection->name }}</strong></h2>
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

    <form class="add-location" method="POST" action="{{ route('frontend.smart_reward.updatecollection', $collection->id) }}">
        @csrf
        <div class="row">
            <div class="form-group col-md-6">
                <label for='locations'>Select Location To Grab Custom Values From</label><br />
                <select id='locations' name='locations' class="form-control">
                    <option value="">Select A Location</option>
                    @foreach ($agencyLocations as $location)
                    <option value="{{ $location->id }}|{{ $location->loc_id }}"
                        {{ $collection->orig_loc_id == $location->id ? 'selected' : '' }}
                        data-user-id="{{$location->a_id}}"
                        data-location-id="{{$location->loc_id}}">
                        {{ $location->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for='cf_loc'>Grab Custom Fields From Another Location</label><br />
                <select id='cf_loc' name='cf_loc' class="form-control">
                    <option data-cf-location-id="0" value="0" {{ $collection->cf_loc_id == $collection->orig_loc_id ? 'selected' : '' }}>Same Location As Custom Values Above</option>
                    @foreach ($agencyLocations as $location)
                    <option data-cf-location-id="{{$location->loc_id}}"
                        value="{{ $location->loc_id }}"
                        {{ $collection->cf_loc_id == $location->loc_id && $collection->cf_loc_id != $collection->orig_loc_id ? 'selected' : '' }}>
                        {{ $location->name }}
                    </option>
                    @endforeach
                </select>
                Select the location where you will have your client fill out the form to update the custom values.
            </div>
        </div>
        <br>
        <div class="form-group text-end" id="getFieldsBtnWrapper">
            <a class="btn btn-primary" id="reload-custom-values">Reload Custom Values</a>
        </div>
        <div class="form-group">
            <label for="col_name">Collection Name</label>
            <input type="text" class="form-control" id="col_name" name="collection_name" value="{{ $collection->name }}" placeholder="Enter Collection Name">
        </div>
        <br>
        <div class="form-group">
            <label for="textarea" class=" control-label">Description</label>
            <textarea name="collection_description" id="textarea" class="form-control" rows="3">{{ $collection->description }}</textarea>
        </div>
        <br>
        <div id="customValuesContainer">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading custom values...</p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" name="insert" class="btn btn-primary add-location">Update Collection</button>
        </div>
    </form>
</div>
@endsection

@section('js-script-add')
<script>
    function autoCheck(element) {
        const index = element.dataset.index;
        if (index) {
            const checkbox = document.getElementById('select_' + index);
            if (checkbox && element.value !== '') {
                checkbox.checked = true;
            }
        }
    }

    function showAdv(id) {
        const row = document.querySelector('.adv_' + id);
        if (row) {
            row.style.display = (row.style.display === 'none' || row.style.display === '') ? 'table-row' : 'none';
        }
    }

    function loadCustomValues() {
        const locationSelect = $('#locations option:selected');
        const cfSelect = $('#cf_loc option:selected');

        const locationId = locationSelect.data('location-id');
        const cfLocationId = cfSelect.data('cf-location-id') || locationId;
        const userId = locationSelect.data('user-id');
        const collectionId = {{ $collection->id }};

        if (!locationId) {
            return;
        }

        $('#customValuesContainer').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading custom values...</p>
            </div>
        `);

        $.ajax({
            url: '{{ route("frontend.smart_reward.updatecollectioncustomvalues") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                location_id: locationId,
                cf_location_id: cfLocationId,
                collection_id: collectionId
            },
            success: function(response) {
                if (response.success) {
                    $('#customValuesContainer').html(response.html);
                } else {
                    $('#customValuesContainer').html(`
                        <div class="alert alert-danger">${response.message}</div>
                    `);
                }
            },
            error: function(xhr) {
                $('#customValuesContainer').html(`
                    <div class="alert alert-danger">Failed to load custom values. Please try again.</div>
                `);
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Load custom values immediately on page load
        loadCustomValues();

        // Set up reload button
        $('#reload-custom-values').on('click', function(e) {
            e.preventDefault();
            loadCustomValues();
        });

        // Handle location change
        $('#locations').on('change', function() {
            const locationSelected = $('#locations').val().trim() !== "";
            $('#getFieldsBtnWrapper').toggle(!locationSelected);
        });
    });
</script>
@endsection
