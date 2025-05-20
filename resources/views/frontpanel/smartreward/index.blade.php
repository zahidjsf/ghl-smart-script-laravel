@extends('frontpanel.layout.app')

@section('content')
@section('smart_apps', 'active')

<div class="container-fluid" style="padding:0 1.5rem">
    <!-- start page title -->
    <div class="row g-0">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h2><strong>Smart Rewards</strong></h2>
            </div>
        </div>
    </div>
    <hr>

    <div class="alert alert-success" id="success-msg" hidden>
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

    <p>{{ $projdetail->description ?? '' }}</p>

    <div class="d-flex justify-content-between align-items-center border p-3 rounded">
        <div class="fw-bold fs-5">
            {!! $licenseMsg !!}
        </div>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">Add Custom
                CSS</button>
            <button class="btn btn-secondary me-2" onclick="toggleCode();">Get LeaderBoard Link</button>
            <button class="btn btn-success me-2" onclick="toggleSnapshots()">Get Snapshot</button>
            <a href="{{ route('frontend.smart_reward.addlocations') }}" class="location-add-modal btn btn-danger"> Add a
                Rewards Location </a>
        </div>
    </div>

    @verbatim
        <div class="form-group center codesection" style="display:none; margin:0;">
            <label>Leaderboard Custom Menu Link:</label>
            <small class="form-text text-muted">Paste this link into custom menu links to display the Rewards
                Leaderboard</small>

            <div class="input-group mt-2">
                <input type="text" class="form-control" id="leaderboardLink" readonly
                    value="https://api.ghlsmartscripts.com/Rewards/getRewardsLeaderboardData.php?location={{ location . id }}">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" onclick="copyLeaderboardLink()">Copy</button>
                </div>
            </div>
        </div>
    @endverbatim

    <div class="col-lg-12 center snapshotSection" style="display:none; margin:0 0 15px 0;">
        <div class="col-md-4">
            <h4>Snapshot links:</h4>
            <div><a class="btn btn-success " target="_blank"
                    href="https://affiliates.gohighlevel.com/?fp_ref=think-big-studios57&share=sb6K1JZOMgYkI1Jcmegw">Original
                    Snapshot (Service Businesses)</a></div>
            <br />
            <div><a class="btn btn-primary " target="_blank"
                    href="https://affiliates.gohighlevel.com/?fp_ref=think-big-studios57&share=AQGzYoeDlS9dNpKO4Lnn">Restaurant
                    Rewards & Marketing (February 2025)</a></div>
            <br />
            <div><a class="btn btn-warning " style="background-color: rgb(230, 54, 54);" target="_blank"
                    href="https://affiliates.gohighlevel.com/?fp_ref=think-big-studios57&share=1WcZPiNjg3RHy2rpR7VV">Salon
                    Snapshot/Med Spa (June 14 2024)</a></div>
            <br />
            <div><a class="btn btn-success" style="background-color: rgb(255, 115, 0);" target="_blank"
                    href="https://affiliates.gohighlevel.com/?fp_ref=think-big-studios57&share=c7r92eZG1uOorH7jwyRT">Fitness
                    Marketing</a></div>
        </div>
        <div class="col-md-4">

        </div>
    </div>


    <br>

    <div class="card">
        <div class="card-body">
            <div class="content-block">

                <table class="table table-bordered" id="locations-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

    <div id="locationupdate-modal"></div>
    <div id="setting-update-modal"></div>
    <div id="locationadd-modal"></div>

    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Add Custom CSS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('frontend.smart_reward.savecss') }}" method="POST">
                    @csrf

                    <div class="modal-body">
                        <strong> WARNING:</strong><br>If your CSS Is not formatter properly, it can break the
                        functionality of the pages is is on.
                        <hr>
                        <strong>Leaderboard CSS:</strong>
                        <p>Add custom CSS to update the look of the leaderboard.</p>
                        <textarea name="leaderboard_css" cols="70" rows="10">{{ $setting->rewards_css ?? '' }}</textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


</div>
@endsection

<script>
    function toggleCode() {
        $('.codesection').toggle();
    }

    function toggleSnapshots() {
        $('.snapshotSection').toggle();
    }

    function copyLeaderboardLink() {
        const copyText = document.getElementById("leaderboardLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices
        document.execCommand("copy");

        // Optional feedback
        alert("Copied to clipboard!");
    }

    function showManLoc() {
        $('.manualLoc').toggle();
    }

    function LocLoader() {
        $('.LocLoader').show();
    }
</script>

@section('js-script-add')
<script>
    $(function() {
        $('#locations-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('frontend.smart_reward.getlocations') }}",
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });


        $(document).on('click', '.location-add-modal', function(e) {

            e.preventDefault();
            // let url = $(this).data('url');
            let url = $(this).attr('href');

            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    // Inject the modal content
                    $('#locationadd-modal').html(response.view);

                    // Show the modal
                    $('#addLocationModal').modal('show');
                },
                error: function(xhr) {
                    alert('Error loading modal content');
                    console.error(xhr.responseText);
                }
            });
        });







        // Use event delegation for dynamically loaded elements
        $(document).on('click', '.load-license-modal', function(e) {

            e.preventDefault();
            let url = $(this).data('url');

            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    // Inject the modal content
                    $('#locationupdate-modal').html(response.view);

                    // Show the modal
                    $('#editDetailsModal').modal('show');
                },
                error: function(xhr) {
                    alert('Error loading modal content');
                    console.error(xhr.responseText);
                }
            });
        });


        $(document).on('click', '.remove-location', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {


                    let url = $(this).data('url');

                    $.ajax({
                        url: url,
                        method: 'GET',
                        success: function(response) {
                            if (response.status == 'success') {

                                var table = $('#locations-table').DataTable();
                                table.draw();

                                var msg = document.getElementById("success-msg");
                                msg.removeAttribute('hidden');
                                msg.innerText = response.message;

                            }
                        },
                        error: function(xhr) {
                            alert('Error loading modal content');
                            console.error(xhr.responseText);
                        }
                    });




                }
            });




        });



        // Use event delegation for dynamically loaded elements
        $(document).on('click', '.load-setting-modal', function(e) {

            e.preventDefault();
            let url = $(this).data('url');

            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    // Inject the modal content
                    $('#setting-update-modal').html(response.view);

                    // Show the modal
                    $('#editSettingModal').modal('show');
                },
                error: function(xhr) {
                    alert('Error loading modal content');
                    console.error(xhr.responseText);
                }
            });
        });



    });


    $('.update-location').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const formData = new FormData(this);
        const submitButton = form.find('button[type="submit"]');

        submitButton.prop('disabled', true).text('Saving...');

        $.ajax({
            url: "{{ route('frontend.smart_reward.locationUpdate') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Close the appropriate modal

                $('#modal-edit-details').modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message || 'Location updated successfully!',
                    timer: 4000,
                    showConfirmButton: false
                });

                setTimeout(() => {
                    location.reload();
                }, 2000);

            },
            error: function(xhr) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong. Please try again.',
                });
            },
            complete: function() {
                submitButton.prop('disabled', false).text('Save changes');
            }
        });
    });
</script>
@endsection
