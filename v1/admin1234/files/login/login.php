<?php
require_once dirname(__DIR__, 2) . "/app/init.php";
require_once APP_ROOT . "/app/auth.php";

error_log("login.php loaded - Request method: " . $_SERVER["REQUEST_METHOD"]);

if (isAdminLoggedIn()) {
    header("Location: " . file_url("dashboard/dashboard.php"));
    exit();
}

$pageTitle = "Login";
$errors = [];
$identifier = "";
$siteLogoUrl = site_logo_url();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    error_log("login.php POST received");
    error_log("POST data: " . print_r($_POST, true));
    
    $identifier = trim($_POST["identifier"] ?? "");
    $password = $_POST["password"] ?? "";

    error_log("identifier: '$identifier'");
    error_log("password length: " . strlen($password));

    if ($identifier === "" || $password === "") {
        $errors[] = "Username/Email and password are required.";
    } elseif (!attemptAdminLogin($identifier, $password)) {
        $errors[] = "Invalid username/email or password.";
    } else {
        header("Location: " . file_url("dashboard/dashboard.php"));
        exit();
    }
}

include LAYOUT_PATH . "/head.php";
?>

<body class="app sidebar-mini ltr login-img">

    <!-- BACKGROUND-IMAGE -->
    <div class="">

        <!-- GLOBAL LOADER -->
        <div id="global-loader">
            <img src="<?= asset_url(
                "images/loader.svg",
            ) ?>" class="loader-img" alt="Loader">
        </div>
        <!-- /GLOBAL LOADER -->

        <!-- PAGE -->
        <div class="page">
            <div class="">
                <div class="col col-login mx-auto mt-7">
                    <div class="text-center">
                        <a href="<?= file_url(
                            "dashboard",
                        ) ?>"><img src="<?= htmlspecialchars(
    $siteLogoUrl,
    ENT_QUOTES,
    "UTF-8",
) ?>" class="header-brand-img" alt="" style="height:54px;object-fit:contain;"></a>
                    </div>
                </div>

                <div class="container-login100">
                    <div class="wrap-login100 p-6">
                        <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                            <div><?= htmlspecialchars($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        <form method="post"
                            action="<?= htmlspecialchars($_SERVER["REQUEST_URI"], ENT_QUOTES, "UTF-8") ?>"
                            class="login100-form validate-form">
                            <span class="login100-form-title pb-5">
                                Login
                            </span>
                            <div class="wrap-input100 validate-input input-group mb-3"
                                data-bs-validate="Username or email is required">
                                <a href="javascript:void(0)" class="input-group-text bg-white text-muted">
                                    <i class="zmdi zmdi-account text-muted" aria-hidden="true"></i>
                                </a>
                                <input class="input100 border-start-0 form-control ms-0" type="text" name="identifier"
                                    value="<?= htmlspecialchars(
                                    $identifier,
                                ) ?>" placeholder="Username or Email address" required>
                            </div>
                            <div class="wrap-input100 validate-input input-group mb-4"
                                data-bs-validate="Password is required">
                                <a href="javascript:void(0)"
                                    class="input-group-text bg-white text-muted password-toggle"
                                    data-password-target="#loginPassword">
                                    <i class="zmdi zmdi-eye text-muted" aria-hidden="true"></i>
                                </a>
                                <input id="loginPassword" class="input100 border-start-0 form-control ms-0"
                                    type="password" name="password" placeholder="Password" required>
                            </div>
                            <div class="text-end pt-1">
                                <!--<p class="mb-0"><a href="javascript:void(0)" class="text-primary ms-1">Forgot Password?</a></p>-->
                            </div>
                            <div class="container-login100-form-btn">
                                <button type="submit" class="login100-form-btn btn-primary">
                                    Login
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JQUERY JS -->
    <script src="<?= asset_url("js/jquery.min.js") ?>"></script>
    <!-- BOOTSTRAP JS -->
    <script src="<?= asset_url(
        "plugins/bootstrap/js/popper.min.js",
    ) ?>"></script>
    <script src="<?= asset_url(
        "plugins/bootstrap/js/bootstrap.min.js",
    ) ?>"></script>
    <!-- SHOW PASSWORD JS -->
    <script src="<?= asset_url("js/show-password.min.js") ?>"></script>
    <!-- CUSTOM JS -->
    <script src="<?= asset_url("js/custom.js") ?>"></script>
    <script>
    (function() {
        document.addEventListener("click", function(event) {
            const toggle = event.target.closest("[data-password-target]");
            if (!toggle) {
                return;
            }
            event.preventDefault();

            const targetSelector = toggle.getAttribute("data-password-target");
            if (!targetSelector) {
                return;
            }

            const passwordInput = document.querySelector(targetSelector);
            if (
                !passwordInput ||
                !(passwordInput instanceof HTMLInputElement)
            ) {
                return;
            }

            const isPassword = passwordInput.type === "password";
            passwordInput.type = isPassword ? "text" : "password";

            const icon = toggle.querySelector("i");
            if (!icon) {
                return;
            }

            if (isPassword) {
                icon.classList.remove("zmdi-eye");
                icon.classList.add("zmdi-eye-off");
            } else {
                icon.classList.remove("zmdi-eye-off");
                icon.classList.add("zmdi-eye");
            }
        });
    })();
    </script>

</body>

</html>
