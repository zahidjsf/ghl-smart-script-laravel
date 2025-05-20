@if ($type == 'edit')
    <div class="modal fade" id="modal-licenses-{{ $systemProject->id }}" tabindex="-1" role="dialog" aria-labelledby="modal-licenses-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Licenses for {{ $systemProject->name }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <form class="license-form" data-project-id="{{ $systemProject->id }}">
                        @csrf
                        <div class="form-group">
                            <input type="hidden" value="{{ $projLicense->a_id }}" name="acc_id">
                            <input type="hidden" value="{{ $projLicense->proj_id }}" name="proj_id">
                            <input type="hidden" value="{{ $projLicense->id }}" name="proj_lic_id">
                            <label for="licenses-{{ $systemProject->id }}">Number of Licenses</label>
                            <input type="number" class="form-control" id="licenses-{{ $systemProject->id }}" name="licenses" value="{{ $numLicenses }}">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@else
    <div class="modal fade" id="modal-addlicenses-{{ $projId }}" tabindex="-1" role="dialog" aria-labelledby="modal-addlicenses-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Licenses for {{ $systemProject->name }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <form class="license-form" data-project-id="{{ $projId }}">
                    @csrf
                    <div class="form-group">
                        <input type="hidden" value="{{ $accID }}" name="acc_id">
                        <input type="hidden" value="{{ $projId }}" name="proj_id">
                        <label for="add-licenses-{{ $projId }}">Number of Licenses</label>
                        <input type="number" class="form-control" id="add-licenses-{{ $projId }}" name="licenses" value="0">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add Licenses</button>
                    </div>
                </form>

                </div>

            </div>
        </div>
    </div>
@endif

<script>
$(document).ready(function () {
    $('.license-form').on('submit', function (e) {
        e.preventDefault();

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
                // Close the appropriate modal

                $('#modal-licenses-' + projectId).modal('hide');
                $('#modal-addlicenses-' + projectId).modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message || 'Licenses updated successfully!',
                    timer: 4000,
                    showConfirmButton: false
                });

                setTimeout(() => {
                    location.reload();
                }, 2000);

            },
            error: function (xhr) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong. Please try again.',
                });
            },
            complete: function () {
                submitButton.prop('disabled', false).text('Save changes');
            }
        });
    });
});
</script>
