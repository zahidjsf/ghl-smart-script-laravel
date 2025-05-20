@extends('adminpanel.layout.app')

@section('content')
@section('select_package', 'active')

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

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                <form action="{{ route('admin.packageupdate', $package->id) }}" method="POST" class="form">
                @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Package Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $package->name }}" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ $package->Description }}</textarea>
                        </div>

                        <div class="alert alert-info">
                            <strong>Note:</strong> If you change the package details it won't update all members who came in through this package. It will only update new members who come in through this package.
                        </div>

                        <h4>Existing Projects</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Project</th>
                                        <th>Credits</th>
                                        <th>Per Location</th>
                                        <th>Unlimited</th>
                                        <th>Cumulative</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($package->details as $detail)
                                    <tr>

                                        <?php
                                            $projId = $detail->project;
                                            $projNames =  App\Models\SystemProject::where('id', $projId)->pluck('name')->implode('') ?? '';
                                        ?>

                                        <td>{{ $projNames }}</td>
                                        <td><input type="number" class="form-control" name="existing_projects[{{ $detail->id }}][credits]" value="{{ $detail->credits }}" min="0" required></td>
                                        <td><input type="number" class="form-control" name="existing_projects[{{ $detail->id }}][per_location]" value="{{ $detail->perLocation }}" min="0" required></td>
                                        <td><input type="checkbox" class="form-check-input" name="existing_projects[{{ $detail->id }}][unlimited]" value="1" {{ $detail->unlimited ? 'checked' : '' }}></td>
                                        <td><input type="checkbox" class="form-check-input" name="existing_projects[{{ $detail->id }}][cumulative]" value="1" {{ $detail->cumulative ? 'checked' : '' }}></td>
                                        <td>
                                            <input type="hidden" name="existing_projects[{{ $detail->id }}][id]" value="{{ $detail->id }}">
                                            <a href="{{ route('admin.removeProject', ['package' => $package->id, 'detail' => $detail->id]) }}"
                                            class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to remove this project from this package?')">Remove</a>

                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <h4>Add New Projects</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="new-projects-table">
                                <thead>
                                    <tr>
                                        <th>Project</th>
                                        <th>Credits</th>
                                        <th>Per Location</th>
                                        <th>Unlimited</th>
                                        <th>Cumulative</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="new-project-rows">
                                    <!-- New project rows will be added here -->
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group">
                            <button type="button" class="btn btn-success" id="add-new-project">Add New Project</button>
                        </div>

                        <div class="alert alert-warning">
                            *** If Per Location is set to 0, it will give unlimited credits to a location with this license
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Package</button>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>
@endsection


@section('js-script')
<script>
 

    $(document).ready(function() {
        // Add new project row
        $('#add-new-project').click(function() {
            addNewProjectRow();
        });

        // Remove new project row
        $(document).on('click', '.remove-new-project', function() {
            $(this).closest('tr').remove();
        });

        function addNewProjectRow() {
        var projects = @json($projects);
        var packageProjects = @json($package->projects ? $package->projects->pluck('id')->toArray() : []);
        var availableProjects = projects.filter(project => !packageProjects.includes(project.id));

        if (availableProjects.length === 0) {
            alert('All projects are already added to this package.');
            return;
        }

        var options = '';
        availableProjects.forEach(function(project) {
            options += '<option value="' + project.id + '">' + project.name + '</option>';
        });

        // Generate a unique key for this new row
        var uniqueKey = Date.now();

        var row = '<tr>' +
            '<td>' +
                '<select class="form-control project-select" name="new_projects[' + uniqueKey + '][id]" required>' +
                    '<option value="">Select Project</option>' + options +
                '</select>' +
            '</td>' +
            '<td><input type="number" class="form-control" name="new_projects[' + uniqueKey + '][credits]" value="0" min="0" required></td>' +
            '<td><input type="number" class="form-control" name="new_projects[' + uniqueKey + '][per_location]" value="0" min="0" required></td>' +
            '<td><input type="checkbox" class="form-check-input" name="new_projects[' + uniqueKey + '][unlimited]" value="1"></td>' +
            '<td><input type="checkbox" class="form-check-input" name="new_projects[' + uniqueKey + '][cumulative]" value="1"></td>' +
            '<td><button type="button" class="btn btn-danger remove-new-project">Remove</button></td>' +
        '</tr>';

        $('#new-project-rows').append(row);
}

    });
</script>


@endsection
