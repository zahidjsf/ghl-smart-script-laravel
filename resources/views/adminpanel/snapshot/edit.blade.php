@extends('adminpanel.layout.app')

@section('content')
@section('select_snapshot', 'active')

<div class="row">
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <h4 class="card-title">Edit Snapshot</h4>
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

                <form action="{{ route('admin.snapshotupdate', $snapshot->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $snapshot->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="url">URL</label>
                        <input type="text" class="form-control" id="url" name="url" value="{{ old('url', $snapshot->url) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="proj_id">Select Project</label>
                        <select class="form-control" name="proj_id" id="proj_id">
                            <option value="0">No Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('proj_id', $snapshot->proj_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="server_image">Current Image</label>
                        @if($snapshot->server_image)
                            <div class="mb-2">
                                <img src="{{ asset($snapshot->server_image) }}" alt="Current Image" style="max-height: 200px;" class="img-thumbnail">
                            </div>
                        @else
                            <p>No image uploaded</p>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="server_image">Change Image (Leave blank to keep current)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="server_image" name="server_image" accept="image/*">
                            <label class="custom-file-label" for="server_image">Choose new file</label>
                        </div>
                        <small class="form-text text-muted">
                            Accepted formats: jpeg, png, jpg, gif. Max size: 2MB
                        </small>
                    </div>

                    <div class="form-group text-center">
                        <img id="image-preview" src="#" alt="Preview" class="img-fluid mt-2 d-none" style="max-height: 200px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>

                    <div class="form-group">
                        <label for="documentation">Documentation</label>
                        <textarea class="form-control" name="documentation" id="documentation" rows="3">{{ old('documentation', $snapshot->documentation) }}</textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('admin.snapshots') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-script')
<script>
    // Image preview functionality
    document.getElementById('server_image').addEventListener('change', function(e) {
        const preview = document.getElementById('image-preview');
        const fileLabel = document.querySelector('.custom-file-label');

        if (e.target.files.length > 0) {
            const file = e.target.files[0];
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
            fileLabel.textContent = file.name;
        } else {
            preview.src = '#';
            preview.classList.add('d-none');
            fileLabel.textContent = 'Choose new file';
        }
    });
</script>
@endsection
