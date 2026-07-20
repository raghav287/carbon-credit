<?php
if (!defined("APP_ROOT")) {
    define("APP_ROOT", dirname(__DIR__));
    define("LAYOUT_PATH", APP_ROOT . "/layout");
    define("FILES_PATH", APP_ROOT . "/files");
    define("ASSETS_PATH", APP_ROOT . "/assets");

    // Resolve the admin base URL for local XAMPP folders and live /admin installs.
    $docRoot = isset($_SERVER["DOCUMENT_ROOT"])
        ? realpath($_SERVER["DOCUMENT_ROOT"])
        : "";
    if ($docRoot === "" && preg_match("#^(.*?/htdocs)(/|$)#", APP_ROOT, $m)) {
        $docRoot = $m[1];
    }
    $docRoot = $docRoot ? str_replace("\\", "/", $docRoot) : "";
    $appPath = str_replace("\\", "/", APP_ROOT);

    $relativePath = "";
    if ($docRoot !== "" && strpos($appPath, $docRoot) === 0) {
        $relativePath = substr($appPath, strlen($docRoot)) ?: "";
    }

    $htdocsRelative = "";
    if (preg_match("#/htdocs(.*)$#", $appPath, $m)) {
        $htdocsRelative = $m[1];
    }

    $candidates = array_values(
        array_filter([$relativePath, $htdocsRelative], function ($p) {
            return trim($p, "/") !== "";
        }),
    );
    usort($candidates, function ($a, $b) {
        return strlen($b) <=> strlen($a);
    });

    $baseCandidate = $candidates !== [] ? "/" . trim($candidates[0], "/") : "/admin";
    define("BASE_URL", $baseCandidate);

    if (!function_exists("app_url_join")) {
        function app_url_join(string $base, string $path): string
        {
            $path = trim($path, "/");
            if ($path === "") {
                return $base === "/" ? "/" : rtrim($base, "/");
            }
            if ($base === "/" || $base === "") {
                return "/" . $path;
            }
            return rtrim($base, "/") . "/" . $path;
        }
    }

    if (!function_exists("asset_url")) {
        function asset_url(string $path): string
        {
            return app_url_join(BASE_URL, "assets/" . ltrim($path, "/"));
        }
    }

    if (!function_exists("file_url")) {
        function file_url(string $path): string
        {
            return app_url_join(BASE_URL, "files/" . ltrim($path, "/"));
        }
    }

    // Simple site settings helpers (meta + branding) stored in assets/uploads/site-settings.json.
    if (!function_exists("site_settings_path")) {
        function site_settings_path(): string
        {
            return ASSETS_PATH . "/uploads/site-settings.json";
        }
    }

    if (!function_exists("load_site_settings")) {
        function load_site_settings(): array
        {
            $defaults = [
                "title" => "Sash Admin Panel",
                "description" =>
                    "Sash – Bootstrap 5 Admin & Dashboard Template",
                "keywords" =>
                    "admin,dashboard,bootstrap,sash,template,responsive",
                "logo" => "",
                "favicon" => "",
                "timezone" => "Asia/Kolkata",
                "from_name" => "Balancing Carbon",
                "from_email" => "no-reply@balancingcarbon.com",
                "admin_email" => "info@balancingcarbon.com",
                "smtp_host" => "",
                "smtp_port" => "587",
                "smtp_username" => "",
                "smtp_password" => "",
                "smtp_encryption" => "tls",
                "smtp_enabled" => false,
            ];

            $path = site_settings_path();
            if (is_file($path)) {
                $json = file_get_contents($path);
                $decoded = json_decode($json, true);
                if (is_array($decoded)) {
                    $defaults = array_merge(
                        $defaults,
                        array_intersect_key($decoded, $defaults),
                    );
                }
            }

            return $defaults;
        }
    }

    if (!function_exists("save_site_settings")) {
        function save_site_settings(array $settings): bool
        {
            $path = site_settings_path();
            $dir = dirname($path);
            if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
                return false;
            }

            $allowed = [
                "title", "description", "keywords", "logo", "favicon",
                "timezone", "from_name", "from_email", "admin_email",
                "smtp_host", "smtp_port", "smtp_username", "smtp_password",
                "smtp_encryption", "smtp_enabled"
            ];
            $payload = array_intersect_key($settings, array_flip($allowed));
            if (isset($payload["smtp_enabled"])) {
                $payload["smtp_enabled"] = (bool)$payload["smtp_enabled"];
            }

            $json = json_encode(
                $payload,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
            );
            return $json !== false && file_put_contents($path, $json) !== false;
        }
    }

    if (!function_exists("handleSiteFaviconUpload")) {
        function handleSiteFaviconUpload(array $file): array
        {
            $maxSize = 2 * 1024 * 1024; // 2 MB

            if ($file["error"] === UPLOAD_ERR_NO_FILE) {
                return [
                    "success" => false,
                    "error" => "No favicon file provided.",
                ];
            }
            if ($file["error"] !== UPLOAD_ERR_OK) {
                return [
                    "success" => false,
                    "error" => "Favicon upload failed (code {$file["error"]}).",
                ];
            }
            if (!is_uploaded_file($file["tmp_name"])) {
                return [
                    "success" => false,
                    "error" => "The uploaded favicon is invalid.",
                ];
            }
            if ($file["size"] > $maxSize) {
                return [
                    "success" => false,
                    "error" => "Favicon must be 2 MB or less.",
                ];
            }

            $allowed = [
                "image/png" => "png",
                "image/jpeg" => "jpg",
                "image/gif" => "gif",
                "image/x-icon" => "ico",
                "image/vnd.microsoft.icon" => "ico",
                "application/octet-stream" => "ico",
            ];

            $mimeType = null;
            if (function_exists("finfo_open")) {
                $finfoFlag = defined("FILEINFO_MIME_TYPE")
                    ? FILEINFO_MIME_TYPE
                    : (defined("FINFO_MIME_TYPE") ? FINFO_MIME_TYPE : 16);
                $finfo = finfo_open($finfoFlag);
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
                if (in_array($legacyExtension, ["png", "jpg", "jpeg", "gif", "ico"])) {
                    $extension = $legacyExtension;
                    if ($extension === "jpeg") {
                        $extension = "jpg";
                    }
                }
            }

            if ($extension === null) {
                return [
                    "success" => false,
                    "error" => "Only PNG, JPG, GIF, and ICO files are allowed for the favicon.",
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
                    "error" => "Unable to create upload directory for favicons.",
                ];
            }

            $filename = sprintf("%s.%s", uniqid("favicon_", true), $extension);
            $targetPath = $uploadDir . "/" . $filename;

            if (!move_uploaded_file($file["tmp_name"], $targetPath)) {
                return [
                    "success" => false,
                    "error" => "Unable to save the uploaded favicon.",
                ];
            }

            @chmod($targetPath, 0644);

            return [
                "success" => true,
                "relative_path" => "uploads/branding/" . $filename,
            ];
        }
    }

    if (!function_exists("site_logo_url")) {
        function site_logo_url(?array $settings = null): string
        {
            $settings = $settings ?? load_site_settings();
            if (!empty($settings["logo"])) {
                return asset_url($settings["logo"]);
            }
            return asset_url("images/brand/logo-white.png");
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

            $allowed = [
                "image/png" => "png",
                "image/jpeg" => "jpg",
                "image/gif" => "gif",
                "image/svg+xml" => "svg",
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
                if (in_array($legacyExtension, ["png", "jpg", "jpeg", "gif", "svg"])) {
                    $extension = $legacyExtension === "jpeg" ? "jpg" : $legacyExtension;
                }
            }

            if ($extension === null) {
                return [
                    "success" => false,
                    "error" => "Only PNG, JPG, GIF, and SVG files are allowed for the logo.",
                ];
            }

            $uploadDir = ASSETS_PATH . "/uploads/branding";
            if (
                !is_dir($uploadDir) &&
                !mkdir($uploadDir, 0775, true) &&
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

            @chmod($targetPath, 0644);

            return [
                "success" => true,
                "relative_path" => "uploads/branding/" . $filename,
            ];
        }
    }

    if (!function_exists("site_logo_href")) {
        /**
         * Returns the logo URL, optionally cache-busted when the file exists locally.
         */
        function site_logo_href(
            bool $cacheBuster = false,
            ?array $settings = null,
        ): string {
            $settings = $settings ?? load_site_settings();
            $logoRel = ltrim($settings["logo"] ?? "", "/");
            $relPath =
                $logoRel !== "" ? $logoRel : "images/brand/logo-white.png";
            $url = asset_url($relPath);

            if (!$cacheBuster) {
                return $url;
            }

            $fullPath = ASSETS_PATH . "/" . $relPath;
            if (is_file($fullPath)) {
                $mtime = filemtime($fullPath);
                if ($mtime !== false) {
                    $sep = strpos($url, "?") === false ? "?" : "&";
                    return $url . $sep . "v=" . $mtime;
                }
            }
            return $url;
        }
    }

    if (!function_exists("media_url")) {
        /**
         * Resolve an uploaded media path from assets/uploads (preferred) or files/uploads (legacy).
         */
        function media_url(string $path): string
        {
            $clean = ltrim($path, "/");
            if ($clean === "") {
                return asset_url("images/users/21.jpg");
            }

            $assetPath = ASSETS_PATH . "/" . $clean;
            if (is_file($assetPath)) {
                return asset_url($clean);
            }

            $filePath = FILES_PATH . "/" . $clean;
            if (is_file($filePath)) {
                return file_url($clean);
            }

            // Default to assets path so missing files don't break page layout.
            return asset_url($clean);
        }
    }
}
