<?php $siteBrandLogo = site_logo_url(); ?>
<!--APP-SIDEBAR-->
<div class="sticky">
    <div class="app-sidebar__overlay" data-bs-toggle="sidebar"></div>
    <div class="app-sidebar">
        <div class="side-header">
            <a class="header-brand1" href="<?= file_url(
                "dashboard/dashboard.php",
            ) ?>">
                <img src="<?= htmlspecialchars(
                    $siteBrandLogo,
                    ENT_QUOTES,
                    "UTF-8",
                ) ?>" class="header-brand-img desktop-logo" alt="logo" style="height:42px;object-fit:contain;">
                <img src="<?= htmlspecialchars(
                    $siteBrandLogo,
                    ENT_QUOTES,
                    "UTF-8",
                ) ?>" class="header-brand-img toggle-logo" alt="logo" style="height:42px;object-fit:contain;">
                <img src="<?= htmlspecialchars(
                    $siteBrandLogo,
                    ENT_QUOTES,
                    "UTF-8",
                ) ?>" class="header-brand-img light-logo" alt="logo" style="height:42px;object-fit:contain;">
                <img src="<?= htmlspecialchars(
                    $siteBrandLogo,
                    ENT_QUOTES,
                    "UTF-8",
                ) ?>" class="header-brand-img light-logo1" alt="logo" style="height:42px;object-fit:contain;">
            </a>
            <!-- LOGO -->
        </div>
        <div class="main-sidemenu">
            <div class="slide-left disabled" id="slide-left"><svg xmlns="http://www.w3.org/2000/svg"
                    fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z" />
                </svg></div>
            <ul class="side-menu">
                <li class="sub-category">
                    <h3>Menu</h3>
                </li>
                <li class="slide">
                    <a class="side-menu__item has-link" data-bs-toggle="slide" href="<?= file_url(
                        "dashboard/dashboard",
                    ) ?>">
                        <i class="side-menu__icon fe fe-home"></i>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>

                <!--<li class="sub-category">
                    <h3>Modules</h3>
                </li>-->

               

                <li class="slide">
                    <a class="side-menu__item has-link" data-bs-toggle="slide" href="<?= file_url(
                        "inquiries/inquiries",
                    ) ?>">
                        <i class="side-menu__icon fe fe-message-square"></i>
                        <span class="side-menu__label">Inquiries</span>
                    </a>
                </li>

                <!--<li class="slide">
                    <a class="side-menu__item has-link" data-bs-toggle="slide" href="<?= file_url(
                        "form/form",
                    ) ?>">
                        <i class="side-menu__icon fe fe-file-text"></i>
                        <span class="side-menu__label">Forms</span>
                    </a>
                </li>-->

                <!--<li class="sub-category">
                    <h3>User Management</h3>
                </li>-->

                <!--<li class="slide">
                    <a class="side-menu__item has-link" data-bs-toggle="slide" href="<?= file_url(
                        "profile/profile.php",
                    ) ?>">
                        <i class="side-menu__icon fe fe-user"></i>
                        <span class="side-menu__label">Profile</span>
                    </a>
                </li>-->

                <!--<li class="slide">
                    <a class="side-menu__item has-link" data-bs-toggle="slide" href="<?= file_url(
                        "edit-profile/edit-profile.php",
                    ) ?>">
                        <i class="side-menu__icon fe fe-edit"></i>
                        <span class="side-menu__label">Edit Profile</span>
                    </a>
                </li>-->

              <!--  <li class="slide">
                    <a class="side-menu__item has-link" data-bs-toggle="slide" href="<?= file_url(
                        "search/search.php",
                    ) ?>">
                        <i class="side-menu__icon fe fe-search"></i>
                        <span class="side-menu__label">Search Results</span>
                    </a>
                </li> -->

                <li class="slide">
                    <a class="side-menu__item has-link" data-bs-toggle="slide" href="<?= file_url(
                        "settings/settings",
                    ) ?>">
                        <i class="side-menu__icon fe fe-settings"></i>
                        <span class="side-menu__label">Settings</span>
                    </a>
                </li>

                <!-- OPTIONAL FEATURES: Commented out for a cleaner look -->
                <!--
                <li class="sub-category">
                    <h3>Advanced Components</h3>
                </li>
                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0)">
                        <i class="side-menu__icon fe fe-calendar"></i>
                        <span class="side-menu__label">Calendar</span>
                        <i class="angle fe fe-chevron-right"></i>
                    </a>
                </li>
                ...
                -->

            </ul>
            <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191"
                    width="24" height="24" viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" />
                </svg></div>
        </div>
    </div>
</div>
<!--/APP-SIDEBAR-->
