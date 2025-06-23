<div class="modal fade" id="addLocationModal" tabindex="-1" role="dialog" aria-labelledby="modal-location-add-label"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('messages.add_location_data') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">
                <form class="add-location" method="POST" action="{{ route('frontend.smart_reward.locationAdd') }}">
                    @csrf


                    <input type="hidden" name="project_id" value="{{ $projectId }}">

                    @if ($showLocationSelect)
                    <div class="form-group" id="select-div">
                        <label for='selectLocation'>{{ __('messages.select_location') }}</label><br />
                        <select id='selectLocation' name='sel_loc_id' class="form-control">
                            <option>{{ __('messages.select_location') }}</option>
                            @foreach ($agencyLocations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <br>
                    @if ($projectId == 2)
                    <div class="form-group">
                        <label><input type="checkbox" name="add_promo_loc" value="yes" checked />{{ __('messages.auth_promo') }}</label>
                        <br /><small>{{ __('messages.auth_promo_desc') }}</small>
                    </div>
                    <br>

                    <div class="form-group">
                        <label><input type="checkbox" name="add_loyalty_loc" value="yes" />{{ __('messages.auth_loyalty') }}</label>
                        <br /><small>{{ __('messages.auth_loyalty_desc') }}</small>
                    </div>
                    <br>

                    <hr />
                    @endif

                    <div class='showManLoc'><a href='#' onclick='showManLoc();'>{{ __('messages.add_location') }}</a></div>
                    <br>
                    <div class='manualLoc' style='display:none;'>
                        @endif

                        <div class="form-group">
                            {{ __('messages.previous_location') }}

                        </div>
                        <br>

                        <div class="form-group">
                            <label for='selectCurLoc'> {{ __('messages.prev_location') }}</label><br />
                            <select id='selectCurLoc' name='selectCurLoc' class="form-control">
                                <option>{{ __('messages.select_location') }}</option>
                                @foreach ($currentLocations as $loc)
                                <option value="{{ $loc->loc_id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <br>

                        <div class="form-group">
                            <label for="loc_name">{{ __('messages.location_name') }}</label>
                            <input type="text" class="form-control" id="loc_name" name="loc_name" value="">
                            <label for="loc_id"> {{ __('messages.loc_id') }}</label>
                            <input type="text" class="form-control" id="loc_id" name="loc_id" value="">
                        </div>
                        <br>

                        @if ($showLocationSelect)
                    </div>
                    @endif

                    @if ($projectId == 1)
                    <div class="form-group">
                        <input type="checkbox" class="form-group" id="ml" value="yes" name="ml">
                        <label for="ml">{{ __('messages.multi_loc') }}<br /></label>
                        <span class="small">{{ __('messages.req_multi_loc') }} </span>
                    </div>
                    <br>
                    @endif

                    @if ($projectId == 2)
                    <div class="form-group" style="margin-top:15px;">
                        <label for="snapshot">{{ __('messages.ind_snap') }}</label><br />
                        <select name="snapshot" class="form-control">
                            <option value="base">{{ __('messages.orig_loyalty') }} </option>
                            <option value="restaurant">{{ __('messages.rest_reward') }} </option>
                            <option value="fitness">{{ __('messages.fitness_reward') }} </option>
                            <option value="salon">{{ __('messages.salon_spa_reward') }} </option>
                            <option value="hs">{{ __('messages.home_service_reward') }}</option>
                        </select><br />
                        <span class="small">{{ __('messages.current_snapshot') }}<br />
                        {{ __('messages.current_snapshot_desc') }}
                        </span>
                    </div>
                    @endif
                    <br>

                    <div class="modal-footer">
                        <button type="submit" name="insert" onclick="LocLoader()" class="btn btn-primary add-location">{{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
