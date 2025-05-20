@extends('adminpanel.layout.app')

@section('content')
@section('select_package', 'active')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-head-row card-tools-still-right">
                        <h4 class="card-title">Packages</h4>
                        <div class="card-tools">
                            <a href="{{  route('admin.packagecreate') }}" class="btn btn-primary">Add New Package</a>
                          </div>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
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

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">

                            <table id="Yajra-dataTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Parent</th>
                                    <th>Projects In Package</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                                <tbody>
                                    <!-- Data will be populated by DataTables -->
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js-script')
@include('adminpanel.package.datatable')
@endsection
