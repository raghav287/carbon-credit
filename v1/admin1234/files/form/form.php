<?php
require_once dirname(__DIR__, 2) . "/app/init.php";
require_once APP_ROOT . "/app/auth.php";
requireAdminLogin();
require_once APP_ROOT . "/app/module-data.php";

$pageTitle = "Listing Form";
$errors = [];
$listingId = null;
$formValues = [
    "name" => "",
    "position" => "",
    "start_date" => "",
    "salary" => "",
];

if (!empty($_GET["id"])) {
    $listingId = (int) $_GET["id"];
    $item = getListingItemById($listingId);
    if ($item !== null) {
        $formValues = [
            "name" => $item["name"],
            "position" => $item["position"],
            "start_date" => $item["start_date"],
            "salary" => $item["salary"],
        ];
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $listingId = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
    $formValues = [
        "name" => trim($_POST["name"] ?? ""),
        "position" => trim($_POST["position"] ?? ""),
        "start_date" => trim($_POST["start_date"] ?? ""),
        "salary" => trim($_POST["salary"] ?? ""),
    ];

    foreach ($formValues as $key => $value) {
        if ($value === "") {
            $errors[] = "Please provide a value for {$key}.";
            break;
        }
    }

    if (empty($errors)) {
        $savedId = saveListingItem([
            "id" => $listingId ?: null,
            "name" => $formValues["name"],
            "position" => $formValues["position"],
            "start_date" => $formValues["start_date"],
            "salary" => $formValues["salary"],
        ]);
        if ($savedId !== null) {
            header("Location: " . file_url("list/list") . "?saved=1");
            exit();
        }
        $errors[] = "Unable to save the listing right now.";
    }
}

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
                            <h1 class="page-title">Basic Form</h1>
                            <div>
                                <ol class="breadcrumb">
                                    <!--<li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>-->
                                    <li class="breadcrumb-item active" aria-current="page">Form</li>
                                </ol>
                            </div>
                        </div>
                        <!-- PAGE-HEADER END -->

                        <!-- Row -->
                        <div class="row">
                            <div class="col-md-12 col-xl-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">General Form Elements</h3>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($errors)): ?>
                                            <div class="alert alert-danger">
                                                <?php foreach (
                                                    $errors
                                                    as $error
                                                ): ?>
                                                    <div><?= htmlspecialchars(
                                                        $error,
                                                    ) ?></div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        <form method="post" action="<?= htmlspecialchars(
                                            $_SERVER["REQUEST_URI"],
                                        ) ?>">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars(
                                                $listingId ?? "",
                                            ) ?>">
                                            <div class="form-group">
                                                <label class="form-label">Name</label>
                                                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars(
                                                    $formValues["name"],
                                                ) ?>" placeholder="Enter full name" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Position</label>
                                                <input type="text" class="form-control" name="position" value="<?= htmlspecialchars(
                                                    $formValues["position"],
                                                ) ?>" placeholder="Enter title" required>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Start Date</label>
                                                        <input type="date" class="form-control" name="start_date" value="<?= htmlspecialchars(
                                                            $formValues[
                                                                "start_date"
                                                            ],
                                                        ) ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Salary</label>
                                                        <input type="text" class="form-control" name="salary" value="<?= htmlspecialchars(
                                                            $formValues[
                                                                "salary"
                                                            ],
                                                        ) ?>" placeholder="Enter salary" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-4 d-flex gap-2">
                                                <button type="submit" class="btn btn-primary"><?= $listingId
                                                    ? "Update"
                                                    : "Add" ?> Listing</button>
                                                <a class="btn btn-secondary" href="<?= file_url(
                                                    "list/list",
                                                ) ?>">Back to Listings</a>
                                            </div>
                                        </form>
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
