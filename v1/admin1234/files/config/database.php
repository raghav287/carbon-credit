<?php
declare(strict_types=1);

// Allow configuration via environment variables for portability across hosts.
$host = getenv("DB_HOST") ?: "localhost";
$user = getenv("DB_USER") ?: "root";
$pass = getenv("DB_PASS") !== false ? getenv("DB_PASS") : "";
$db = getenv("DB_NAME") ?: "carbon";
$port = (int) (getenv("DB_PORT") ?: 3306);

/**
 * Opens a MySQLi connection and returns null if it cannot be established.
 * Errors are logged instead of triggering a fatal so the UI can show fallbacks.
 */
function getSashDBConnection(): ?\mysqli
{
    global $host, $user, $pass, $db, $port;

    // Fail gracefully on hosts that enable MYSQLI_REPORT_STRICT.
    \mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $candidates = [
        [$host, $user, $pass, $db, $port],
    ];

    if (getenv("DB_USER") === false && $user === "root") {
        $candidates[] = ["localhost", "u586615155_balanccarbon", "|gZ9@76!W6k:", "u586615155_balanccarbon", 3306];
    }

    foreach ($candidates as [$candidateHost, $candidateUser, $candidatePass, $candidateDb, $candidatePort]) {
        try {
            $conn = new \mysqli(
                $candidateHost,
                $candidateUser,
                $candidatePass,
                $candidateDb,
                (int) $candidatePort,
            );
            $conn->set_charset("utf8mb4");
            return $conn;
        } catch (\mysqli_sql_exception $e) {
            error_log("Database connection failed for {$candidateUser}@{$candidateHost}/{$candidateDb}: {$e->getMessage()}");
        }
    }

    return null;
}
