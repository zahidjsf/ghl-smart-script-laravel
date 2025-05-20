@extends('adminpanel.layout.app')

@section('content')
@section('select_project', 'active')

<div class="row">
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <h4 class="card-title">Edit Projects</h4>
            </div>
            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger mt-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.projectupdate', $project->id) }}" method="POST" class="form responsive-width-100 mt-3">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">First Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Project Name" value="{{ old('name', $project->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $project->description) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="cvcollection">Custom Value Collection Ids</label>
                        <input type="text" class="form-control" id="cvcollection" name="cvcol" placeholder="Add Collection IDs, Comma Separated" value="{{ old('cvcol', $project->cv_collections) }}">
                        <small>Comma Separate Your Collection Ids IE: 12,13,14,15</small>
                    </div>

                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="inMembership" name="inMembership" value="yes" {{ old('inMembership', $project->inMembership) == 'yes' ? 'checked' : '' }}>
                        <label class="form-check-label" for="inMembership">Available In Membership</label>
                    </div>

                    <div class="submit-btns mt-3">
                        <input type="submit" class="btn btn-primary" name="submit" value="Submit">
                       
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
