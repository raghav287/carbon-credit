    <!-- BACK-TO-TOP -->
    <a href="#top" id="back-to-top"><i class="fa fa-angle-up"></i></a>

    <!-- JQUERY JS -->
    <script src="<?= asset_url("js/jquery.min.js") ?>"></script>

    <!-- BOOTSTRAP JS -->
    <script src="<?= asset_url(
        "plugins/bootstrap/js/popper.min.js",
    ) ?>"></script>
    <script src="<?= asset_url(
        "plugins/bootstrap/js/bootstrap.min.js",
    ) ?>"></script>

    <!-- SPARKLINE JS-->
    <script src="<?= asset_url("js/jquery.sparkline.min.js") ?>"></script>

    <!-- Sticky js -->
    <script src="<?= asset_url("js/sticky.js") ?>"></script>

    <!-- CHART-CIRCLE JS-->
    <script src="<?= asset_url("js/circle-progress.min.js") ?>"></script>

    <!-- PIETY CHART JS-->
    <script src="<?= asset_url(
        "plugins/peitychart/jquery.peity.min.js",
    ) ?>"></script>
    <script src="<?= asset_url(
        "plugins/peitychart/peitychart.init.js",
    ) ?>"></script>

    <!-- SIDEBAR JS -->
    <script src="<?= asset_url("plugins/sidebar/sidebar.js") ?>"></script>

    <!-- Perfect SCROLLBAR JS-->
    <script src="<?= asset_url(
        "plugins/p-scroll/perfect-scrollbar.js",
    ) ?>"></script>
    <script src="<?= asset_url("plugins/p-scroll/pscroll.js") ?>"></script>
    <script src="<?= asset_url("plugins/p-scroll/pscroll-1.js") ?>"></script>

    <!-- SIDE-MENU JS-->
    <script src="<?= asset_url("plugins/sidemenu/sidemenu.js") ?>"></script>

    <!-- TypeHead js -->
    <script src="<?= asset_url(
        "plugins/bootstrap5-typehead/autocomplete.js",
    ) ?>"></script>
    <script src="<?= asset_url("js/typehead.js") ?>"></script>

    <!-- INTERNAL CHARTJS CHART JS-->
    <script src="<?= asset_url("plugins/chart/Chart.bundle.js") ?>"></script>
    <script src="<?= asset_url("plugins/chart/utils.js") ?>"></script>

    <!-- INTERNAL SELECT2 JS -->
    <script src="<?= asset_url(
        "plugins/select2/select2.full.min.js",
    ) ?>"></script>

    <!-- INTERNAL Data tables js-->
    <script src="<?= asset_url(
        "plugins/datatable/js/jquery.dataTables.min.js",
    ) ?>"></script>
    <script src="<?= asset_url(
        "plugins/datatable/js/dataTables.bootstrap5.js",
    ) ?>"></script>
    <script src="<?= asset_url(
        "plugins/datatable/dataTables.responsive.min.js",
    ) ?>"></script>

    <!-- INTERNAL APEXCHART JS -->
    <script src="<?= asset_url("js/apexcharts.js") ?>"></script>
    <script src="<?= asset_url(
        "plugins/apexchart/irregular-data-series.js",
    ) ?>"></script>

    <!-- INTERNAL Flot JS -->
    <script src="<?= asset_url("plugins/flot/jquery.flot.js") ?>"></script>
    <script src="<?= asset_url(
        "plugins/flot/jquery.flot.fillbetween.js",
    ) ?>"></script>
    <script src="<?= asset_url(
        "plugins/flot/chart.flot.sampledata.js",
    ) ?>"></script>
    <script src="<?= asset_url(
        "plugins/flot/dashboard.sampledata.js",
    ) ?>"></script>

    <!-- theme Color js -->
    <script src="<?= asset_url("js/themeColors.js") ?>"></script>

    <!-- CUSTOM JS -->
    <script src="<?= asset_url("js/custom.js") ?>"></script>

    <!-- Custom-switcher -->
    <script src="<?= asset_url("js/custom-swicher.js") ?>"></script>

    <!-- Switcher js -->
    <script src="<?= asset_url("switcher/js/switcher.js") ?>"></script>

    <script>
        (function () {
            document.addEventListener("click", function (event) {
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
    <script>
        (function () {
            document.addEventListener("DOMContentLoaded", function () {
                document.querySelectorAll(".alert").forEach((alert) => {
                    const attr = alert.getAttribute("data-autohide");
                    const duration =
                        attr !== null ? Number(attr) || 4000 : 4000;
                    alert.classList.add("fade", "show");
                    setTimeout(() => {
                        alert.classList.remove("show");
                        setTimeout(() => {
                            alert.remove();
                        }, 300);
                    }, duration);
                });
            });
        })();
    </script>
