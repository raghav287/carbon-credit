<?php
require_once dirname(__DIR__, 2) . "/app/init.php";
require_once APP_ROOT . "/app/auth.php";
requireAdminLogin();
$pageTitle = "Search Results";
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
                            <h1 class="page-title">Search</h1>
                            <div>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Search Results</li>
                                </ol>
                            </div>
                        </div>
                        <!-- PAGE-HEADER END -->

                        <!-- Row -->
                        <div class="row">
                            <div class="col-sm-12 col-md-12">
                                <div class="card">
                                    <div class="card-body pb-0">
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" placeholder="Search for something...">
                                            <button class="btn btn-primary" type="button">Search</button>
                                        </div>
                                        <div class="tabs-menu search-tabs">
                                            <ul class="nav panel-tabs">
                                                <li><a href="#tab-all" class="active" data-bs-toggle="tab">All</a></li>
                                                <li><a href="#tab-images" data-bs-toggle="tab" class="text-dark">Images</a></li>
                                                <li><a href="#tab-files" data-bs-toggle="tab" class="text-dark">Files</a></li>
                                                <li><a href="#tab-users" data-bs-toggle="tab" class="text-dark">Users</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-body p-5">
                                        <p class="text-muted mb-0 ps-3">About 1,250 results (0.45 seconds)</p>
                                    </div>
                                </div>
                                <div class="panel-body tabs-menu-body p-0 border-0">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tab-all">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="mb-2">
                                                        <a href="javascript:void(0)" class="h4 text-dark">Advanced Analytics Dashboard Overview</a>
                                                    </div>
                                                    <a href="<?= file_url(
                                                        "dashboard/dashboard.php",
                                                    ) ?>" class="text-primary"><?= file_url(
    "dashboard/dashboard.php",
) ?></a>
                                                    <p class="text-muted mt-2 mb-2">Detailed statistics and performance tracking for the current month. Includes user engagement metrics, sales data, and server status monitoring.</p>
                                                    <div>
                                                        <span class="badge bg-primary-transparent text-primary">Analytics</span>
                                                        <span class="badge bg-secondary-transparent text-secondary">Dashboard</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="mb-2">
                                                        <a href="javascript:void(0)" class="h4 text-dark">User Management: Percy Kewshun</a>
                                                    </div>
                                                    <a href="<?= file_url(
                                                        "profile/profile.php",
                                                    ) ?>?id=21" class="text-primary"><?= file_url(
    "profile/profile.php",
) ?>?id=21</a>
                                                    <p class="text-muted mt-2 mb-2">View and update user profile information. Manage permissions and roles for this specific user account.</p>
                                                    <div>
                                                        <span class="badge bg-success-transparent text-success">User</span>
                                                        <span class="badge bg-info-transparent text-info">Profile</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="mb-2">
                                                        <a href="javascript:void(0)" class="h4 text-dark">Data Import Specifications</a>
                                                    </div>
                                                    <a href="javascript:void(0)" class="text-primary">/docs/import-guide.pdf</a>
                                                    <p class="text-muted mt-2 mb-2">Technical requirements and field mappings for bulk data imports into the system. Supports CSV and JSON formats.</p>
                                                    <div>
                                                        <span class="badge bg-warning-transparent text-warning">Documentation</span>
                                                        <span class="badge bg-danger-transparent text-danger">PDF</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-center">
                                                <div class="mb-5">
                                                    <ul class="pagination justify-content-center">
                                                        <li class="page-item page-prev disabled">
                                                            <a class="page-link" href="javascript:void(0)" tabindex="-1">Prev</a>
                                                        </li>
                                                        <li class="page-item active"><a class="page-link" href="javascript:void(0)">1</a></li>
                                                        <li class="page-item"><a class="page-link" href="javascript:void(0)">2</a></li>
                                                        <li class="page-item"><a class="page-link" href="javascript:void(0)">3</a></li>
                                                        <li class="page-item page-next">
                                                            <a class="page-link" href="javascript:void(0)">Next</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab-images">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="row row-sm">
                                                        <div class="col-6 col-md-3">
                                                        <img alt="img" class="img-fluid br-7 mb-4" src="<?= asset_url(
                                                            "images/media/1.jpg",
                                                        ) ?>">
                                                        </div>
                                                        <div class="col-6 col-md-3">
                                                            <img alt="img" class="img-fluid br-7 mb-4" src="<?= asset_url(
                                                                "images/media/2.jpg",
                                                            ) ?>">
                                                        </div>
                                                        <div class="col-6 col-md-3">
                                                            <img alt="img" class="img-fluid br-7 mb-4" src="<?= asset_url(
                                                                "images/media/3.jpg",
                                                            ) ?>">
                                                        </div>
                                                        <div class="col-6 col-md-3">
                                                            <img alt="img" class="img-fluid br-7 mb-4" src="<?= asset_url(
                                                                "images/media/4.jpg",
                                                            ) ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Row -->

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

</body>

</html>
