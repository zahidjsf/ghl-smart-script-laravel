<div class="modal fade" id="editDetailsModal" tabindex="-1" role="dialog" aria-labelledby="modal-licenses-label"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update Location Data - {{ $location->name }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">
                <form class="update-location" method="POST"
                    action="{{ route('frontend.smart_reward.locationUpdate') }}">
                    @csrf
                    <div class="form-group">
                        <input type="hidden" value="{{ $location->id }}" name="loc_id">
                        <label for="licenses-{{ $location->id }}">Name</label>
                        <input type="text" class="form-control" id="licenses-{{ $location->id }}" name="name"
                            value="{{ $location->name }}">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary update-location">Save changes</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>


@section('js-script-add')
    <script>
        $(document).ready(function() {

        });
    </script>
@endsection
