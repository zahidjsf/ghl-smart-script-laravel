<div class="topnav" style="background: linear-gradient(274deg, rgba(66,178,236,1) 0%, rgba(2,76,139,1) 100%);">
    <div class="container-fluid dashboard-container px-0">
        <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

            <!-- Mobile Toggle Button -->
            <button type="button" class="btn btn-sm px-3 font-size-16 d-lg-none header-item waves-effect waves-light text-white"
                    data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <i class="fa fa-fw fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav">
                    <!-- Dashboard Link -->
                    <li class="nav-item">
                        <a class="nav-link text-white @yield('select_dashboard')" href="{{ route('frontend.dashboard') }}" id="topnav-dashboard" role="button">
                            <i class="bx bx-home-circle me-2"></i>
                            <span key="t-dashboards">{{ __('messages.dashboard') }}</span>
                        </a>
                    </li>

                    <!-- SMART Apps Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none text-white @yield('smart_apps')" href="#" id="topnav-smart-apps" role="button">
                            <span key="t-smart-apps">{{ __('messages.smart_apps') }}</span>
                            <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-smart-apps">
                            <a href="{{ route('frontend.smart_reward.index') }}" class="dropdown-item">
                                {{ __('messages.smart_rewards') }}
                            </a>
                            <a href="{{ route('frontend.smart_reward.cvcupdater') }}" class="dropdown-item">
                                {{ __('messages.cv_updater') }}
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
