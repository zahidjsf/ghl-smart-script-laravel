@extends('frontpanel.layout.app')

@section('content')
@section('select_account_setting', 'active')

<style>
    .view-data {
        margin-left: 5px;
        color: #6c757d;
    }
    .view-data:hover {
        color: #0d6efd;
    }
    .modal-body pre {
        white-space: pre-wrap;
        word-wrap: break-word;
        background: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
    }
</style>


<div class="container-fluid" style="padding:0 1.5rem">
    <!-- start page title -->
    <div class="row g-0">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h2><strong>API History</strong></h2>
            </div>
        </div>
    </div>
    <hr>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="content-block">

                <table id="Yajra-dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Location ID</th>
                            <th>Webhook Data</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Notes</th>
                            <th>Extra</th>
                        </tr>
                    </thead>
                </table>

                <!-- Static Modal (Recommended Approach) -->
                <div class="modal fade" id="dataModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Webhook Data</h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <pre id="modalDataContent"></pre>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

@endsection

@section('js-script-add')
@include('frontpanel.apihistory.datatable')
@endsection
