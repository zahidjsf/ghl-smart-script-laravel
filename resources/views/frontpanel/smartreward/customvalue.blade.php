@extends('frontpanel.layout.app')

@section('content')
@section('smart_apps', 'active')

<div class="container-fluid" style="padding:0 1.5rem">
    <!-- start page title -->
    <div class="row g-0">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h2><strong>Smart Rewards</strong></h2>
            </div>
        </div>
    </div>
    <hr>

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

    <form method="POST" action="{{ route('frontend.smart_reward.custom_values_update', $location) }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="locid" value="{{ $location->id }}">

        <div class="card mb-4">
            <div class="card-body">
                @foreach($inputs as $input)
                <div class="form-group row mb-4">
                    <label class="col-md-3 col-form-label">
                        {{ $input['name'] }}
                        @if($input['tooltip'])
                        <i class="fas fa-info-circle" title="{{ $input['tooltip'] }}"></i>
                        @endif
                    </label>

                    <div class="col-md-9">
                        @if($input['fieldtype'] == 'text')
                        <input type="text" class="form-control"
                            name="{{ $input['id'] }}||{{ $input['name'] }}"
                            value="{{ old($input['id'], $input['val']) }}"
                            {{ $input['readonly'] }}>
                        @elseif($input['fieldtype'] == 'textarea')
                        <textarea class="form-control {{ $input['summernote'] }}"
                            name="{{ $input['id'] }}||{{ $input['name'] }}"
                            rows="4">{{ old($input['id'], $input['val']) }}</textarea>
                        @elseif($input['fieldtype'] == 'boolean')
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input"
                                name="{{ $input['id'] }}||{{ $input['name'] }}"
                                value="yes" {{ $input['checked'] }}>
                            <label class="form-check-label">Yes</label>
                        </div>
                        @elseif($input['fieldtype'] == 'revLogo')
                        <select class="form-control" name="{{ $input['id'] }}||{{ $input['name'] }}">
                            {!! $input['revOptions'] !!}
                        </select>
                        @elseif(in_array($input['fieldtype'], ['logo', 'image', 'revLogo']))
                        @if($input['showImage'] == 'yes' && $input['val'])
                        <img src="{{ $input['val'] }}" class="img-thumbnail mb-2" style="max-height: 100px;">
                        @endif
                        <input type="file" class="form-control-file"
                            name="IMAGE-{{ $input['id'] }}||{{ $input['name'] }}">
                        <input type="hidden" name="ALT-{{ $input['id'] }}||{{ $input['name'] }}"
                            value="{{ $input['val'] }}">
                        @endif

                        @if($input['resources'])
                        <small class="form-text text-muted">{!! $input['resources'] !!}</small>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-9 offset-md-3">
                <button type="submit" class="btn btn-primary" name="submit">
                    Update Custom Values
                </button>
                <a href="" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>


</div>
@endsection


@section('js-script-add')

<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">

<script>
    $(document).ready(function() {
        @foreach($inputs as $input)
        @if($input['summernote'])
        $('.{{ $input['
            summernote '] }}').summernote({
            height: 150,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough']],
                ['para', ['ul', 'ol']],
                ['insert', ['link']],
                ['view', ['codeview']]
            ]
        });
        @endif
        @endforeach
    });
</script>
@endsection
