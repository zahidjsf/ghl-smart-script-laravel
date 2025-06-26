<div class="topnav">
    <div class="w-100">
        <nav class="navbar navbar-light navbar-expand-lg topnav-menu">
            <!-- Brand Logo -->
            <div class="navbar-brand-box">
                {{ __('messages.app_name') }}
            </div>

            <!-- Mobile Toggle Button -->
            <button type="button" class="btn btn-sm px-3 font-size-16 d-lg-none header-item waves-effect waves-light"
                    data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <i class="fa fa-fw fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav">
                    <!-- Dashboard Link -->
                    <li class="nav-item">
                        <a class="nav-link @yield('select_dashboard')" href="{{ route('frontend.dashboard') }}" id="topnav-dashboard" role="button">
                            <i class="bx bx-home-circle me-2"></i>
                            <span key="t-dashboards">{{ __('messages.dashboard') }}</span>
                        </a>
                    </li>

                    <!-- SMART Apps Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none @yield('smart_apps')" href="#" id="topnav-smart-apps" role="button">
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
                            <a href="dashboard-crypto.html" class="dropdown-item">
                                {{ __('messages.crypto') }}
                            </a>
                            <a href="dashboard-blog.html" class="dropdown-item">
                                {{ __('messages.blog') }}
                            </a>
                        </div>
                    </li>
                </ul>

                <!-- Right-aligned Account Menu -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none @yield('select_account_setting')" href="#" id="topnav-user" role="button">
                            <i class="bx bx-user me-2"></i>
                            <span key="t-user">{{ __('messages.account_settings') }}</span>
                            <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="topnav-user">
                            <a href="{{ route('frontend.profile-detail') }}" class="dropdown-item">
                                {{ __('messages.profile') }}
                            </a>
                            <a href="{{ route('frontend.api-history') }}" class="dropdown-item">
                                {{ __('messages.api_settings') }}
                            </a>
                            <a href="https://billing.stripe.com/p/login/dR6dTag2x6ee05G7ss" target="_blank" class="dropdown-item">
                                {{ __('messages.billing_portal') }}
                            </a>
                            <a href="{{ route('frontend.location-display') }}" class="dropdown-item">
                                {{ __('messages.all_locations') }}
                            </a>
                            <a href="{{ route('frontend.logout-member') }}" class="dropdown-item">
                                {{ __('messages.logout') }}
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
