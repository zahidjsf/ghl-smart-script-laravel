@extends('adminpanel.layout.app')

@section('content')
@section('select_package', 'active')


<div class="row">
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <h4 class="card-title">Create Projects</h4>
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

                <form action="{{ route('admin.packagestore') }}" method="POST" class="form">
                @csrf

<div class="form-group">
    <label for="name">Package Name</label>
    <input type="text" class="form-control" id="name" name="name" required>
</div>

<div class="form-group">
    <label for="description">Description</label>
    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
</div>

<div class="alert alert-info">
    <strong>Note:</strong> If you change the package details it won't update all members who came in through this package. It will only update new members who come in through this package.
</div>

<h4>Projects</h4>
<div class="table-responsive">
    <table class="table table-bordered" id="projects-table">
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
        <tbody id="project-rows">
            <!-- Initial row will be added by JavaScript -->
        </tbody>
    </table>
</div>

<div class="form-group">
    <button type="button" class="btn btn-success" id="add-project">Add Project</button>
</div>

<div class="alert alert-warning">
    *** If Per Location is set to 0, it will give unlimited credits to a location with this license
</div>

<button type="submit" class="btn btn-primary">Submit</button>
</form>
                        </div>
                    </div>
                </div>
            </div>
@endsection


@section('js-script')
<script>
    $(document).ready(function() {
        let projectIndex = 0;

        addProjectRow();

        $('#add-project').click(function() {
            addProjectRow();
        });

        $(document).on('click', '.remove-project', function() {
            $(this).closest('tr').remove();
            updateProjectIndexes();
        });

        function addProjectRow() {
            const projects = @json($projects);
            let options = '<option value="">Select Project</option>';

            projects.forEach(function(project) {
                options += `<option value="${project.id}">${project.name}</option>`;
            });

            const row = `
                <tr>
                    <td>
                        <select class="form-control" name="projects[${projectIndex}][id]" required>
                            ${options}
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control" name="projects[${projectIndex}][credits]" value="0" min="0" required>
                    </td>
                    <td>
                        <input type="number" class="form-control" name="projects[${projectIndex}][per_location]" value="0" min="0" required>
                    </td>
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input" name="projects[${projectIndex}][unlimited]" value="1">
                    </td>
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input" name="projects[${projectIndex}][cumulative]" value="1">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger remove-project">Remove</button>
                    </td>
                </tr>
            `;

            $('#project-rows').append(row);
            projectIndex++;
        }

        function updateProjectIndexes() {
            projectIndex = 0;
            $('#project-rows tr').each(function() {
                $(this).find('select, input').each(function() {
                    const name = $(this).attr('name');
                    const newName = name.replace(/projects\[\d+\]/, `projects[${projectIndex}]`);
                    $(this).attr('name', newName);
                });
                projectIndex++;
            });
        }
    });
</script>
@endsection