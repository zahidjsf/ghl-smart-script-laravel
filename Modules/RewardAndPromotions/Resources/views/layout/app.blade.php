<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>GHL SMART SCRIPTS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
        <meta content="Themesbrand" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{asset('adminpanel/assets/img/kaiadmin/FLP-Icon.png')}}">

        <!-- Bootstrap Css -->
        <link href="{{ asset('frontpanel/assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{ asset('frontpanel/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{ asset('frontpanel/assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />


    </head>

    <body data-topbar="dark" data-layout="horizontal">

        <!-- Begin page -->
        <div id="layout-wrapper">
          @include('rewardandpromotions::layout.navbar')

            <div class="main-content">

                <div class="page-content">


                    {{-- <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                   @yield('content')
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                    </div> <!-- container-fluid --> --}}

                    @yield('content')



                </div>
                <!-- End Page-content -->

            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->

        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>

        <script src="{{ asset('frontpanel/assets/libs/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('frontpanel/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('frontpanel/assets/libs/metismenu/metisMenu.min.js') }}"></script>
        <script src="{{ asset('frontpanel/assets/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('frontpanel/assets/libs/node-waves/waves.min.js') }}"></script>
        <script src="{{ asset('frontpanel/assets/js/app.js') }}"></script>

        <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


        @yield('js-script-add')

    </body>
</html>
