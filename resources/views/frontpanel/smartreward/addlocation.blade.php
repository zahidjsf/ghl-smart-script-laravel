<div class="modal fade" id="addLocationModal" tabindex="-1" role="dialog" aria-labelledby="modal-location-add-label"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Location Data</h4>
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
                            <label for='selectLocation'>Select Location Id</label><br />
                            <select id='selectLocation' name='sel_loc_id' class="form-control">
                                <option>Select A Location</option>
                                @foreach ($agencyLocations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <br>
                        @if ($projectId == 2)
                            <div class="form-group">
                                <label><input type="checkbox" name="add_promo_loc" value="yes" checked /> Authorize
                                    Promotions</label>
                                <br /><small>Add Selected Location To The Promotions App</small>
                            </div>
                            <br>

                            <div class="form-group">
                                <label><input type="checkbox" name="add_loyalty_loc" value="yes" /> Authorize Loyalty
                                    Stamp
                                    Cards</label>
                                <br /><small>Add Selected Location To The Loyalty Stamp Cards App</small>
                            </div>
                            <br>

                            <hr />
                        @endif

                        <div class='showManLoc'><a href='#' onclick='showManLoc();'>Add Location Manually - Click
                                To Show
                                Form.</a></div>
                        <br>
                        <div class='manualLoc' style='display:none;'>
                    @endif

                    <div class="form-group">
                        Select a previously added location, or add a new location via API & Location ID.
                    </div>
                    <br>

                    <div class="form-group">
                        <label for='selectCurLoc'>Choose A Previously Added Location</label><br />
                        <select id='selectCurLoc' name='selectCurLoc' class="form-control">
                            <option>Select A Location</option>
                            @foreach ($currentLocations as $loc)
                                <option value="{{ $loc->loc_id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <br>

                    <div class="form-group">
                        <label for="loc_name">Location Name</label>
                        <input type="text" class="form-control" id="loc_name" name="loc_name" value="">
                        <label for="loc_id">Location Id</label>
                        <input type="text" class="form-control" id="loc_id" name="loc_id" value="">
                    </div>
                    <br>

                    @if ($showLocationSelect)
            </div>
            @endif

            @if ($projectId == 1)
                <div class="form-group">
                    <input type="checkbox" class="form-group" id="ml" value="yes" name="ml">
                    <label for="ml">Is this a multi-location review site?<br /></label>
                    <span class="small">(requires Multi Location Snapshot)</span>
                </div>
                <br>
            @endif

            @if ($projectId == 2)
                <div class="form-group" style="margin-top:15px;">
                    <label for="snapshot">Select Industry Snapshot</label><br />
                    <select name="snapshot" class="form-control">
                        <option value="base">Original Loyalty & Rewards</option>
                        <option value="restaurant">Restaurant Rewards & Promotions</option>
                        <option value="fitness">Fitness Rewards & Marketing</option>
                        <option value="salon">Salon / Spa Rewards & Marketing</option>
                        <option value="hs">Home Services - Rewards, Referrals & Promotions</option>
                    </select><br />
                    <span class="small">Which snapshot do you plan on using?<br />This will not install the
                        snapshot, but will set up our editor for your plans.</span>
                </div>
            @endif
            <br>

            <div class="modal-footer">
                <button type="submit" name="insert" onclick="LocLoader()" class="btn btn-primary add-location">Save
                    changes</button>
            </div>
            </form>
        </div>

    </div>
</div>
</div>
