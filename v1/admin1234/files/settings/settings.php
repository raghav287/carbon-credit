<?php
require_once dirname(__DIR__, 2) . "/app/init.php";
require_once APP_ROOT . "/app/auth.php";
requireAdminLogin();

$pageTitle = "Settings";
$settings = load_site_settings();
$success = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newSettings = [
        "title" => trim($_POST["site_title"] ?? ""),
        "description" => trim($_POST["site_description"] ?? ""),
        "keywords" => trim($_POST["site_keywords"] ?? ""),
        "timezone" => trim($_POST["timezone"] ?? ""),
        "from_name" => trim($_POST["from_name"] ?? ""),
        "from_email" => trim($_POST["from_email"] ?? ""),
        "admin_email" => trim($_POST["admin_email"] ?? ""),
        "smtp_host" => trim($_POST["smtp_host"] ?? ""),
        "smtp_port" => trim($_POST["smtp_port"] ?? ""),
        "smtp_username" => trim($_POST["smtp_username"] ?? ""),
        "smtp_password" => trim($_POST["smtp_password"] ?? ""),
        "smtp_encryption" => trim($_POST["smtp_encryption"] ?? ""),
        "smtp_enabled" => isset($_POST["smtp_enabled"]) ? true : false,
        "favicon" => $settings["favicon"] ?? "",
        "logo" => $settings["logo"] ?? "",
    ];

    if (
        isset($_FILES["favicon"]) &&
        is_array($_FILES["favicon"]) &&
        $_FILES["favicon"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {
        $uploadResult = handleSiteFaviconUpload($_FILES["favicon"]);
        if (!$uploadResult["success"]) {
            $errors[] = $uploadResult["error"];
        } else {
            $newSettings["favicon"] = $uploadResult["relative_path"];
        }
    }

    if (
        isset($_FILES["site_logo"]) &&
        is_array($_FILES["site_logo"]) &&
        $_FILES["site_logo"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {
        $uploadResult = handleSiteLogoUpload($_FILES["site_logo"]);
        if (!$uploadResult["success"]) {
            $errors[] = $uploadResult["error"];
        } else {
            $newSettings["logo"] = $uploadResult["relative_path"];
        }
    }

    if (empty($errors)) {
        if (save_site_settings($newSettings)) {
            $settings = load_site_settings();
            $success = "Settings saved successfully!";
        } else {
            $errors[] = "Failed to save settings, please try again.";
        }
    }
}

include LAYOUT_PATH . "/head.php";
?>

<body class="app sidebar-mini ltr light-mode">

    <!-- GLOBAL-LOADER -->
    <div id="global-loader">
        <img src="<?= asset_url("images/loader.svg") ?>" class="loader-img" alt="Loader">
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
                            <h1 class="page-title">Settings</h1>
                            <div>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= file_url("dashboard/dashboard.php") ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Settings</li>
                                </ol>
                            </div>
                        </div>
                        <!-- PAGE-HEADER END -->

                        <!-- Success/Error Alerts -->
                        <div id="alert-container">
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php foreach ($errors as $error): ?>
                                        <div><?= htmlspecialchars($error) ?></div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($success !== ""): ?>
                                <div class="alert alert-success" role="alert">
                                    <?= htmlspecialchars($success) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Row -->
                        <div class="row">
                            <div class="col-lg-4 col-xl-3">
                                <div class="card">
                                    <div class="card-body p-0">
                                        <div class="list-group list-group-transparent mb-0 settings-tab">
                                            <a href="#general" data-bs-toggle="tab" class="list-group-item list-group-item-action active d-flex align-items-center">
                                                <i class="fe fe-settings me-3 fs-16"></i> General
                                            </a>
                                            <a href="#smtp" data-bs-toggle="tab" class="list-group-item list-group-item-action d-flex align-items-center">
                                                <i class="fe fe-mail me-3 fs-16"></i> SMTP Settings
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8 col-xl-9">
                                <form method="post" enctype="multipart/form-data">
                                    <div class="tab-content">
                                        <!-- General Settings -->
                                        <div class="tab-pane active" id="general">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">General Settings</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Site Title</label>
                                                        <input type="text" name="site_title" class="form-control"
                                                               value="<?= htmlspecialchars($settings["title"] ?? "") ?>">
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Site Description</label>
                                                        <textarea name="site_description" class="form-control"
                                                                  rows="3"><?= htmlspecialchars($settings["description"] ?? "") ?></textarea>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Site Keywords</label>
                                                        <input type="text" name="site_keywords" class="form-control"
                                                               value="<?= htmlspecialchars($settings["keywords"] ?? "") ?>">
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Timezone</label>
                                                        <select name="timezone" class="form-control form-select">
                                                            <?php
                                                            $timezoneList = DateTimeZone::listIdentifiers();
                                                            $currentTimezone = $settings["timezone"] ?? "Asia/Kolkata";
                                                            foreach ($timezoneList as $tz):
                                                            ?>
                                                                <option value="<?= htmlspecialchars($tz) ?>" <?= $tz === $currentTimezone ? "selected" : "" ?>>
                                                                    <?= htmlspecialchars($tz) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Logo</label>
                                                        <?php if (!empty($settings["logo"])): ?>
                                                            <div class="mb-2">
                                                                <img src="<?= asset_url($settings["logo"]) ?>" alt="Logo" style="max-width: 180px; max-height: 80px;">
                                                            </div>
                                                        <?php endif; ?>
                                                        <input type="file" name="site_logo" class="form-control" accept="image/png,image/jpeg,image/gif,image/svg+xml">
                                                        <small class="text-muted">Upload a PNG, JPG, GIF, or SVG file (max 3 MB).</small>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Favicon</label>
                                                        <?php if (!empty($settings["favicon"])): ?>
                                                            <div class="mb-2">
                                                                <img src="<?= asset_url($settings["favicon"]) ?>" alt="Favicon" style="max-width: 64px; max-height: 64px;">
                                                            </div>
                                                        <?php endif; ?>
                                                        <input type="file" name="favicon" class="form-control" accept="image/png,image/jpeg,image/gif,image/x-icon">
                                                        <small class="text-muted">Upload a PNG, JPG, GIF, or ICO file (max 2 MB).</small>
                                                    </div>
                                                    <hr class="my-4">
                                                    <h4 class="mb-3">Email Settings</h4>
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">From Name</label>
                                                        <input type="text" name="from_name" class="form-control"
                                                               value="<?= htmlspecialchars($settings["from_name"] ?? "") ?>"
                                                               placeholder="e.g., Balancing Carbon">
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">From Email</label>
                                                        <input type="email" name="from_email" class="form-control"
                                                               value="<?= htmlspecialchars($settings["from_email"] ?? "") ?>"
                                                               placeholder="e.g., no-reply@balancingcarbon.com">
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Admin Notification Email</label>
                                                        <input type="email" name="admin_email" class="form-control"
                                                               value="<?= htmlspecialchars($settings["admin_email"] ?? "") ?>"
                                                               placeholder="e.g., info@balancingcarbon.com">
                                                        <small class="text-muted">This email will receive new contact form notifications.</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- SMTP Settings -->
                                        <div class="tab-pane" id="smtp">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">SMTP Settings</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group mb-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                   name="smtp_enabled" id="smtp_enabled"
                                                                <?= ($settings["smtp_enabled"] ?? false) ? "checked" : "" ?>>
                                                            <label class="form-check-label" for="smtp_enabled">
                                                                Use SMTP for sending emails
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div id="smtp_settings" style="display: <?= ($settings["smtp_enabled"] ?? false) ? "block" : "none" ?>;">
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">SMTP Host</label>
                                                                <input type="text" name="smtp_host" class="form-control"
                                                                       value="<?= htmlspecialchars($settings["smtp_host"] ?? "") ?>"
                                                                       placeholder="e.g., smtp.gmail.com">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">SMTP Port</label>
                                                                <input type="number" name="smtp_port" class="form-control"
                                                                       value="<?= htmlspecialchars($settings["smtp_port"] ?? "587") ?>"
                                                                       placeholder="e.g., 587">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">SMTP Username</label>
                                                                <input type="text" name="smtp_username" class="form-control"
                                                                       value="<?= htmlspecialchars($settings["smtp_username"] ?? "") ?>"
                                                                       placeholder="e.g., your-email@gmail.com">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">SMTP Password</label>
                                                                <input type="password" name="smtp_password" class="form-control"
                                                                       value="<?= htmlspecialchars($settings["smtp_password"] ?? "") ?>"
                                                                       placeholder="Your SMTP password">
                                                            </div>
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label class="form-label">SMTP Encryption</label>
                                                            <select name="smtp_encryption" class="form-control form-select">
                                                                <option value="tls" <?= ($settings["smtp_encryption"] ?? "tls") === "tls" ? "selected" : "" ?>>
                                                                    TLS
                                                                </option>
                                                                <option value="ssl" <?= ($settings["smtp_encryption"] ?? "tls") === "ssl" ? "selected" : "" ?>>
                                                                    SSL
                                                                </option>
                                                                <option value="" <?= ($settings["smtp_encryption"] ?? "tls") === "" ? "selected" : "" ?>>
                                                                    None
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
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
    <script>
        // SMTP toggle functionality
        const smtpToggle = document.getElementById('smtp_enabled');
        const smtpSettings = document.getElementById('smtp_settings');

        if (smtpToggle && smtpSettings) {
            smtpToggle.addEventListener('change', function() {
                smtpSettings.style.display = this.checked ? 'block' : 'none';
            });
        }
    </script>

</body>
</html>
