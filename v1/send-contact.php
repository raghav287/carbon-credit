<?php
declare(strict_types=1);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: contact-us.php");
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$message = trim($_POST["message"] ?? "");
$website = trim($_POST["website"] ?? "");
$consent = $_POST["consent"] ?? "";

if ($website !== "") {
    header("Location: contact-us.php?sent=1");
    exit;
}

$isValid =
    $name !== "" &&
    filter_var($email, FILTER_VALIDATE_EMAIL) !== false &&
    $mobile !== "" &&
    $message !== "" &&
    $consent === "1";

if (!$isValid) {
    header("Location: contact-us.php?sent=0");
    exit;
}

require_once __DIR__ . "/admin1234/config/database.php";

function getInquiryColumns(mysqli $connection): array
{
    try {
        $columns = [];
        $result = $connection->query("SHOW COLUMNS FROM inquiries");
        while ($row = $result->fetch_assoc()) {
            $columns[$row["Field"]] = true;
        }
        $result->free();
        return $columns;
    } catch (mysqli_sql_exception $e) {
        error_log("Inquiry column lookup failed: {$e->getMessage()}");
        return [];
    }
}

function ensureContactInquiriesTable(mysqli $connection): array
{
    $columns = getInquiryColumns($connection);
    if ($columns !== []) {
        return $columns;
    }

    $connection->query(
        "CREATE TABLE IF NOT EXISTS inquiries (
            id int(11) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            email varchar(150) NOT NULL,
            mobile varchar(50) NOT NULL,
            message text NOT NULL,
            created_at timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
    );

    return getInquiryColumns($connection);
}

function saveContactInquiry(
    mysqli $connection,
    array $columns,
    string $name,
    string $email,
    string $mobile,
    string $message
): void {
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
        throw new mysqli_sql_exception("The inquiries table has no compatible columns.");
    }

    $escapedFields = array_map(static fn ($field) => "`" . $field . "`", $fields);
    $placeholders = implode(", ", array_fill(0, count($fields), "?"));
    $sql = "INSERT INTO inquiries (" . implode(", ", $escapedFields) . ") VALUES ({$placeholders})";

    $stmt = $connection->prepare($sql);
    $types = str_repeat("s", count($values));
    $bindValues = [$types];
    foreach ($values as $index => $value) {
        $bindValues[] = &$values[$index];
    }
    call_user_func_array([$stmt, "bind_param"], $bindValues);
    $stmt->execute();
    $stmt->close();
}

$connection = getSashDBConnection();
if ($connection === null) {
    header("Location: contact-us.php?sent=0");
    exit;
}

try {
    $columns = ensureContactInquiriesTable($connection);
    saveContactInquiry($connection, $columns, $name, $email, $mobile, $message);
    $connection->close();

    header("Location: contact-us.php?sent=1");
    exit;
} catch (mysqli_sql_exception $e) {
    error_log("Contact form submission failed: {$e->getMessage()}");
    header("Location: contact-us.php?sent=0");
    exit;
}
