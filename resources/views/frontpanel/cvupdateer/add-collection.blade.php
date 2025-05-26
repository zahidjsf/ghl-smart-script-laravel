@extends('frontpanel.layout.app')

@section('content')
@section('smart_apps', 'active')

<div class="container-fluid" style="padding:0 1.5rem">
    <!-- start page title -->
    <div class="row g-0">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h2><strong>Create New Collection</strong></h2>
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
                <label for='locations'>Select Location To Grab Custom Values From</label><br />
                <select id='locations' name='locations' class="form-control">
                    <option value="">Select A Location</option>
                    @foreach ($agencyLocations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for='cf_loc'>Grab Custom Fields From Another Location</label><br />
                <select id='cf_loc' name='cf_loc' class="form-control">
                    <option value="">Select A Location</option>
                    @foreach ($agencyLocations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <br>
        <div class="form-group text-end" id="getFieldsBtnWrapper" hidden>
            <a class="btn btn-primary" id="get-custom-fields">Get Custom Fields</a>
        </div>
        <div class="form-group">
            <label for="col_name">Collection Name</label>
            <input type="text" class="form-control" id="col_name" name="col_name" value="" placeholder="Enter Collection Name">
        </div>
        <br>
        <div class="form-group">
            <label for="textarea" class=" control-label">Description</label>
            <textarea name="col_desc" id="textarea" class="form-control" rows="3"></textarea>
        </div>
        <br>
        <div class="modal-footer">
            <button type="submit" name="insert" class="btn btn-primary add-location">Save
                changes</button>
        </div>
    </form>


</div>
@endsection

@section('js-script-add')
<script>
    const locationSelect = document.getElementById('locations');
    const cfLocSelect = document.getElementById('cf_loc');
    const buttonWrapper = document.getElementById('getFieldsBtnWrapper');

    function toggleButtonVisibility() {
        const locValue = locationSelect.value;
        const cfLocValue = cfLocSelect.value;
        if (locValue && cfLocValue && locValue !== cfLocValue) {
            buttonWrapper.hidden = false;
        } else {
            buttonWrapper.hidden = true;
        }
    }

    function preventSameSelection(changedSelect, otherSelect) {
        const selectedValue = changedSelect.value;
        for (let option of otherSelect.options) {
            option.disabled = (option.value === selectedValue && selectedValue !== "");
        }
    }
    locationSelect.addEventListener('change', () => {
        preventSameSelection(locationSelect, cfLocSelect);
        toggleButtonVisibility();
    });
    cfLocSelect.addEventListener('change', () => {
        preventSameSelection(cfLocSelect, locationSelect);
        toggleButtonVisibility();
    });
</script>
@endsection
