<?php
declare(strict_types=1);

function contactRedirect(string $status): void
{
    $token = bin2hex(random_bytes(8));
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $_SESSION["contact_flash"] = $status;
    $_SESSION["contact_flash_token"] = $token;
    header("Location: contact-us?flash={$token}");
    exit;
}

if (($_SERVER["REQUEST_METHOD"] ?? "GET") !== "POST") {
    header("Location: contact-us");
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$message = trim($_POST["message"] ?? "");
$website = trim($_POST["website"] ?? "");
$consent = $_POST["consent"] ?? "";

if ($website !== "") {
    contactRedirect("1");
}

if (
    $name === "" ||
    filter_var($email, FILTER_VALIDATE_EMAIL) === false ||
    $mobile === "" ||
    $message === "" ||
    $consent !== "1"
) {
    contactRedirect("0");
}

function openContactConnection()
{
    if (!function_exists("mysqli_connect")) {
        throw new RuntimeException("The mysqli extension is not available.");
    }

    $isLocalHost =
        empty($_SERVER["HTTP_HOST"]) ||
        in_array($_SERVER["HTTP_HOST"], ["localhost", "127.0.0.1"], true) ||
        strpos($_SERVER["HTTP_HOST"], "localhost:") === 0;

    $candidates = [
        [
            getenv("DB_HOST") ?: "localhost",
            getenv("DB_USER") ?: ($isLocalHost ? "root" : "u586615155_balanccarbon"),
            getenv("DB_PASS") !== false ? getenv("DB_PASS") : ($isLocalHost ? "" : "|gZ9@76!W6k:"),
            getenv("DB_NAME") ?: ($isLocalHost ? "carbon" : "u586615155_balanccarbon"),
            (int) (getenv("DB_PORT") ?: 3306),
        ],
    ];

    if (getenv("DB_USER") === false) {
        $candidates[] = ["localhost", "u586615155_balanccarbon", "|gZ9@76!W6k:", "u586615155_balanccarbon", 3306];
        $candidates[] = ["localhost", "root", "", "carbon", 3306];
    }

    foreach ($candidates as $candidate) {
        [$host, $user, $pass, $db, $port] = $candidate;
        $connection = @mysqli_connect($host, $user, $pass, $db, (int) $port);
        if ($connection) {
            mysqli_set_charset($connection, "utf8mb4");
            return $connection;
        }

        error_log(
            "Contact DB connection failed for {$user}@{$host}/{$db}: " .
            mysqli_connect_error(),
        );
    }

    throw new RuntimeException("Unable to connect to the contact database.");
}

function getContactColumns($connection): array
{
    $columns = [];
    $result = mysqli_query($connection, "SHOW COLUMNS FROM `inquiries`");
    if (!$result) {
        return $columns;
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $columns[$row["Field"]] = true;
    }
    mysqli_free_result($result);

    return $columns;
}

function createContactTable($connection): void
{
    mysqli_query(
        $connection,
        "CREATE TABLE IF NOT EXISTS `inquiries` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `email` varchar(150) NOT NULL,
            `mobile` varchar(50) NOT NULL,
            `message` text NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
    );
}

function normalizeContactTable($connection): void
{
    $columns = getContactColumns($connection);
    if (!isset($columns["id"])) {
        return;
    }

    $idIsAutoIncrement = false;
    $columnResult = mysqli_query($connection, "SHOW COLUMNS FROM `inquiries` LIKE 'id'");
    if ($columnResult && ($row = mysqli_fetch_assoc($columnResult))) {
        $idIsAutoIncrement = stripos((string) $row["Extra"], "auto_increment") !== false;
        mysqli_free_result($columnResult);
    }

    $hasPrimaryKey = false;
    $indexResult = mysqli_query($connection, "SHOW INDEX FROM `inquiries` WHERE Key_name = 'PRIMARY'");
    if ($indexResult) {
        $hasPrimaryKey = mysqli_num_rows($indexResult) > 0;
        mysqli_free_result($indexResult);
    }

    if ($idIsAutoIncrement && $hasPrimaryKey) {
        return;
    }

    $alterParts = [];
    if (!$idIsAutoIncrement) {
        $alterParts[] = "MODIFY `id` int(11) NOT NULL AUTO_INCREMENT";
    }
    if (!$hasPrimaryKey) {
        $alterParts[] = "ADD PRIMARY KEY (`id`)";
    }

    if ($alterParts !== []) {
        mysqli_query($connection, "ALTER TABLE `inquiries` " . implode(", ", $alterParts));
    }
}

function saveContactInquiry($connection, array $columns, string $name, string $email, string $mobile, string $message): bool
{
    $fields = [];
    $values = [];

    if (isset($columns["name"])) {
        $fields[] = "name";
        $values[] = $name;
    }
    if (isset($columns["email"])) {
        $fields[] = "email";
        $values[] = $email;
    }
    if (isset($columns["mobile"])) {
        $fields[] = "mobile";
        $values[] = $mobile;
    }
    if (isset($columns["phone"])) {
        $fields[] = "phone";
        $values[] = $mobile;
    }
    if (isset($columns["service"])) {
        $fields[] = "service";
        $values[] = "contact_form";
    }
    if (isset($columns["address"])) {
        $fields[] = "address";
        $values[] = "";
    }
    if (isset($columns["event_date"])) {
        $fields[] = "event_date";
        $values[] = date("Y-m-d");
    }
    if (isset($columns["message"])) {
        $fields[] = "message";
        $values[] = $message;
    }

    if ($fields === []) {
        return false;
    }

    $safeFields = [];
    foreach ($fields as $field) {
        $safeFields[] = "`" . str_replace("`", "``", $field) . "`";
    }

    $safeValues = [];
    foreach ($values as $value) {
        $safeValues[] = "'" . mysqli_real_escape_string($connection, $value) . "'";
    }

    $sql =
        "INSERT INTO `inquiries` (" .
        implode(", ", $safeFields) .
        ") VALUES (" .
        implode(", ", $safeValues) .
        ")";

    return mysqli_query($connection, $sql) !== false;
}

try {
    $connection = openContactConnection();

    $columns = getContactColumns($connection);
    if ($columns === []) {
        createContactTable($connection);
        $columns = getContactColumns($connection);
    }
    normalizeContactTable($connection);

    $saved = saveContactInquiry($connection, $columns, $name, $email, $mobile, $message);
    if (!$saved) {
        error_log("Contact form submission failed: " . mysqli_error($connection));
    }
    mysqli_close($connection);

    contactRedirect($saved ? "1" : "0");
} catch (Throwable $e) {
    error_log("Contact form failure: " . $e->getMessage());
    contactRedirect("0");
}