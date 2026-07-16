<?php
require_once dirname(__DIR__, 2) . "/app/init.php";
require_once APP_ROOT . "/app/auth.php";
requireAdminLogin();

if (!function_exists("handleAdminProfileImageUpload")) {
    function handleAdminProfileImageUpload(array $file): array
    {
        $maxSize = 2 * 1024 * 1024;

        if ($file["error"] !== UPLOAD_ERR_OK) {
            return [
                "success" => false,
                "error" => "Profile picture upload failed (code {$file["error"]}).",
            ];
        }

        if (!is_uploaded_file($file["tmp_name"])) {
            return [
                "success" => false,
                "error" => "The uploaded profile picture is invalid.",
            ];
        }

        if ($file["size"] > $maxSize) {
            return [
                "success" => false,
                "error" => "Profile picture must be 2 MB or less.",
            ];
        }

        $allowed = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/gif" => "gif",
        ];

        $mimeType = null;
        if (function_exists("finfo_open")) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo !== false) {
                $mimeType = finfo_file($finfo, $file["tmp_name"]);
                finfo_close($finfo);
            }
        }

        if ($mimeType === null && function_exists("mime_content_type")) {
            $mimeType = mime_content_type($file["tmp_name"]);
        }

        $extension =
            $mimeType !== null && isset($allowed[$mimeType])
                ? $allowed[$mimeType]
                : null;

        if ($extension === null) {
            $legacyExtension = strtolower(
                pathinfo($file["name"], PATHINFO_EXTENSION),
            );
            if ($legacyExtension === "jpeg") {
                $legacyExtension = "jpg";
            }
            if (
                $legacyExtension === "jpg" ||
                $legacyExtension === "png" ||
                $legacyExtension === "gif"
            ) {
                $extension = $legacyExtension;
            }
        }

        if ($extension === null) {
            return [
                "success" => false,
                "error" =>
                    "Only JPG, PNG, and GIF files are allowed for the profile picture.",
            ];
        }

        $uploadDir = ASSETS_PATH . "/uploads/profile";
        if (
            !is_dir($uploadDir) &&
            !mkdir($uploadDir, 0755, true) &&
            !is_dir($uploadDir)
        ) {
            return [
                "success" => false,
                "error" =>
                    "Unable to create upload directory for profile pictures.",
            ];
        }

        $filename = sprintf("%s.%s", uniqid("profile_", true), $extension);
        $targetPath = $uploadDir . "/" . $filename;

        if (!move_uploaded_file($file["tmp_name"], $targetPath)) {
            return [
                "success" => false,
                "error" => "Unable to save the uploaded profile picture.",
            ];
        }

        return [
            "success" => true,
            "relative_path" => "uploads/profile/{$filename}",
        ];
    }
}

if (!function_exists("handleSiteLogoUpload")) {
    function handleSiteLogoUpload(array $file): array
    {
        $maxSize = 3 * 1024 * 1024; // 3 MB
        if ($file["error"] === UPLOAD_ERR_NO_FILE) {
            return [
                "success" => false,
                "error" => "No logo file provided.",
            ];
        }
        if ($file["error"] !== UPLOAD_ERR_OK) {
            return [
                "success" => false,
                "error" => "Logo upload failed (code {$file["error"]}).",
            ];
        }
        if (!is_uploaded_file($file["tmp_name"])) {
            return [
                "success" => false,
                "error" => "The uploaded logo is invalid.",
            ];
        }
        if ($file["size"] > $maxSize) {
            return [
                "success" => false,
                "error" => "Logo must be 3 MB or less.",
            ];
        }

        // Force PNG branding to keep consistent rendering across the app.
        $allowed = ["image/png" => "png"];

        $mimeType = null;
        if (function_exists("finfo_open")) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo !== false) {
                $mimeType = finfo_file($finfo, $file["tmp_name"]);
                finfo_close($finfo);
            }
        }
        if ($mimeType === null && function_exists("mime_content_type")) {
            $mimeType = mime_content_type($file["tmp_name"]);
        }

        $extension =
            $mimeType !== null && isset($allowed[$mimeType])
                ? $allowed[$mimeType]
                : null;

        if ($extension === null) {
            $legacyExtension = strtolower(
                pathinfo($file["name"], PATHINFO_EXTENSION),
            );
            if ($legacyExtension === "png") {
                $extension = "png";
            }
        }

        if ($extension === null) {
            return [
                "success" => false,
                "error" => "Only PNG logo files are allowed.",
            ];
        }

        $uploadDir = ASSETS_PATH . "/uploads/branding";
        if (
            !is_dir($uploadDir) &&
            !mkdir($uploadDir, 0755, true) &&
            !is_dir($uploadDir)
        ) {
            return [
                "success" => false,
                "error" => "Unable to create upload directory for logos.",
            ];
        }

        $filename = sprintf("%s.%s", uniqid("logo_", true), $extension);
        $targetPath = $uploadDir . "/" . $filename;

        if (!move_uploaded_file($file["tmp_name"], $targetPath)) {
            return [
                "success" => false,
                "error" => "Unable to save the uploaded logo.",
            ];
        }

        return [
            "success" => true,
            "relative_path" => "uploads/branding/{$filename}",
        ];
    }
}

$pageTitle = "Edit Profile";
$errors = [];
$passwordErrors = [];
$profileSaved = false;
$passwordUpdated = false;
$siteMetaErrors = [];
$siteMetaSaved = false;

$adminId = (int) ($_SESSION["admin_id"] ?? 0);
$adminAccount = getAdminUser($adminId) ?? [];
$siteSettings = load_site_settings();
$siteLogoUrl = site_logo_url($siteSettings);
$formValues = [
    "username" => $adminAccount["username"] ?? "",
];
$existingProfilePicture = $adminAccount["profile_picture"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mode = $_POST["mode"] ?? "";
    if ($mode === "profile") {
        $formValues["username"] = trim($_POST["username"] ?? "");
        $profilePictureRelative = null;

        if ($formValues["username"] === "") {
            $errors[] = "Username is required.";
        }

        if (
            isset($_FILES["profile_picture"]) &&
            is_array($_FILES["profile_picture"]) &&
            $_FILES["profile_picture"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {
            $uploadResult = handleAdminProfileImageUpload(
                $_FILES["profile_picture"],
            );
            if (!$uploadResult["success"]) {
                $errors[] = $uploadResult["error"];
            } else {
                $profilePictureRelative = $uploadResult["relative_path"];
            }
        }

        if (empty($errors)) {
            $adminEmail =
                $adminAccount["email"] ?? ($_SESSION["admin_email"] ?? "");
            if ($adminEmail === "") {
                $errors[] = "Unable to resolve the current email address.";
            } else {
                $adminUpdated = updateAdminAccount(
                    $adminId,
                    $formValues["username"],
                    $adminEmail,
                    $profilePictureRelative,
                );
                if ($adminUpdated) {
                    $profileSaved = true;
                    $_SESSION["admin_username"] = $formValues["username"];
                    if ($profilePictureRelative !== null) {
                        $_SESSION[
                            "admin_profile_picture"
                        ] = $profilePictureRelative;
                        // Delete old profile picture if it exists and is different.
                        if (
                            $existingProfilePicture !== "" &&
                            $existingProfilePicture !== $profilePictureRelative
                        ) {
                            $oldAsset =
                                ASSETS_PATH .
                                "/" .
                                ltrim($existingProfilePicture, "/");
                            $oldFile =
                                FILES_PATH .
                                "/" .
                                ltrim($existingProfilePicture, "/");
                            foreach ([$oldAsset, $oldFile] as $oldPath) {
                                if (
                                    is_file($oldPath) &&
                                    (str_starts_with(
                                        realpath($oldPath) ?: "",
                                        realpath(ASSETS_PATH) ?: "",
                                    ) ||
                                        str_starts_with(
                                            realpath($oldPath) ?: "",
                                            realpath(FILES_PATH) ?: "",
                                        ))
                                ) {
                                    @unlink($oldPath);
                                }
                            }
                        }
                    }
                    $adminAccount = getAdminUser($adminId) ?? [];
                    $formValues["username"] = $adminAccount["username"] ?? "";
                } else {
                    $errors[] = "Unable to update profile details right now.";
                }
            }
        }
    } elseif ($mode === "password") {
        $currentPassword = $_POST["current_password"] ?? "";
        $newPassword = $_POST["new_password"] ?? "";
        $confirmPassword = $_POST["confirm_password"] ?? "";

        if ($currentPassword === "") {
            $passwordErrors[] = "Current password is required.";
        }
        if ($newPassword === "") {
            $passwordErrors[] = "New password is required.";
        }
        if ($confirmPassword === "") {
            $passwordErrors[] = "Confirm password is required.";
        }
        if (
            $newPassword !== "" &&
            $confirmPassword !== "" &&
            $newPassword !== $confirmPassword
        ) {
            $passwordErrors[] = "New password and confirmation must match.";
        }

        if (empty($passwordErrors)) {
            $adminId = $_SESSION["admin_id"] ?? null;
            if (
                $adminId === null ||
                !updateAdminPassword(
                    (int) $adminId,
                    $currentPassword,
                    $newPassword,
                )
            ) {
                $passwordErrors[] =
                    "Current password is incorrect or cannot be updated.";
            } else {
                $passwordUpdated = true;
            }
        }
    } elseif ($mode === "site_meta") {
        $siteTitle = trim($_POST["site_title"] ?? "");
        $metaDescription = trim($_POST["meta_description"] ?? "");
        $metaKeywords = trim($_POST["meta_keywords"] ?? "");
        $logoPath = $siteSettings["logo"] ?? "";

        if ($siteTitle === "") {
            $siteMetaErrors[] = "Site title is required.";
        }

        if (
            isset($_FILES["site_logo"]) &&
            is_array($_FILES["site_logo"]) &&
            $_FILES["site_logo"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {
            $uploadResult = handleSiteLogoUpload($_FILES["site_logo"]);
            if (!$uploadResult["success"]) {
                $siteMetaErrors[] = $uploadResult["error"];
            } else {
                // Delete previous logo if we successfully store a new one.
                $oldLogo = $logoPath;
                $logoPath = $uploadResult["relative_path"];
                if ($oldLogo !== "" && $oldLogo !== $logoPath) {
                    $oldFull = ASSETS_PATH . "/" . ltrim($oldLogo, "/");
                    if (
                        is_file($oldFull) &&
                        str_starts_with(
                            realpath($oldFull) ?: "",
                            realpath(ASSETS_PATH) ?: "",
                        )
                    ) {
                        @unlink($oldFull);
                    }
                }
            }
        }

        if (empty($siteMetaErrors)) {
            $newSettings = [
                "title" => $siteTitle,
                "description" => $metaDescription,
                "keywords" => $metaKeywords,
                "logo" => $logoPath,
            ];
            if (save_site_settings($newSettings)) {
                $siteMetaSaved = true;
                $siteSettings = load_site_settings();
                // Also refresh favicon.png so browsers pick up the new logo.
                $sourceLogo = ASSETS_PATH . "/" . ltrim($logoPath, "/");
                $faviconDest = ASSETS_PATH . "/favicon.png";
                if (is_file($sourceLogo)) {
                    @copy($sourceLogo, $faviconDest);
                }
            } else {
                $siteMetaErrors[] = "Unable to save site settings right now.";
            }
        }
    }
}

$profilePicturePath = $adminAccount["profile_picture"] ?? "";
$profilePictureUrl = media_url($profilePicturePath);
$siteLogoUrl = site_logo_url($siteSettings);

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
                            <h1 class="page-title">Edit Profile</h1>
                            <div>
                                <ol class="breadcrumb">

                                    <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
                                </ol>
                            </div>
                        </div>
                        <!-- PAGE-HEADER END -->

                        <!-- ROW-1 OPEN -->
                        <div class="row">
                            <div class="col-xl-4">
                                <form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="mode" value="password">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="card-title">Update Password</div>
                                        </div>
                                        <div class="card-body">
                                            <?php if ($passwordUpdated): ?>
                                                <div class="alert alert-success mb-3">
                                                    Password updated successfully.
                                                </div>
                                            <?php elseif (
                                                !empty($passwordErrors)
                                            ): ?>
                                                <div class="alert alert-danger mb-3">
                                                    <?php foreach (
                                                        $passwordErrors
                                                        as $error
                                                    ): ?>
                                                        <div><?= htmlspecialchars(
                                                            $error,
                                                        ) ?></div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="text-center chat-image mb-5">
                                                <div class="avatar avatar-xxl chat-profile mb-3 brround">
                                                    <img alt="avatar" src="<?= htmlspecialchars(
                                                        $profilePictureUrl,
                                                        ENT_QUOTES,
                                                        "UTF-8",
                                                    ) ?>" class="brround"></a>
                                                </div>
                                                <div class="main-chat-msg-name">
                                                    <a href="<?= file_url(
                                                        "profile/profile",
                                                    ) ?>">
                                                        <h5 class="mb-1 text-dark fw-semibold"><?= htmlspecialchars(
                                                            $adminAccount[
                                                                "username"
                                                            ] ?? "Admin User",
                                                        ) ?></h5>
                                                    </a>
                                                    <p class="text-muted mt-0 mb-0 pt-0 fs-13"><?= htmlspecialchars(
                                                        $adminAccount[
                                                            "email"
                                                        ] ?? "",
                                                    ) ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Current Password</label>
                                            <div class="wrap-input100 validate-input input-group" id="Password-toggle">
                                                    <a href="javascript:void(0)" class="input-group-text bg-white text-muted password-toggle" data-password-target="#currentPassword">
                                                    <i class="zmdi zmdi-eye text-muted" aria-hidden="true"></i>
                                                </a>
                                                    <input id="currentPassword" class="input100 form-control" type="password" name="current_password" placeholder="Current Password" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">New Password</label>
                                                <div class="wrap-input100 validate-input input-group" id="Password-toggle1">
                                                    <a href="javascript:void(0)" class="input-group-text bg-white text-muted password-toggle" data-password-target="#newPassword">
                                                    <i class="zmdi zmdi-eye text-muted" aria-hidden="true"></i>
                                                </a>
                                                    <input id="newPassword" class="input100 form-control" type="password" name="new_password" placeholder="New Password" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Confirm Password</label>
                                                <div class="wrap-input100 validate-input input-group" id="Password-toggle2">
                                                    <a href="javascript:void(0)" class="input-group-text bg-white text-muted password-toggle" data-password-target="#confirmPassword">
                                                    <i class="zmdi zmdi-eye text-muted" aria-hidden="true"></i>
                                                </a>
                                                    <input id="confirmPassword" class="input100 form-control" type="password" name="confirm_password" placeholder="Confirm Password" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer text-end">
                                            <button class="btn btn-primary" type="submit">Update Password</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-xl-8">
                                <form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8') ?>" enctype="multipart/form-data">
                                    <input type="hidden" name="mode" value="profile">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Update Profile Information</h3>
                                        </div>
                                        <div class="card-body">
                                            <?php if ($profileSaved): ?>
                                                <div class="alert alert-success mb-4">
                                                    Profile updated successfully.
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($errors)): ?>
                                                <div class="alert alert-danger mb-4">
                                                    <?php foreach (
                                                        $errors
                                                        as $error
                                                    ): ?>
                                                        <div><?= htmlspecialchars(
                                                            $error,
                                                            ENT_QUOTES,
                                                            "UTF-8",
                                                        ) ?></div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>

                                            <div class="form-group">
                                                <label class="form-label">User Name</label>
                                                <input type="text" class="form-control" name="username" value="<?= htmlspecialchars(
                                                    $formValues["username"],
                                                    ENT_QUOTES,
                                                    "UTF-8",
                                                ) ?>" required>
                                            </div>
                                            <div class="form-group mt-3">
                                                <label class="form-label mt-0">Profile Picture</label>
                                                <input type="file" class="form-control" name="profile_picture" accept="image/*">
                                                <small class="text-muted">Allowed formats: PNG, JPG | Recommended size: 400 × 400 PX | Max size: 3 MB</small>

                                            </div>
                                        </div>
                                        <div class="card-footer text-end">
                                            <button type="submit" class="btn btn-success">Save Changes</button>
                                            <!--<a href="" class="btn btn-danger">Cancel</a>-->
                                        </div>
                                    </div>
                                </form>
                                <form class="mt-4" method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8') ?>" enctype="multipart/form-data">
                                    <input type="hidden" name="mode" value="site_meta">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Update Site Meta & Logo</h3>
                                        </div>
                                        <div class="card-body">
                                            <?php if ($siteMetaSaved): ?>
                                                <div class="alert alert-success mb-4">
                                                    Site meta and logo updated.
                                                </div>
                                            <?php endif; ?>
                                            <?php if (
                                                !empty($siteMetaErrors)
                                            ): ?>
                                                <div class="alert alert-danger mb-4">
                                                    <?php foreach (
                                                        $siteMetaErrors
                                                        as $error
                                                    ): ?>
                                                        <div><?= htmlspecialchars(
                                                            $error,
                                                            ENT_QUOTES,
                                                            "UTF-8",
                                                        ) ?></div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>

                                            <div class="form-group">
                                                <label class="form-label">Site Title</label>
                                                <input type="text" class="form-control" name="site_title" value="<?= htmlspecialchars(
                                                    $siteSettings["title"] ??
                                                        "",
                                                    ENT_QUOTES,
                                                    "UTF-8",
                                                ) ?>" required>
                                            </div>
                                            <div class="form-group mt-3">
                                                <label class="form-label">Meta Description</label>
                                                <textarea class="form-control" name="meta_description" rows="2" placeholder="Short description for SEO"><?= htmlspecialchars(
                                                    $siteSettings[
                                                        "description"
                                                    ] ?? "",
                                                    ENT_QUOTES,
                                                    "UTF-8",
                                                ) ?></textarea>
                                            </div>
                                            <div class="form-group mt-3">
                                                <label class="form-label">Meta Keywords</label>
                                                <input type="text" class="form-control" name="meta_keywords" value="<?= htmlspecialchars(
                                                    $siteSettings["keywords"] ??
                                                        "",
                                                    ENT_QUOTES,
                                                    "UTF-8",
                                                ) ?>" placeholder="comma,separated,keywords">
                                            </div>
                                            <div class="form-group mt-4">

                                                <div class="d-flex align-items-center gap-3 flex-wrap">

                                                    <div class="flex-grow-1">
                                                        <label class="form-label mb-1">Upload New Logo</label>
                                                        <input type="file" class="form-control" name="site_logo" accept="image/png,image/jpeg,image/gif,image/svg+xml">
                                                        <small class="text-muted">Allowed formats: PNG | Recommended size: 107 × 40 PX | Max size: 3 MB</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer text-end">
                                            <button type="submit" class="btn btn-primary">Save Meta & Logo</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- ROW-1 CLOSED -->

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
