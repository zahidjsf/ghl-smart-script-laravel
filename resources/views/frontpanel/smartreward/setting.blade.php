<div class="modal fade" id="editSettingModal" tabindex="-1" role="dialog" aria-labelledby="modal-licenses-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('messages.update_location_data') }} - {{ $location->name }}</h4>
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
                                    {{ __('messages.enable_settings') }}</label><br/>
                                    <small>{{ __('messages.settings_nav_description') }}</small>
                                </div><br>
                                <div class="form-group">
                                    <label for="showRewards">
                                    <input type="checkbox" id="showRewards" @if($settings && property_exists($settings, 'showRewards') && $settings->showRewards == 'yes' ) checked @endif name="showRewardsEditor" value="yes">
                                    {{ __('messages.enable_rewards_editor') }}</label><br/>
                                    <small>{{ __('messages.rewards_editor_nav_description') }}</small>
                                </div>
                                <br>
                                <div class="form-group">
                                    <label for="showPoints">
                                    <input type="checkbox" id="showPoints" @if($settings && property_exists($settings, 'showPoints') && $settings->showPoints == 'yes' ) checked @endif name="showPoints" value="yes">
                                    {{ __('messages.enable_points_leaderboard') }}</label><br/>
                                    <small>{{ __('messages.points_nav_description') }}</small>
                                </div><br>
                                <div class="form-group">
                                    <label for="showPromotions">
                                    <input type="checkbox" id="showPromotions" @if($settings && property_exists($settings, 'showPromotions') && $settings->showPromotions == 'yes' ) checked @endif name="showPromotions" value="yes">
                                    {{ __('messages.enable_promotions_manager') }}</label><br/>
                                    <small>{{ __('messages.promotions_nav_description') }}</small>
                                </div>
                                <br>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="showReporting">
                                    <input type="checkbox" id="showReporting" @if($settings && property_exists($settings, 'showReporting') && $settings->showReporting == 'yes' ) checked @endif name="showReporting" value="yes">
                                    {{ __('messages.enable_reporting') }}</label><br/>
                                    <small>{{ __('messages.reporting_nav_description') }}</small>
                                </div><br>
                                <div class="form-group">
                                    <label for="showTiers">
                                    <input type="checkbox" id="showTiers" @if($settings && property_exists($settings, 'showTiers') && $settings->showTiers == 'yes' ) checked @endif name="showTiers" value="yes">
                                    {{ __('messages.enable_tier_memberships') }}</label><br/>
                                    <small>{{ __('messages.tiers_nav_description') }}</small>
                                </div><br>
                                <div class="form-group">
                                    <label for="showPromoPoints">
                                        <input type="checkbox"  id="showPromoPoints" @if($settings && property_exists($settings, 'showPromoPoints') && $settings->showPromoPoints == 'yes' ) checked @endif name="showPromoPoints" value="yes">
                                        {{ __('messages.allow_promo_points') }}</label><br/>
                                    <small>{{ __('messages.promo_points_description') }}</small>
                                </div><br>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rename_points">{{ __('messages.rename_points_label') }}</label>
                                    <input type="text" class="form-control" id="rename_points" name="renamePoints" value="{{ $settings->renamePoints ?? "" }}">
                                    <small class="form-text text-muted">{{ __('messages.rename_points_description') }}</small>
                                </div>
                            </div><br>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="points_value">{{ __('messages.points_value_label') }}</label>
                                    <input type="number" class="form-control" id="points_value" name="pointsValue" value="{LOCDATA.pointsValue}">
                                    <small class="form-text text-muted">{{ __('messages.points_value_description') }}</small>
                                </div>
                            </div>
                            <br>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="form-label">{{ __('messages.select_language_label') }}</label>
                                    <select class="form-select form-select-lg" name="language" id="">
                                        <option value="">{{ __('messages.select_one') }}</option>
                                        <option value="english" @if($settings && property_exists($settings, 'language') &&  $settings->language == 'english' ?? '') selected @endif >{{ __('messages.english') }}</option>
                                        <option value="spanish" @if($settings && property_exists($settings, 'language') && $settings->language == 'spanish' ?? '') selected @endif >{{ __('messages.spanish') }}</option>
                                    </select>
                                    <small class="form-text text-muted">{{ __('messages.language_description') }}</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="form-label">{{ __('messages.select_template_label') }}</label>
                                    <select class="form-select form-select-lg" name="rewardsTemplate" id="">
                                        <option selected value="">{{ __('messages.select_one') }}</option>
                                        @foreach($projOptions as $projOption)
                                        <option value="{{ $projOption->optVal }}" @if($settings && property_exists($settings, 'rewardsTemplate') && $projOption->optVal == $settings->rewardsTemplate ?? '') selected @endif > {{ $projOption->name }} </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">{{ __('messages.template_description') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="logo">{{ __('messages.change_voucher_bg') }}</label>
                            <input type="file" class="form-control-file" id="vbg" name="vbg">
                            <small id="helpId" class="form-text text-muted"><a href="https://www.canva.com/design/DAF_mFrv75c/HQLJNz-LJSq067wg6CPCfw/view?utm_content=DAF_mFrv75c&utm_campaign=designshare&utm_medium=link&utm_source=publishsharelink&mode=preview" target="_blank">{{ __('messages.get_template_link') }}</a></small>
                        </div>

                        <input type='hidden' name='locid' value="{{ $location->id ?? '' }}" />
                        <input type='hidden' name='settingId' value="{{ $settingRec->id ?? '' }}" />

                        <div class="modal-footer">
                            <button type="submit" onclick="subLocLoader()" name="update" class="btn btn-primary">{{ __('messages.update_settings') }}</button>
                            <span class='sublocLoader' style="display:none;">
                            <img src="https://ghlsmart.s3.us-west-2.amazonaws.com/smart-images/smallLoading.gif" width="24" height="24">&nbsp;
                            {{ __('messages.updating_settings') }}</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
