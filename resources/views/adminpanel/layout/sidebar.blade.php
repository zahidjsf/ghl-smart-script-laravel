<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="index.html" class="logo">
                <img src="{{asset('adminpanel/assets/img/kaiadmin/FLP-Icon.png')}}" alt="navbar brand" class="navbar-brand"
                    height="20" />
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">

                <li class="nav-item @yield('select_account')">
                    <a href="{{ route('admin.accounts') }}">
                        <i class="fas fa-users"></i>
                        <p>Accounts</p>
                    </a>
                </li>

                <li class="nav-item @yield('select_emailtemplate')">
                    <a href="{{ route('admin.emailtemplate') }}">
                        <i class="fas fa-envelope"></i>
                        <p>Email Template</p>
                    </a>
                </li>

                <li class="nav-item @yield('select_project')">
                    <a href="{{ route('admin.projects') }}">
                        <i><span class="icon">🛠️</span></i>
                        <p>Projects</p>
                    </a>
                </li>

                <li class="nav-item @yield('select_package')">
                    <a href="{{ route('admin.packages') }}">
                        <i><span class="icon">🛠️</span></i>

                        <p>Packages</p>
                    </a>
                </li>

                <li class="nav-item @yield('select_snapshot')">
                    <a href="{{ route('admin.snapshots') }}">
                    <i><span class="icon">🛠️</span></i>
                        <p>Snapshots</p>
                    </a>
                </li>

                <li class="nav-item @yield('select_setting')">
                    <a href="{{ route('admin.settings') }}">
                    <i><span class="icon">🛠️</span></i>
                        <p>Setting</p>
                    </a>
                </li>


            </ul>
        </div>
    </div>
</div>
