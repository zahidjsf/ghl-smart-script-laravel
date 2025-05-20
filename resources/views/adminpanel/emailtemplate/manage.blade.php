@extends('adminpanel.layout.app')

@section('content')
@section('select_emailtemplate', 'active')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-head-row card-tools-still-right">
                        <h4 class="card-title">Email Template</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                        @if(session('success'))
                            <div style="color: green; margin-bottom: 10px;">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('admin.templateupdate') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="emailtemplate" class="form-label">Template</label>
                                <textarea 
                                    name="emailtemplate" 
                                    id="emailtemplate" 
                                    class="form-control" 
                                    rows="10"
                                >{{ old('emailtemplate', $contents) }}</textarea>
                            </div>
                        <div class="form-group">

                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>

                        </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection