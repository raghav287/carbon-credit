<?php
$siteSettings = load_site_settings();
$siteMetaTitle = $siteSettings["title"] ?? "Sash Admin Panel";
$siteMetaDescription =
    $siteSettings["description"] ??
    "Sash – Bootstrap 5 Admin & Dashboard Template";
$siteMetaKeywords =
    $siteSettings["keywords"] ??
    "admin,admin dashboard,admin panel,admin template,bootstrap,clean,dashboard,flat,jquery,modern,responsive,premium admin templates,responsive admin,ui,ui kit.";
$faviconPath = ASSETS_PATH . "/favicon.png";
$faviconUrl = is_file($faviconPath)
    ? asset_url("favicon.png") . "?v=" . (filemtime($faviconPath) ?: time())
    : site_logo_href(true, $siteSettings);
?>
<!doctype html>
<html lang="en" dir="ltr">

<head>

    <!-- META DATA -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="<?= htmlspecialchars(
        $siteMetaDescription,
        ENT_QUOTES,
        "UTF-8",
    ) ?>">
    <meta name="author" content="Spruko Technologies Private Limited">
    <meta name="keywords"
        content="<?= htmlspecialchars(
            $siteMetaKeywords,
            ENT_QUOTES,
            "UTF-8",
        ) ?>">

    <!-- FAVICON -->
    <link rel="icon" type="image/png" href="<?= htmlspecialchars(
        $faviconUrl,
        ENT_QUOTES,
        "UTF-8",
    ) ?>">
    <link rel="shortcut icon" type="image/png" href="<?= htmlspecialchars(
        $faviconUrl,
        ENT_QUOTES,
        "UTF-8",
    ) ?>">

    <!-- TITLE -->
    <title><?= htmlspecialchars(
        $siteMetaTitle,
        ENT_QUOTES,
        "UTF-8",
    ) ?> – <?= htmlspecialchars($pageTitle ?? "Admin", ENT_QUOTES, "UTF-8") ?>
    </title>

    <!-- BOOTSTRAP CSS -->
    <link id="style" href="<?= asset_url(
        "plugins/bootstrap/css/bootstrap.min.css",
    ) ?>" rel="stylesheet">

    <!-- STYLE CSS -->
    <link href="<?= asset_url("css/style.css") ?>" rel="stylesheet">

    <!-- Plugins CSS -->
    <link href="<?= asset_url("css/plugins.css") ?>" rel="stylesheet">

    <!--- FONT-ICONS CSS -->
    <link href="<?= asset_url("css/icons.css") ?>" rel="stylesheet">

    <!-- INTERNAL Switcher css -->
    <link href="<?= asset_url("switcher/css/switcher.css") ?>" rel="stylesheet">
    <link href="<?= asset_url("switcher/demo.css") ?>" rel="stylesheet">

</head>
