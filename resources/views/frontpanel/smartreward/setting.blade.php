<div class="modal fade" id="editSettingModal" tabindex="-1" role="dialog" aria-labelledby="modal-licenses-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update Location Data - {{ $location->name }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">
                <form class="update-location" method="POST" action="{{ route('frontend.smart_reward.settingUpdate') }}">
                    @csrf
                    <div class="settings">
                          <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="showSettings">
                                        <input type="checkbox" id="showSettings" @if($settings && property_exists($settings, 'showSettings') && $settings->showSettings == 'yes' ) checked @endif name="showSettings" value="yes">
                                        Enable Settings in Leaderboard</label><br/>
                                        <small>Display Reward Settings In Admin Rewards Nav</small>
                                    </div><br>
                                    <div class="form-group">
                                        <label for="showRewards">
                                        <input type="checkbox" id="showRewards" @if($settings && property_exists($settings, 'showRewards') && $settings->showRewards == 'yes' ) checked @endif name="showRewardsEditor" value="yes">
                                        Enable Rewards Editor in Leaderboard</label><br/>
                                        <small>Display Reward Item Editor In Admin Rewards Nav</small>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="showPoints">
                                        <input type="checkbox" id="showPoints" @if($settings && property_exists($settings, 'showPoints') && $settings->showPoints == 'yes' ) checked @endif name="showPoints" value="yes">
                                        Enable Points Leaderboard in Leaderboard</label><br/>
                                        <small>Display Loyalty Points Page In Admin Rewards Nav</small>
                                    </div><br>
                                    <div class="form-group">
                                        <label for="showPromotions">
                                        <input type="checkbox" id="showPromotions" @if($settings && property_exists($settings, 'showPromotions') && $settings->showPromotions == 'yes' ) checked @endif name="showPromotions" value="yes">
                                        Enable Promotions Manager in Leaderboard</label><br/>
                                        <small>Display Promotions Page In Admin Rewards Nav</small>
                                    </div>
                                    <br>

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="showReporting">
                                        <input type="checkbox" id="showReporting" @if($settings && property_exists($settings, 'showReporting') && $settings->showReporting == 'yes' ) checked @endif name="showReporting" value="yes">
                                        Enable Reporting in Leaderboard</label><br/>
                                        <small>Display Reporting Page In Rewards Nav</small>
                                    </div><br>
                                    <div class="form-group">
                                        <label for="showTiers">
                                        <input type="checkbox" id="showTiers" @if($settings && property_exists($settings, 'showTiers') && $settings->showTiers == 'yes' ) checked @endif name="showTiers" value="yes">
                                        Enable Tier Memberships in Leaderboard</label><br/>
                                        <small>Display Tiers Memberships In Leaderboard Nav</small>
                                    </div><br>
                                    <div class="form-group">
                                        <label for="showPromoPoints">
                                            <input type="checkbox"  id="showPromoPoints" @if($settings && property_exists($settings, 'showPromoPoints') && $settings->showPromoPoints == 'yes' ) checked @endif name="showPromoPoints" value="yes">
                                            Allow Promo Coupons To Award Loyalty Points</label><br/>
                                        <small> Display Loyalty Points Option In Promotions Manager</small>
                                    </div><br>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="rename_points">Rename "Points" to another word.</label>
                                        <input type="text" class="form-control" id="rename_points" name="renamePoints" value="{{ $settings->renamePoints ?? "" }}">
                                        <small class="form-text text-muted">Example: Use "Agency Bucks" instead of "Points" - you've just earned 100 Agency Bucks.</small>
                                    </div>
                                </div><br>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="points_value">How Many Points = 1 Dollar.</label>
                                        <input type="number" class="form-control" id="points_value" name="pointsValue" value="{LOCDATA.pointsValue}">
                                        <small class="form-text text-muted">What is the point value of 1 Dollar. IE: 1 dollar = 1 point or 1 dollar = 100 points<br/> Used for converting transactions</small>
                                    </div>
                                </div>
                                <br>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="form-label">Select Language For Leaderboard</label>
                                        <select class="form-select form-select-lg" name="language" id="">
                                            <option value="">Select one</option>
                                            <option value="english" @if($settings && property_exists($settings, 'language') &&  $settings->language == 'english' ?? '') selected @endif >English</option>
                                            <option value="spanish" @if($settings && property_exists($settings, 'language') && $settings->language == 'spanish' ?? '') selected @endif >Spanish (Coming Soon)</option>
                                        </select>
                                        <small class="form-text text-muted">This will set the language for the Rewards Leadboard Dashboard</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="form-label">Select Reward Template Layout</label>
                                        <select class="form-select form-select-lg" name="rewardsTemplate" id="">
                                            <option selected value="">Select one</option>
                                            @foreach($projOptions as $projOption)
                                            <option value="{{ $projOption->optVal }}" @if($settings && property_exists($settings, 'rewardsTemplate') && $projOption->optVal == $settings->rewardsTemplate ?? '') selected @endif > {{ $projOption->name }} </option>
                                            @endforeach

                                        </select>
                                        <small class="form-text text-muted">View Reward Templates</small>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group mt-3">
                                {{-- IF("{LOCDATA.vbg}" != ""){
                                    <div style="width:100%; margin:15px;"><img src="/assets/img/rewardimages/{LOCDATA.vbg}" width="100" /></div>
                                {:IF} --}}

                                <label for="logo">Change Voucher Background</label>
                                <input type="file" class="form-control-file" id="vbg" name="vbg">
                                <small id="helpId" class="form-text text-muted"><a href="https://www.canva.com/design/DAF_mFrv75c/HQLJNz-LJSq067wg6CPCfw/view?utm_content=DAF_mFrv75c&utm_campaign=designshare&utm_medium=link&utm_source=publishsharelink&mode=preview" target="_blank">Get Background Template Here.</a></small>
                            </div>

                            <input type='hidden' name='locid' value='{{ $location->id ?? '' }}' />
                            <input type='hidden' name='settingId' value='{{ $settingRec->id ?? '' }}' />

                            <div class="modal-footer">
                                <button type="submit" onclick="subLocLoader()" name="update" class="btn btn-primary">Update Settings</button>
                                <span class='sublocLoader' style="display:none;">
                                <img src="https://ghlsmart.s3.us-west-2.amazonaws.com/smart-images/smallLoading.gif" width="24" height="24">&nbsp;
                                Updating Settings...</span>
                            </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('js-script-add')
<script>
    $(document).ready(function () {
        // Your JavaScript code here
    });
</script>
@endsection
