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

function loadContactMailSettings(): array
{
    $defaults = [
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

    foreach (["admin", "admin1234"] as $adminDirectory) {
        $adminInitPath = __DIR__ . "/{$adminDirectory}/app/init.php";
        if (is_file($adminInitPath)) {
            require_once $adminInitPath;
            break;
        }
    }

    if (function_exists("load_site_settings")) {
        return array_merge(
            $defaults,
            array_intersect_key(load_site_settings(), $defaults),
        );
    }

    $settingsPath = "";
    foreach (["admin", "admin1234"] as $adminDirectory) {
        $candidatePath = __DIR__ . "/{$adminDirectory}/assets/uploads/site-settings.json";
        if (is_file($candidatePath)) {
            $settingsPath = $candidatePath;
            break;
        }
    }

    if (!is_file($settingsPath)) {
        return $defaults;
    }

    $decoded = json_decode((string) file_get_contents($settingsPath), true);
    if (!is_array($decoded)) {
        return $defaults;
    }

    return array_merge($defaults, array_intersect_key($decoded, $defaults));
}

function sanitizeMailHeader(string $value): string
{
    return trim(str_replace(["\r", "\n"], "", $value));
}

function encodeMailSubject(string $subject): string
{
    return "=?UTF-8?B?" . base64_encode($subject) . "?=";
}

function buildContactMailMessage(string $body): string
{
    return str_replace("\n", "\r\n", trim($body)) . "\r\n";
}

function smtpRead($socket): string
{
    $response = "";
    while (($line = fgets($socket, 515)) !== false) {
        $response .= $line;
        if (isset($line[3]) && $line[3] === " ") {
            break;
        }
    }

    return $response;
}

function smtpCommand($socket, string $command, array $expectedCodes): string
{
    if ($command !== "") {
        fwrite($socket, $command . "\r\n");
    }

    $response = smtpRead($socket);
    $code = (int) substr($response, 0, 3);
    if (!in_array($code, $expectedCodes, true)) {
        throw new RuntimeException("SMTP command failed: {$command} | {$response}");
    }

    return $response;
}

function sendViaSmtp(array $settings, string $to, string $subject, string $body, ?string $replyTo = null): bool
{
    $host = trim((string) ($settings["smtp_host"] ?? ""));
    $port = (int) ($settings["smtp_port"] ?? 587);
    $username = trim((string) ($settings["smtp_username"] ?? ""));
    $password = (string) ($settings["smtp_password"] ?? "");
    $encryption = strtolower(trim((string) ($settings["smtp_encryption"] ?? "tls")));
    $fromEmail = sanitizeMailHeader((string) ($settings["from_email"] ?? ""));
    $fromName = sanitizeMailHeader((string) ($settings["from_name"] ?? ""));

    if ($host === "" || $port <= 0 || $fromEmail === "") {
        return false;
    }

    $transportHost = $encryption === "ssl" ? "ssl://{$host}" : $host;
    $socket = @fsockopen($transportHost, $port, $errno, $errstr, 20);
    if (!$socket) {
        throw new RuntimeException("SMTP connection failed: {$errstr} ({$errno})");
    }

    try {
        stream_set_timeout($socket, 20);
        smtpCommand($socket, "", [220]);
        smtpCommand($socket, "EHLO " . ($_SERVER["SERVER_NAME"] ?? "localhost"), [250]);

        if ($encryption === "tls") {
            smtpCommand($socket, "STARTTLS", [220]);
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new RuntimeException("Unable to enable SMTP TLS encryption.");
            }
            smtpCommand($socket, "EHLO " . ($_SERVER["SERVER_NAME"] ?? "localhost"), [250]);
        }

        if ($username !== "") {
            smtpCommand($socket, "AUTH LOGIN", [334]);
            smtpCommand($socket, base64_encode($username), [334]);
            smtpCommand($socket, base64_encode($password), [235]);
        }

        smtpCommand($socket, "MAIL FROM:<{$fromEmail}>", [250]);
        smtpCommand($socket, "RCPT TO:<{$to}>", [250, 251]);
        smtpCommand($socket, "DATA", [354]);

        $headers = [
            "From: {$fromName} <{$fromEmail}>",
            "To: <{$to}>",
            "Subject: " . encodeMailSubject($subject),
            "MIME-Version: 1.0",
            "Content-Type: text/plain; charset=UTF-8",
            "Content-Transfer-Encoding: 8bit",
            "Date: " . date(DATE_RFC2822),
        ];
        if ($replyTo !== null && filter_var($replyTo, FILTER_VALIDATE_EMAIL) !== false) {
            $headers[] = "Reply-To: <" . sanitizeMailHeader($replyTo) . ">";
        }

        $data = implode("\r\n", $headers) . "\r\n\r\n" . buildContactMailMessage($body);
        $data = preg_replace("/^\./m", "..", $data);
        fwrite($socket, $data . "\r\n.\r\n");
        smtpCommand($socket, "", [250]);
        smtpCommand($socket, "QUIT", [221]);
    } finally {
        fclose($socket);
    }

    return true;
}

function sendContactMail(array $settings, string $to, string $subject, string $body, ?string $replyTo = null): bool
{
    $to = sanitizeMailHeader($to);
    if (filter_var($to, FILTER_VALIDATE_EMAIL) === false) {
        return false;
    }

    if (!empty($settings["smtp_enabled"])) {
        return sendViaSmtp($settings, $to, $subject, $body, $replyTo);
    }

    $fromEmail = sanitizeMailHeader((string) ($settings["from_email"] ?? ""));
    $fromName = sanitizeMailHeader((string) ($settings["from_name"] ?? ""));
    $headers = [
        "From: {$fromName} <{$fromEmail}>",
        "MIME-Version: 1.0",
        "Content-Type: text/plain; charset=UTF-8",
    ];
    if ($replyTo !== null && filter_var($replyTo, FILTER_VALIDATE_EMAIL) !== false) {
        $headers[] = "Reply-To: <" . sanitizeMailHeader($replyTo) . ">";
    }

    return mail($to, encodeMailSubject($subject), buildContactMailMessage($body), implode("\r\n", $headers));
}

function notifyContactSubmission(string $name, string $email, string $mobile, string $message): void
{
    $settings = loadContactMailSettings();
    $adminEmail = trim((string) ($settings["admin_email"] ?? ""));

    $adminBody = <<<MAIL
New contact form enquiry received.

Name: {$name}
Email: {$email}
Mobile: {$mobile}

Message:
{$message}
MAIL;

    $userBody = <<<MAIL
Dear {$name},

Thank you for contacting Balancing Carbon. We have received your enquiry and our team will get back to you shortly.

Your submitted message:
{$message}

Regards,
Balancing Carbon
MAIL;

    try {
        if (!sendContactMail($settings, $email, "We received your enquiry", $userBody)) {
            error_log("Contact user email was not sent.");
        }
    } catch (Throwable $e) {
        error_log("Contact user email send failure: " . $e->getMessage());
    }

    if ($adminEmail !== "") {
        try {
            if (!sendContactMail($settings, $adminEmail, "New contact enquiry from {$name}", $adminBody, $email)) {
                error_log("Contact admin email was not sent.");
            }
        } catch (Throwable $e) {
            error_log("Contact admin email send failure: " . $e->getMessage());
        }
    }
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

    if ($saved) {
        notifyContactSubmission($name, $email, $mobile, $message);
    }

    contactRedirect($saved ? "1" : "0");
} catch (Throwable $e) {
    error_log("Contact form failure: " . $e->getMessage());
    contactRedirect("0");
}
