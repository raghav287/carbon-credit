<?php
require_once dirname(__DIR__, 2) . "/app/init.php";
require_once APP_ROOT . "/app/auth.php";
requireAdminLogin();
require_once APP_ROOT . "/app/module-data.php";

function deleteInquiryDirectly(int $id): bool
{
    if ($id <= 0) {
        return false;
    }

    try {
        $connection = getSashDBConnection();
        if ($connection === null) {
            error_log("Inquiry direct delete failed: database connection unavailable.");
            return false;
        }

        $stmt = $connection->prepare("DELETE FROM `inquiries` WHERE `id` = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $deleted = $stmt->affected_rows > 0;
        $stmt->close();
        $connection->close();
        return $deleted;
    } catch (Throwable $e) {
        error_log("Inquiry direct delete failed for id {$id}: {$e->getMessage()}");
        return false;
    }
}

if (isset($_GET["delete_id"])) {
    $deleteId = filter_input(INPUT_GET, "delete_id", FILTER_VALIDATE_INT);
    $deleted = $deleteId !== null && $deleteId !== false && deleteInquiryDirectly($deleteId);

    header("Location: " . file_url("inquiries/inquiries.php") . "?deleted=" . ($deleted ? "1" : "0"));
    exit();
}

if (($_SERVER["REQUEST_METHOD"] ?? "GET") === "POST" && isset($_POST["delete_inquiry"])) {
    $inquiryId = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
    $deleted = false;

    if ($inquiryId !== null && $inquiryId !== false && $inquiryId > 0) {
        $deleted = deleteInquiryDirectly($inquiryId);
    }

    if (!$deleted) {
        $deleted = deleteInquiryByDetails(
            trim((string) ($_POST["name"] ?? "")),
            trim((string) ($_POST["email"] ?? "")),
            trim((string) ($_POST["mobile"] ?? "")),
            trim((string) ($_POST["message"] ?? "")),
            trim((string) ($_POST["created_at"] ?? ""))
        );
    }

    header("Location: " . file_url("inquiries/inquiries.php") . "?deleted=" . ($deleted ? "1" : "0"));
    exit();
}

$pageTitle = "Inquiries";
$inquiries = getInquiries();
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
                            <h1 class="page-title">Inquiries</h1>
                            <div>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= file_url(
                                        "dashboard/dashboard.php",
                                    ) ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Inquiries</li>
                                </ol>
                            </div>
                        </div>
                        <?php $deleteStatus = $_GET["deleted"] ?? ""; ?>
                        <?php if ($deleteStatus !== ""): ?>
                            <div class="alert alert-<?= $deleteStatus === "1"
                                ? "success"
                                : "danger" ?> alert-dismissible fade show" role="alert" data-autohide="4000">
                                <?= $deleteStatus === "1"
                                    ? "Inquiry deleted successfully."
                                    : "Unable to delete the inquiry right now." ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <!-- PAGE-HEADER END -->

                        <!-- Row -->
                        <div class="row row-sm">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between">
                                        <h3 class="card-title">Contact Form Submissions</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered text-nowrap border-bottom" id="responsive-datatable">
                                                <thead>
                                                    <tr>
                                                        <th class="border-bottom-0">Name</th>
                                                        <th class="border-bottom-0">Email ID</th>
                                                        <th class="border-bottom-0">Mobile Number</th>
                                                        <th class="border-bottom-0">Message</th>
                                                        <th class="border-bottom-0">Date Submitted</th>
                                                        <th class="border-bottom-0">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (empty($inquiries)): ?>
                                                        <tr>
                                                            <td colspan="6" class="text-center text-muted">No submissions found.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                    <?php foreach ($inquiries as $inquiry): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($inquiry["name"]) ?></td>
                                                            <td><?= htmlspecialchars($inquiry["email"]) ?></td>
                                                            <td><?= htmlspecialchars($inquiry["mobile"]) ?></td>
                                                            <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($inquiry["message"]) ?></td>
                                                            <td><?= htmlspecialchars(date("Y-m-d H:i:s", strtotime($inquiry["created_at"]))) ?></td>
                                                            <td>
                                                                <a href="<?= htmlspecialchars(file_url("inquiries/inquiries.php") . "?delete_id=" . (int) $inquiry["id"], ENT_QUOTES, "UTF-8") ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this inquiry?');">
                                                                    <i class="fe fe-trash"></i>
                                                                </a>
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
