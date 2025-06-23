@extends('frontpanel.layout.app')

@section('content')
@section('select_account_setting', 'active')

<div class="container-fluid" style="padding:0 1.5rem">
    <!-- start page title -->
    <div class="row g-0">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h2><strong>{{ __('messages.location_connection') }}</strong></h2>
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

                <div id="loading-locations" class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">{{ __('messages.loading_loc') }}</span>
                    </div>
                    <p>{{ __('messages.loading_loc') }}</p>
                </div>

                <div id="progress-container" style="display: none; margin-top: 10px;">
                    <div class="progress">
                        <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <p id="progress-text" class="mt-1">Processing locations 0/0</p>
                </div>

                <form id="location-form" action="{{ route('frontend.final-connect') }}" method="POST">
                    @csrf

                    <div class="form-group col-md-6 col-lg-3">
                        <button type="button" id="submit-btn" class="btn btn-primary" disabled>{{ __('messages.connect_location') }}</button>
                    </div>
                    <br>

                    <div id="locations-container" class="row">
                        <!-- Locations will be loaded here via AJAX -->
                    </div>

                    <br>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-script-add')
<script>
$(document).ready(function() {
    let allLocations = [];
    let alreadyConnected = @json($alreadyConnected);
    let isLoading = false;
    let hasMore = true;
    let skip = 0;
    const limit = 100;

    // Function to fetch locations in chunks
    function fetchLocations(skip1 = 0) {
        console.log(skip1);
        if (isLoading || !hasMore) return;
        isLoading = true;
        $.ajax({
            url: '{{ route("frontend.fetch-locations") }}',
            type: 'GET',
            data: { skip: skip1 },
            success: function(response) {
                if (response.locations.length > 0) {
                    // Process the locations
                    response.locations.forEach(location => {
                        allLocations.push({
                            id: location.id,
                            name: location.name
                        });
                        // Add location checkbox to the container
                        const isChecked = alreadyConnected.includes(location.id) ? 'checked' : '';
                        $('#locations-container').append(`
                            <div class="form-group col-md-6 col-lg-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input location-checkbox"
                                           id="loc${location.id}" name="connectLocations[]"
                                           value="${location.id}" ${isChecked}>
                                    <label class="form-check-label" for="loc${location.id}">${location.name}</label>
                                </div>
                            </div>
                        `);
                    });

                    skip = response.nextSkip;
                    hasMore = response.hasMore;

                    // If there are more locations, fetch the next chunk
                    if (hasMore) {
                        isLoading = false;
                        fetchLocations(skip);
                    } else {
                        // All locations loaded
                        $('#loading-locations').hide();
                        $('#submit-btn').prop('disabled', false);
                    }
                } else {
                    // No more locations
                    hasMore = false;
                    $('#loading-locations').hide();
                    $('#submit-btn').prop('disabled', false);
                }
                isLoading = false;
            },
            error: function() {
                isLoading = false;
                $('#loading-locations').html('<p class="text-danger">Error loading locations. Please refresh the page.</p>');
            }
        });
    }
    // Start loading locations
    fetchLocations();
    // Your existing submit button code
    $('#submit-btn').click(function() {
        // Get all checked locations
        const checkedLocations = $('.location-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (checkedLocations.length === 0) {
            alert('Please select at least one location');
            return;
        }

        // Show progress container
        $('#progress-container').show();
        $('#submit-btn').prop('disabled', true);

        // Process in chunks of 10
        const chunkSize = 10;
        const totalChunks = Math.ceil(checkedLocations.length / chunkSize);
        let processedChunks = 0;
        let successfulSubmissions = 0;

        // Update progress bar
        function updateProgress(current, total) {
            const percent = Math.round((current / total) * 100);
            $('#progress-bar').css('width', percent + '%');
            $('#progress-text').text(`Processing locations ${current * chunkSize}/${checkedLocations.length}`);
        }

        // Process each chunk
        function processChunk(chunkIndex) {
            const start = chunkIndex * chunkSize;
            const end = start + chunkSize;
            console.log('start = ' + start + 'end = ' + end);
            const chunk = checkedLocations.slice(start, end);

            $.ajax({
                url: '{{ route("frontend.final-connect") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    connectLocations: chunk,
                    checkedLocations: checkedLocations,
                    start: start
                },
                success: function(response) {
                    successfulSubmissions++;
                    processedChunks++;
                    updateProgress(processedChunks, totalChunks);

                    if (processedChunks < totalChunks) {
                        processChunk(processedChunks);
                    } else {
                        // All chunks processed
                        if (successfulSubmissions === totalChunks) {
                            window.location.reload();
                        } else {
                            alert('Some locations failed to connect. Please try again.');
                            $('#submit-btn').prop('disabled', false);
                        }
                    }
                },
                error: function(xhr) {
                    processedChunks++;
                    updateProgress(processedChunks, totalChunks);

                    if (processedChunks < totalChunks) {
                        processChunk(processedChunks);
                    } else {
                        alert(successfulSubmissions > 0 ?
                            'Some locations connected successfully, but some failed.' :
                            'Failed to connect locations. Please try again.');
                        $('#submit-btn').prop('disabled', false);
                    }
                }
            });
        }

        // Start processing
        updateProgress(0, totalChunks);
        processChunk(0);
    });
});
</script>

<style>
.progress {
    height: 20px;
    margin-bottom: 5px;
}
</style>
@endsection
