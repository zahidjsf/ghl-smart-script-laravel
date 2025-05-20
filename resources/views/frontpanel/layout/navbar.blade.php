<div class="topnav">
    <div class="w-100">
        <nav class="navbar navbar-light navbar-expand-lg topnav-menu">
            <!-- Added logo and hamburger button here -->
            <div class="navbar-brand-box">
                GHL SMART SCRIPT
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 d-lg-none header-item waves-effect waves-light" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <i class="fa fa-fw fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav">

                    <li class="nav-item">
                        <a class="nav-link @yield('select_dashboard')" href="{{ route('frontend.dashboard') }}" id="topnav-dashboard" role="button">
                            <i class="bx bx-home-circle me-2"></i><span key="t-dashboards">Dashboards</span>
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none @yield('smart_apps')" href="#" id="topnav-dashboard" role="button">
                            <i class="bx bx-home-circle me-2"></i><span key="t-dashboards">SMART Apps</span> <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-dashboard">
                            <a href="{{ route('frontend.smart_reward.index') }}" class="dropdown-item" key="t-default">Smart Rewards</a>
                            <a href="dashboard-saas.html" class="dropdown-item" key="t-saas">Saas</a>
                            <a href="dashboard-crypto.html" class="dropdown-item" key="t-crypto">Crypto</a>
                            <a href="dashboard-blog.html" class="dropdown-item" key="t-blog">Blog</a>
                        </div>
                    </li>
                </ul>

                <!-- Right-aligned dropdown menu -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none @yield('select_account_setting')" href="#" id="topnav-user" role="button">
                            <i class="bx bx-user me-2"></i><span key="t-user">Account Setting</span> <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="topnav-user">
                            <a href="{{ route('frontend.profile-detail') }}" class="dropdown-item" key="t-profile">Profile</a>
                            <a href="{{ route('frontend.api-history') }}" class="dropdown-item" key="t-settings">API Setting</a>
                            <a href="https://billing.stripe.com/p/login/dR6dTag2x6ee05G7ss" target="_blank" class="dropdown-item" key="t-settings">Stripe Billing Portal</a>
                            <a href="{{ route('frontend.location-display') }}" class="dropdown-item" key="t-settings">All Locations</a>
                            <a href="{{ route('frontend.logout-member') }}" class="dropdown-item" key="t-logout">Logout</a>
                        </div>
                    </li>
                </ul>

            </div>
        </nav>
    </div>
</div>
