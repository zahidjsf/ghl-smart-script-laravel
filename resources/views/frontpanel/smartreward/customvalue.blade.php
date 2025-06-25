@extends('frontpanel.layout.app')

@section('content')
@section('smart_apps', 'active')

<div class="container-fluid" style="padding:0 1.5rem">
    <!-- start page title -->
    <div class="row g-0">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h2><strong>{{ __('messages.smart_rewards') }}</strong></h2>
                <div>
                    <a href="javascript:history.back()" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> {{ __('messages.go_back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

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

    <div class="col-lg-12 mb-3">
        @if(session('msg'))
        <div class="col-lg-12 alert alert-info text-center mb-3">
            {{ session('msg') }}
        </div>
        @endif
        <div class="card">
            <div class="card-body">
                <form id="customValuesForm" action="{{ route('frontend.smart_reward.custom_values_update', $location->id) }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    <div class="d-flex justify-content-end mb-3">
                        <button type="submit" name="submit" class="btn btn-primary btn-lg fw-bold">{{ __('messages.update_cv') }}</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="w-25">{{ __('messages.name') }}</th>
                                    <th>{{ __('messages.value') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inputs as $input)
                                <tr>
                                    <td>
                                        <div class="mb-2">
                                            <strong>{{ $input['name'] }}</strong> {{ $input['readonly'] ?? '' }}
                                        </div>
                                        <button class="btn btn-sm btn-outline-info" type="button" data-bs-toggle="collapse" data-bs-target="#descToggle{{ $loop->index }}" aria-expanded="false" aria-controls="descToggle{{ $loop->index }}">
                                            <i class="fas fa-question-circle"></i> Info
                                        </button>
                                        <div class="collapse mt-2" id="descToggle{{ $loop->index }}">
                                            <div class="card card-body bg-light">
                                                {{ $input['tooltip'] }}<br />{{ $input['resources'] ?? '' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($input['fieldtype'] == "logo")
                                        <div class="mb-3">
                                            @if(($input['showImage'] ?? '') == "yes")
                                            <div class="mb-2">
                                                <img src="{{ $input['val'] }}" class="img-thumbnail" style="max-height: 100px;">
                                            </div>
                                            @endif
                                            <input type="text" class="form-control mb-2" name="IMAGE-{{ $input['id'] }}||{{ $input['name'] }}" id="{{ $input['id'] }}" value="{{ $input['val'] }}">
                                            <label class="form-label">{{ __('messages.select_image') }}</label>
                                            <input type="file" class="form-control" name="{{ $input['id'] }}||{{ $input['name'] }}" id="{{ $input['id'] }}">
                                        </div>
                                        @elseif($input['fieldtype'] == "revLogo")
                                        <div class="mb-3">
                                            @if(($input['showImage'] ?? '') == "yes")
                                            <div class="mb-2">
                                                <img src="{{ $input['val'] }}" class="img-thumbnail" style="max-height: 100px;">
                                            </div>
                                            @endif
                                            <input type="text" class="form-control mb-2" name="IMAGE-{{ $input['id'] }}||{{ $input['name'] }}" id="{{ $input['id'] }}" value="{{ $input['val'] }}">
                                            <div class="row mb-2">
                                                <div class="col-md-6">
                                                    <label class="form-label"> {{ __('messages.review_site') }}</label>
                                                    <select class="form-select" name="ALT-{{ $input['id'] }}||{{ $input['name'] }}">
                                                        <option value="">Select...</option>
                                                        {!! $input['revOptions'] ?? '' !!}
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ __('messages.select_image') }}</label>
                                                    <input type="file" class="form-control" name="{{ $input['id'] }}||{{ $input['name'] }}" id="{{ $input['id'] }}">
                                                </div>
                                            </div>
                                        </div>
                                        @elseif($input['fieldtype'] == "image")
                                        <div class="mb-3">
                                            @if(($input['showImage'] ?? '') == "yes")
                                            <div class="mb-2">
                                                <img src="{{ $input['val'] }}" class="img-thumbnail" style="max-height: 100px;">
                                            </div>
                                            @endif
                                            <input type="text" class="form-control mb-2" name="IMAGE-{{ $input['id'] }}||{{ $input['name'] }}" id="{{ $input['id'] }}" value="{{ $input['val'] }}">
                                            <label class="form-label">{{ __('messages.select_image') }}</label>
                                            <input type="file" class="form-control" name="{{ $input['id'] }}||{{ $input['name'] }}" id="{{ $input['id'] }}">
                                        </div>
                                        @elseif($input['fieldtype'] == "file")
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('messages.select_file') }}</label>
                                            <input type="file" class="form-control" name="{{ $input['id'] }}||{{ $input['name'] }}" id="{{ $input['id'] }}">
                                        </div>
                                        @elseif($input['fieldtype'] == "paragraph")
                                        <div class="mb-3">
                                            <textarea class="form-control summernote" name="{{ $input['id'] }}||{{ $input['name'] }}" id="summernote{{ $loop->index }}" rows="5">{{ $input['val'] }}</textarea>
                                        </div>
                                        @elseif($input['fieldtype'] == "text")
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="{{ $input['id'] }}||{{ $input['name'] }}" id="{{ $input['id'] }}" value="{{ $input['val'] }}" {{ $input['readonly'] ?? '' }}>
                                        </div>
                                        @elseif($input['fieldtype'] == "boolean")
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input type="hidden" value="No" name="{{ $input['id'] }}||{{ $input['name'] }}">
                                                <input class="form-check-input" type="checkbox" role="switch" name="{{ $input['id'] }}||{{ $input['name'] }}" id="{{ $input['id'] }}" value="yes" {{ $input['checked'] ?? '' }}>
                                                <label class="form-check-label" for="{{ $input['id'] }}">{{ __('messages.turn_on') }}</label>
                                            </div>
                                        </div>
                                        @endif
                                        <small class="text-muted"><span class="fw-semibold">{{ __('messages.what_update') }} </span> {{ $input['tooltip'] }}</small>
                                    </td>
                                </tr>
                                @endforeach
                                @if(!empty($html))
                                {!! $html !!}
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" name="locid" value="{{ $location->id }}" />
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" name="submit" class="btn btn-primary btn-lg fw-bold">{{ __('messages.update_cv') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-script-add')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Initialize summernote editors
        @foreach($inputs as $input)
        @if($input['fieldtype'] == "paragraph")
        $('#summernote{{ $loop->index }}').summernote({
            toolbar: [
                ['style', ['bold', 'italic', 'underline']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']]
            ],
            height: 200
        });
        @endif
        @endforeach

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        // Handle form submission with SweetAlert
        $('#customValuesForm').on('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '{{ __("messages.are_you_sure") }}',
                text: '{{ __("messages.confirm_update_cv") }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __("messages.yes_update") }}',
                cancelButtonText: '{{ __("messages.cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, submit the form
                    this.submit();
                }
            });
        });
    });
</script>
@endsection
