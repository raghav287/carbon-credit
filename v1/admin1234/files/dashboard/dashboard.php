<?php
require_once dirname(__DIR__, 2) . "/app/init.php";
require_once APP_ROOT . "/app/auth.php";
requireAdminLogin();
require_once APP_ROOT . "/app/module-data.php";
$pageTitle = "Dashboard";
$dashboardStats = getDashboardMetrics();
$adminName = htmlspecialchars(
    $_SESSION["admin_username"] ?? "Admin User",
    ENT_QUOTES,
    "UTF-8",
);
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
                        <!-- DUMMY CONTENT COMMENTED OUT -->
                        <!--
                        <div class="page-header">
                            <h1 class="page-title">Dashboard</h1>
                        </div>
                        <div class="row">
                            ... existing stats & charts ...
                        </div>
                        -->
                        <div class="d-flex justify-content-center align-items-center" style="height: 60vh;">
                            <div class="text-center">
                            <h2>Welcome back, <?= $adminName ?>!</h2>
                                <p class="text-muted">Use the navigation links to access any module.</p>
                            </div>
                        </div>

                    </div>
                    <!-- CONTAINER END -->
                </div>
            </div>
            <!--app-content close-->

        </div>

        <?php include LAYOUT_PATH . "/footer.php"; ?>
    </div>

    <!-- REQUIRED JS COMPONENTS -->
    <?php include LAYOUT_PATH . "/scripts.php"; ?>

    <!-- INTERNAL INDEX JS (For charts on this page) -->
    <script src="<?= asset_url("js/index1.js") ?>"></script>

</body>

</html>
