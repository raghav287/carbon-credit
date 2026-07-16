<?php
require_once dirname(__DIR__, 2) . "/app/init.php";
require_once APP_ROOT . "/app/auth.php";
requireAdminLogin();
require_once APP_ROOT . "/app/module-data.php";
$pageTitle = "Data Listings";
$listingItems = getListingItems();
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
                            <h1 class="page-title">Data Listings</h1>
                            <div>
                                <ol class="breadcrumb">
                                    <!--<li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>-->
                                    <li class="breadcrumb-item active" aria-current="page">Listings</li>
                                </ol>
                            </div>
                        </div>
                        <?php if (
                            isset($_GET["saved"]) &&
                            $_GET["saved"] === "1"
                        ): ?>
                            <div class="alert alert-success" data-autohide="4000">
                                Data saved successfully.
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_GET["deleted"])): ?>
                            <div class="alert alert-<?= $_GET["deleted"] === "1"
                                ? "success"
                                : "danger" ?>" data-autohide="4000">
                                <?= $_GET["deleted"] === "1"
                                    ? "Data deleted successfully."
                                    : "Unable to delete the data right now." ?>
                            </div>
                        <?php endif; ?>
                        <!-- PAGE-HEADER END -->

                        <!-- Row -->
                        <div class="row row-sm">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between">
                                        <h3 class="card-title">Responsive DataTable</h3>
                                        <a href="<?= file_url(
                                            "form/form",
                                        ) ?>" class="btn btn-primary btn-sm"><i class="fe fe-plus"></i> Add New</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered text-nowrap border-bottom" id="responsive-datatable">
                                                <thead>
                                                    <tr>
                                                        <th class="border-bottom-0">Name</th>
                                                        <th class="border-bottom-0">Position</th>
                                                        <th class="border-bottom-0">Start date</th>
                                                        <th class="border-bottom-0">Salary</th>
                                                        <th class="border-bottom-0">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (
                                                        empty($listingItems)
                                                    ): ?>
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">No records found.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                    <?php foreach (
                                                        $listingItems
                                                        as $item
                                                    ): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars(
                                                                $item["name"],
                                                            ) ?></td>
                                                            <td><?= htmlspecialchars(
                                                                $item[
                                                                    "position"
                                                                ],
                                                            ) ?></td>
                                                            <td><?= htmlspecialchars(
                                                                $item[
                                                                    "start_date"
                                                                ],
                                                            ) ?></td>
                                                            <td><?= htmlspecialchars(
                                                                $item["salary"],
                                                            ) ?></td>
                                                            <td>
                                                                <a href="<?= file_url(
                                                                    "form/form",
                                                                ) ?>?id=<?= urlencode(
    $item["id"],
) ?>" class="btn btn-primary btn-sm me-1"><i class="fe fe-edit"></i></a>
                                                                <form method="post" action="<?= file_url(
                                                                    "list/delete",
                                                                ) ?>" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this listing?');">
                                                                    <input type="hidden" name="id" value="<?= (int) $item[
                                                                        "id"
                                                                    ] ?>">
                                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                                        <i class="fe fe-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
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

    <!-- INTERNAL Data tables js-->
    <script src="<?= asset_url(
        "plugins/datatable/js/jquery.dataTables.min.js",
    ) ?>"></script>
    <script src="<?= asset_url(
        "plugins/datatable/js/dataTables.bootstrap5.js",
    ) ?>"></script>
    <script src="<?= asset_url(
        "plugins/datatable/js/dataTables.buttons.min.js",
    ) ?>"></script>
    <script src="<?= asset_url(
        "plugins/datatable/js/buttons.bootstrap5.min.js",
    ) ?>"></script>
    <script src="<?= asset_url(
        "plugins/datatable/js/jszip.min.js",
    ) ?>"></script>
    <script src="<?= asset_url(
        "plugins/datatable/pdfmake/pdfmake.min.js",
    ) ?>"></script>
    <script src="<?= asset_url(
        "plugins/datatable/pdfmake/vfs_fonts.js",
    ) ?>"></script>
    <script src="<?= asset_url(
        "plugins/datatable/js/buttons.html5.min.js",
    ) ?>"></script>
    <script src="<?= asset_url(
        "plugins/datatable/js/buttons.print.min.js",
    ) ?>"></script>
    <script src="<?= asset_url(
        "plugins/datatable/js/buttons.colVis.min.js",
    ) ?>"></script>
    <script src="<?= asset_url(
        "plugins/datatable/dataTables.responsive.min.js",
    ) ?>"></script>
    <script src="<?= asset_url(
        "plugins/datatable/responsive.bootstrap5.min.js",
    ) ?>"></script>
    <script src="<?= asset_url("js/table-data.js") ?>"></script>

</body>

</html>
