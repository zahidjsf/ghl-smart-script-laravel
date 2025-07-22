<style>
    .select-with-search {
        position: relative;
        margin-bottom: 15px;
    }

    .select-with-search .select2-container {
        width: 100% !important;
    }

    .select-with-search .no-results {
        padding: 10px;
        color: #666;
        font-style: italic;
        text-align: center;
    }

    .select-with-search .loading {
        padding: 10px;
        color: #666;
        text-align: center;
    }
</style>

<div class="modal fade" id="addLocationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.add_location_data') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="add-location" method="POST" action="{{ route('frontend.smart_reward.locationAdd') }}">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $projectId }}">

                    @if ($showLocationSelect)
                    <div class="form-group select-with-search" id="select-div">
                        <label>{{ __('messages.select_location') }}</label>
                        <select id="selectLocation" name="sel_loc_id" class="form-control select2-locations" style="width: 100%">
                            <option value="">{{ __('messages.search_locations') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="password">{{ __('messages.password') }}</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="{{ __('messages.enter_password') }}">
                    </div>
                    @if ($projectId == 2)
                    <div class="form-group">
                        <label><input type="checkbox" name="add_promo_loc" value="yes" checked />{{ __('messages.auth_promo') }}</label>
                        <br /><small>{{ __('messages.auth_promo_desc') }}</small>
                    </div>

                    <div class="form-group">
                        <label><input type="checkbox" name="add_loyalty_loc" value="yes" />{{ __('messages.auth_loyalty') }}</label>
                        <br /><small>{{ __('messages.auth_loyalty_desc') }}</small>
                    </div>

                    <hr />
                    @endif

                    <div class='showManLoc'><a href='#' class="toggle-manual-location">{{ __('messages.add_location') }}</a></div>
                    <br>
                    <div class='manualLoc' style='display:none;'>
                        @endif
                        <div class="form-group">
                            {{ __('messages.previous_location') }}
                        </div>
                        <br>
                        <div class="form-group">
                            <label for='selectCurLoc'>{{ __('messages.prev_location') }}</label><br />
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
                            <label for="loc_id">{{ __('messages.loc_id') }}</label>
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
                        <span class="small">{{ __('messages.req_multi_loc') }}</span>
                    </div>
                    @endif

                    @if ($projectId == 2)
                    <div class="form-group" style="margin-top:15px;">
                        <label for="snapshot">{{ __('messages.ind_snap') }}</label><br />
                        <select name="snapshot" class="form-control">
                            <option value="base">{{ __('messages.orig_loyalty') }}</option>
                            <option value="restaurant">{{ __('messages.rest_reward') }}</option>
                            <option value="fitness">{{ __('messages.fitness_reward') }}</option>
                            <option value="salon">{{ __('messages.salon_spa_reward') }}</option>
                            <option value="hs">{{ __('messages.home_service_reward') }}</option>
                        </select><br />
                        <span class="small">{{ __('messages.current_snapshot') }}<br />
                            {{ __('messages.current_snapshot_desc') }}
                        </span>
                    </div>
                    @endif

                    <div class="modal-footer">
                        <button type="submit" name="insert" class="btn btn-primary add-location">{{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2 when modal is shown
        $(document).on('shown.bs.modal', '#addLocationModal', function() {
            // Destroy any existing Select2 instances
            if ($('#selectLocation').hasClass('select2-hidden-accessible')) {
                $('#selectLocation').select2('destroy');
            }

            // Initialize Select2 with proper configuration
            $('#selectLocation').select2({
                placeholder: "{{ __('messages.search_locations') }}",
                allowClear: true,
                minimumInputLength: 0,
                ajax: {
                    url: "{{ route('frontend.smart_reward.get_locations') }}",
                    dataType: 'json',
                    delay: 500,
                    data: function(params) {
                        return {
                            search: params.term,
                            skip: (params.page || 0) * 30
                        };
                    },
                    processResults: function(data) {
                        // Transform data into Select2 format
                        var results = $.map(data.locations || [], function(item) {
                            return {
                                id: item.id,
                                text: item.name
                            };
                        });

                        return {
                            results: results,
                            pagination: {
                                more: data.hasMore || false
                            }
                        };
                    },
                    cache: true
                }
            }).on('select2:open', function() {
                // Trigger search with empty term to load initial options
                $('.select2-search__field').val('').trigger('input');
            });

            // When a location is selected
            $('#selectLocation').on('change', function() {
                var data = $(this).select2('data')[0];
                if (data) {
                    $('#loc_name').val(data.text);
                    $('#loc_id').val(data.id);
                }
            });
        });

        // Toggle manual location form
        $(document).on('click', '.toggle-manual-location', function(e) {
            e.preventDefault();
            $('.manualLoc').toggle();
        });

        // When a previous location is selected
        $('#selectCurLoc').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            if (selectedOption.val()) {
                $('#loc_name').val(selectedOption.text());
                $('#loc_id').val(selectedOption.val());
            }
        });

        // Clean up when modal is closed
        $('#addLocationModal').on('hidden.bs.modal', function() {
            if ($('#selectLocation').hasClass('select2-hidden-accessible')) {
                $('#selectLocation').select2('destroy');
            }
        });
    });
</script>
