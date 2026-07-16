<?php
require_once dirname(__DIR__, 2) . "/app/init.php";
require_once APP_ROOT . "/app/auth.php";
requireAdminLogin();

$pageTitle = "User Profile";
$adminId = (int) ($_SESSION["admin_id"] ?? 0);
$adminAccount = getAdminUser($adminId) ?? [];
$displayName = $adminAccount["username"] ?? "Admin User";
$profileEmail = $adminAccount["email"] ?? "Not set";
$profilePicturePath = $adminAccount["profile_picture"] ?? "";
$profilePictureUrl = media_url($profilePicturePath);

include LAYOUT_PATH . "/head.php";
?>

<body class="app sidebar-mini ltr light-mode">

    <!-- GLOBAL-LOADER -->
    <div id="global-loader">
        <img src="<?= asset_url(
            "images/loader.svg",
        ) ?>" class="loader-img" alt="Loader">
    </div>
    <!-- /GLOBAL-LOADER -->

    <!-- PAGE -->
    <div class="page">
        <div class="page-main">

            <?php include LAYOUT_PATH . "/header.php"; ?>
            <?php include LAYOUT_PATH . "/sidebar.php"; ?>

            <!--app-content open-->
            <div class="main-content app-content mt-0">
                <div class="side-app">

                    <!-- CONTAINER -->
                    <div class="main-container container-fluid">

                        <!-- PAGE-HEADER -->
                        <div class="page-header">
                            <h1 class="page-title">Profile</h1>
                            <div>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="javascript:void(0)">Pages</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                                </ol>
                            </div>
                        </div>
                        <!-- PAGE-HEADER END -->

                        <div class="row">
                            <div class="col-xl-4">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <div class="avatar avatar-xxl chat-profile mb-3 brround">
                                            <img src="<?= htmlspecialchars(
                                                $profilePictureUrl,
                                                ENT_QUOTES,
                                                "UTF-8",
                                            ) ?>" alt="avatar" class="brround">
                                        </div>
                                        <h4 class="text-dark fw-semibold mb-1"><?= htmlspecialchars(
                                            $displayName,
                                            ENT_QUOTES,
                                            "UTF-8",
                                        ) ?></h4>
                                        <p class="text-muted mb-2"><?= htmlspecialchars(
                                            $profileEmail,
                                            ENT_QUOTES,
                                            "UTF-8",
                                        ) ?></p>
                                        <a href="<?= file_url(
                                            "edit-profile/edit-profile.php",
                                        ) ?>" class="btn btn-primary btn-sm"><i class="fe fe-edit"></i> Edit Profile</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table row table-borderless">
                                                <tbody class="col-lg-12 col-xl-12 p-0">
                                                    <tr>
                                                        <td><strong>Username :</strong> <?= htmlspecialchars(
                                                            $displayName,
                                                            ENT_QUOTES,
                                                            "UTF-8",
                                                        ) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Email :</strong> <?= htmlspecialchars(
                                                            $profileEmail,
                                                            ENT_QUOTES,
                                                            "UTF-8",
                                                        ) ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-8">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-title">Account Overview</div>
                                    </div>
                                    <div class="card-body">
                                        <p>
                                            You are operating under the account for <strong><?= htmlspecialchars(
                                                $displayName,
                                                ENT_QUOTES,
                                                "UTF-8",
                                            ) ?></strong>.
                                        </p>
                                        <p>
                                            Keep your username and profile picture updated through the <a href="<?= file_url(
                                                "edit-profile/edit-profile.php",
                                            ) ?>">Edit Profile</a> page. Password management is also available there.
                                        </p>
                                    </div>
                                </div>
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <div class="card-title">Tips</div>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2">Make sure your profile image is square for the best appearance.</li>
                                            <li>Use a unique username to personalize your admin experience.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- CONTAINER CLOSED -->
                </div>
            </div>
            <!--app-content closed-->
        </div>

        <?php include LAYOUT_PATH . "/footer.php"; ?>
    </div>

    <!-- REQUIRED JS COMPONENTS -->
    <?php include LAYOUT_PATH . "/scripts.php"; ?>

</body>

</html>
