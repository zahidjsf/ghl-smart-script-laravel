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
                        <a class="nav-link text-white @yield('select_dashboard')" href="{{ route('reward-promotions.dashboard') }}" id="topnav-dashboard" role="button">
                            <!-- <i class="bx bx-home-circle me-2"></i> -->
                            <span key="t-dashboards">Home</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white @yield('select_referrals')" href="{{ route('reward-promotions.referrals') }}" id="topnav-dashboard" role="button">
                            <!-- <i class="bx bx-home-circle me-2"></i> -->
                            <span key="t-dashboards">Referrals</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-white @yield('select_contactpoints')" href="{{ route('reward-promotions.contact_points') }}" id="topnav-dashboard" role="button">
                            <!-- <i class="bx bx-home-circle me-2"></i> -->
                            <span key="t-dashboards">Contact Points</span>
                        </a>
                    </li>



                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none text-white @yield('smart_apps')" href="#" id="topnav-smart-apps" role="button">
                            <span key="t-smart-apps">Rewards</span>
                            <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-smart-apps">
                            <a href="{{ route('frontend.smart_reward.index') }}" class="dropdown-item">
                                Reward Items
                            </a>
                            <a href="{{ route('frontend.smart_reward.cvcupdater') }}" class="dropdown-item">
                                Manage Redemptions
                            </a>
                        </div>
                    </li>


                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none text-white @yield('smart_apps')" href="#" id="topnav-smart-apps" role="button">
                            <span key="t-smart-apps">Promotions</span>
                            <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-smart-apps">
                            <a href="{{ route('frontend.smart_reward.index') }}" class="dropdown-item">
                                Manage Promotions
                            </a>
                            <a href="{{ route('frontend.smart_reward.cvcupdater') }}" class="dropdown-item">
                                Redeemed Coupons
                            </a>
                        </div>
                    </li>


                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none text-white @yield('smart_apps')" href="#" id="topnav-smart-apps" role="button">
                            <span key="t-smart-apps">Settings</span>
                            <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-smart-apps">
                            <a href="{{ route('frontend.smart_reward.index') }}" class="dropdown-item">
                                Points Settings
                            </a>
                            <a href="{{ route('frontend.smart_reward.cvcupdater') }}" class="dropdown-item">
                                Social Settings
                            </a>

                            <a href="{{ route('frontend.smart_reward.cvcupdater') }}" class="dropdown-item">
                                Business Settings
                            </a>

                        </div>
                    </li>
                </ul>
                <div class="ms-auto d-flex">
                    <a href="{{ route('reward-promotions.logout') }}" class="d-flex align-items-center" style="color: white;">
                        <i class="fa fa-sign-out-alt me-1"></i> Logout
                    </a>
                </div>
            </div>
        </nav>
    </div>
</div>
