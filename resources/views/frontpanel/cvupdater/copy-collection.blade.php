<div class="modal fade" id="duplicateLocationModal" tabindex="-1" role="dialog" aria-labelledby="modal-licenses-label"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('messages.duplicate') }} - {{ $collection->name }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">
                <form class="update-location" method="POST"
                    action="{{ route('frontend.smart_reward.duplicatecollection') }}">
                    @csrf

                    <input type="hidden" name="col_desc" value="{{  $collection->description ?? '' }}">
                    <input type="hidden" name="locations" value="{{  $collection->orig_loc_id }}">
                    <input type="hidden" name="cf_loc" value="{{  $collection->cf_loc_id }}">
                    <input type="hidden" name="col" value="{{ $collection->id }}">
                    <input type="hidden" name="aid" value="{{ $collection->a_id }}">

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary update-location">Save changes</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
