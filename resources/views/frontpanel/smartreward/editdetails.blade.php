<div class="modal fade" id="editDetailsModal" tabindex="-1" role="dialog" aria-labelledby="modal-licenses-label"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('messages.update_loc_data') }} - {{ $location->name }}</h4>
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
                        <label for="licenses-{{ $location->id }}">{{ __('messages.name') }}</label>
                        <input type="text" class="form-control" id="licenses-{{ $location->id }}" name="name" value="{{ $location->name }}">
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="email-{{ $location->id }}">Email</label>
                        <input type="text" readonly class="form-control" id="email-{{ $location->id }}" name="email" value="{{ $location->email }}" placeholder="Email readonly">
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="password-{{ $location->id }}">Password</label>
                        <input type="password" class="form-control" id="password-{{ $location->id }}" name="password" value="" placeholder="Leave empty if want the same password">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary update-location">{{ __('messages.save') }}</button>
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
