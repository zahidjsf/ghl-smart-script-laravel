@extends('frontpanel.layout.app')

@section('content')
@section('select_account_setting', 'active')

<div class="container-fluid" style="padding:0 1.5rem">
    <!-- start page title -->
    <div class="row g-0">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h2><strong>Location Connections</strong></h2>
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

                <div id="progress-container" style="display: none; margin-top: 10px;">
                    <div class="progress">
                        <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <p id="progress-text" class="mt-1">Processing locations 0/0</p>
                </div>

                <form id="location-form" action="{{ route('frontend.final-connect') }}" method="POST">
                    @csrf

                    <div class="form-group col-md-6 col-lg-3">
                        <button type="button" id="submit-btn" class="btn btn-primary">Connect Location</button>
                    </div>
                    <br>

                    <div class="row">
                        @foreach ($crmlocationID as $LocName => $LocId)
                            <div class="form-group col-md-6 col-lg-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input location-checkbox" id="loc{{ $LocId }}"
                                        name="connectLocations[]" value="{{ $LocId }}"
                                        {{ in_array($LocId, $alreadyConnected) ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                        for="loc{{ $LocId }}">{{ $LocName }}</label>
                                </div>
                            </div>
                            <br>
                            <br>
                        @endforeach
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
        const chunkSize = 1;
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
