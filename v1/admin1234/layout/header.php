<?php
// Build header user info; fall back to defaults when not logged in.
$headerAdminUsername = $_SESSION["admin_username"] ?? "Admin User";
$headerAdminEmail = $_SESSION["admin_email"] ?? "";
$headerAdminPicture = $_SESSION["admin_profile_picture"] ?? "";

if ($headerAdminPicture === "" && !empty($_SESSION["admin_id"])) {
    $headerAdminRecord = getAdminUser((int) $_SESSION["admin_id"]) ?? [];
    if ($headerAdminRecord !== []) {
        $headerAdminUsername =
            $headerAdminRecord["username"] ?? $headerAdminUsername;
        $headerAdminEmail = $headerAdminRecord["email"] ?? $headerAdminEmail;
        $headerAdminPicture =
            $headerAdminRecord["profile_picture"] ?? $headerAdminPicture;
        $_SESSION["admin_username"] = $headerAdminUsername;
        $_SESSION["admin_email"] = $headerAdminEmail;
        $_SESSION["admin_profile_picture"] = $headerAdminPicture;
    }
}

$headerAvatarUrl = media_url($headerAdminPicture ?? "");
$siteBrandLogo = site_logo_url();
?>
<!-- app-Header -->
<div class="app-header header sticky">
    <div class="container-fluid main-container">
        <div class="d-flex">
            <a aria-label="Hide Sidebar" class="app-sidebar__toggle" data-bs-toggle="sidebar" href="javascript:void(0)"></a>
            <!-- sidebar-toggle-->
            <a class="logo-horizontal " href="<?= file_url(
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
                ) ?>" class="header-brand-img light-logo1" alt="logo" style="height:42px;object-fit:contain;">
            </a>
            <!-- LOGO -->
            <!--<div class="main-header-center ms-3 d-none d-lg-block">
                <input type="text" class="form-control" id="typehead" placeholder="Search for results...">
                <button class="btn px-0 pt-2"><i class="fe fe-search" aria-hidden="true"></i></button>
            </div>-->
            <div class="d-flex order-lg-2 ms-auto header-right-icons">
                <!-- SEARCH -->
                <button class="navbar-toggler navresponsive-toggler d-lg-none ms-auto" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent-4"
                    aria-controls="navbarSupportedContent-4" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon fe fe-more-vertical"></span>
                </button>
                <div class="navbar navbar-collapse responsive-navbar p-0">
                    <div class="collapse navbar-collapse" id="navbarSupportedContent-4">
                        <div class="d-flex order-lg-2">
                            <div class="dropdown d-lg-none d-flex">
                                <a href="javascript:void(0)" class="nav-link icon" data-bs-toggle="dropdown">
                                    <i class="fe fe-search"></i>
                                </a>
                                <div class="dropdown-menu header-search dropdown-menu-start">
                                    <div class="input-group w-100 p-2">
                                        <input type="text" class="form-control" placeholder="Search....">
                                        <div class="input-group-text btn btn-primary">
                                            <i class="fa fa-search" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- OPTIONAL FEATURE: Theme-Layout (Light/Dark mode) -->
                            <div class="d-flex">
                                <a class="nav-link icon theme-layout nav-link-bg layout-setting">
                                    <span class="dark-layout"><i class="fe fe-moon"></i></span>
                                    <span class="light-layout"><i class="fe fe-sun"></i></span>
                                </a>
                            </div>

                            <!-- OPTIONAL FEATURE: Shopping Cart (currently disabled)
                            <div class="dropdown d-flex shopping-cart">
                                <a class="nav-link icon text-center" data-bs-toggle="dropdown">
                                    <i class="fe fe-shopping-cart"></i><span class="badge bg-secondary header-badge">4</span>
                                </a>
                                ...
                            </div>
                            -->

                            <!-- FULL-SCREEN -->
                            <div class="dropdown d-flex">
                                <a class="nav-link icon full-screen-link nav-link-bg">
                                    <i class="fe fe-minimize fullscreen-button"></i>
                                </a>
                            </div>

                            <!-- OPTIONAL FEATURE: Notifications Module (currently disabled) -->
                            <!--
                            <div class="dropdown d-flex notifications">
                                <a class="nav-link icon" data-bs-toggle="dropdown"><i class="fe fe-bell"></i><span class=" pulse"></span></a>
                                ...
                            </div>
                            -->

                            <!-- OPTIONAL FEATURE: Messages/Chat (currently disabled) -->
                            <!--
                            <div class="dropdown d-flex message">
                                <a class="nav-link icon text-center" data-bs-toggle="dropdown"><i class="fe fe-message-square"></i><span class="pulse-danger"></span></a>
                                ...
                            </div>
                            -->

                            <!-- PROFILE -->
                            <div class="dropdown d-flex profile-1">
                                <a href="javascript:void(0)" data-bs-toggle="dropdown" class="nav-link leading-none d-flex">
                                    <img src="<?= htmlspecialchars(
                                        $headerAvatarUrl,
                                        ENT_QUOTES,
                                        "UTF-8",
                                    ) ?>" alt="<?= htmlspecialchars(
    $headerAdminUsername,
    ENT_QUOTES,
    "UTF-8",
) ?>" class="avatar profile-user brround cover-image">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <div class="drop-heading">
                                        <div class="text-center">
                                            <h5 class="text-dark mb-0 fs-14 fw-semibold"><?= htmlspecialchars(
                                                $headerAdminUsername,
                                                ENT_QUOTES,
                                                "UTF-8",
                                            ) ?></h5>
                                            <?php if (
                                                $headerAdminEmail !== ""
                                            ): ?>
                                                <small class="text-muted"><?= htmlspecialchars(
                                                    $headerAdminEmail,
                                                    ENT_QUOTES,
                                                    "UTF-8",
                                                ) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="dropdown-divider m-0"></div>
                                    <!--<a class="dropdown-item" href="<?= file_url(
                                        "profile/profile.php",
                                    ) ?>">
                                        <i class="dropdown-icon fe fe-user"></i> Profile
                                    </a>-->
                                    <a class="dropdown-item" href="<?= file_url(
                                        "edit-profile/edit-profile",
                                    ) ?>">
                                        <i class="dropdown-icon fe fe-edit"></i> Edit Profile
                                    </a>
                                    <!-- <a class="dropdown-item" href="settings.php">
                                        <i class="dropdown-icon fe fe-settings"></i> Settings
                                    </a> -->
                                    <a class="dropdown-item" href="<?= file_url(
                                        "login/logout",
                                    ) ?>">
                                        <i class="dropdown-icon fe fe-log-out"></i> Log Out
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /app-Header -->
