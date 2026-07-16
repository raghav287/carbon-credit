<?php
declare(strict_types=1);

require_once APP_ROOT . "/config/database.php";

function ensureAuthSession(): void
{
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }

    ini_set("session.cookie_httponly", "1");
    ini_set("session.use_only_cookies", "1");
    ini_set("session.cookie_samesite", "Lax");
    session_start();
}

function isAdminLoggedIn(): bool
{
    ensureAuthSession();
    return !empty($_SESSION["admin_id"]) && !empty($_SESSION["admin_username"]);
}

function requireAdminLogin(): void
{
    if (!isAdminLoggedIn()) {
        header("Location: " . file_url("login/login"));
        exit();
    }
}

function attemptAdminLogin(string $identifier, string $password): bool
{
    error_log("attemptAdminLogin called with identifier: " . $identifier);
    $connection = getSashDBConnection();
    if ($connection === null) {
        error_log("attemptAdminLogin: DB connection failed");
        return false;
    }

    try {
        error_log("attemptAdminLogin: Preparing statement");
        // First check what columns exist in admin_users
        $columnsResult = $connection->query("SHOW COLUMNS FROM admin_users");
        $columns = [];
        while ($col = $columnsResult->fetch_assoc()) {
            $columns[] = $col['Field'];
        }
        
        // Build query based on available columns
        $selectFields = ["id", "username", "email", "password"];
        if (in_array("profile_picture", $columns)) {
            $selectFields[] = "profile_picture";
        }
        $sql = "SELECT " . implode(", ", $selectFields) . " FROM admin_users WHERE email = ? OR username = ? LIMIT 1";
        
        $stmt = $connection->prepare($sql);
        if ($stmt === false) {
            error_log("Auth prepare failed: {$connection->error}");
            return false;
        }

        $stmt->bind_param("ss", $identifier, $identifier);
        error_log("attemptAdminLogin: Executing statement");
        $stmt->execute();
        
        // Bind results dynamically
        if (in_array("profile_picture", $columns)) {
            $stmt->bind_result(
                $id,
                $dbUsername,
                $dbEmail,
                $hashedPassword,
                $profilePicture,
            );
        } else {
            $stmt->bind_result(
                $id,
                $dbUsername,
                $dbEmail,
                $hashedPassword,
            );
            $profilePicture = "";
        }
        $found = $stmt->fetch();
        error_log("attemptAdminLogin: Found user? " . ($found ? 'YES' : 'NO'));
        $stmt->close();

        if (!$found || $hashedPassword === null) {
            error_log("attemptAdminLogin: No user found or no password");
            return false;
        }

        error_log("attemptAdminLogin: Verifying password for user " . $dbUsername);
        $verifyResult = password_verify($password, $hashedPassword);
        error_log("attemptAdminLogin: password_verify result: " . ($verifyResult ? 'SUCCESS' : 'FAILED'));
        if (!$verifyResult) {
            return false;
        }

        ensureAuthSession();
        session_regenerate_id(true);
        $_SESSION["admin_id"] = $id;
        $_SESSION["admin_username"] = $dbUsername;
        $_SESSION["admin_email"] = $dbEmail;
        $_SESSION["admin_profile_picture"] = $profilePicture ?? "";
        error_log("attemptAdminLogin: Login SUCCESS!");

        return true;
    } catch (\mysqli_sql_exception $e) {
        error_log("Auth lookup failed: {$e->getMessage()}");
        return false;
    } finally {
        $connection->close();
    }
}

function updateAdminPassword(
    int $adminId,
    string $currentPassword,
    string $newPassword,
): bool {
    if ($adminId <= 0) {
        return false;
    }

    $connection = getSashDBConnection();
    if ($connection === null) {
        return false;
    }

    $stmt = null;
    $updateStmt = null;

    try {
        $stmt = $connection->prepare(
            "SELECT password FROM admin_users WHERE id = ? LIMIT 1",
        );
        if ($stmt === false) {
            error_log("Password lookup failed: {$connection->error}");
            return false;
        }
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        $stmt->bind_result($hashedPassword);
        $found = $stmt->fetch();
        $stmt->close();
        $stmt = null;

        if (!$found || $hashedPassword === null) {
            return false;
        }

        if (!password_verify($currentPassword, $hashedPassword)) {
            return false;
        }

        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $connection->prepare(
            "UPDATE admin_users SET password = ? WHERE id = ?",
        );
        if ($updateStmt === false) {
            error_log("Password update prepare failed: {$connection->error}");
            return false;
        }
        $updateStmt->bind_param("si", $newHash, $adminId);
        $updateStmt->execute();
        $success = $updateStmt->affected_rows > 0;
        return $success;
    } catch (\mysqli_sql_exception $e) {
        error_log("Password update failed: {$e->getMessage()}");
        return false;
    } finally {
        if ($stmt !== null) {
            $stmt->close();
        }
        if ($updateStmt !== null) {
            $updateStmt->close();
        }
        $connection->close();
    }
}

function getAdminUser(int $adminId): ?array
{
    if ($adminId <= 0) {
        return null;
    }

    $connection = getSashDBConnection();
    if ($connection === null) {
        return null;
    }

    $stmt = null;
    try {
        // Check columns first
        $columnsResult = $connection->query("SHOW COLUMNS FROM admin_users");
        $columns = [];
        while ($col = $columnsResult->fetch_assoc()) {
            $columns[] = $col['Field'];
        }
        
        $selectFields = ["id", "username", "email"];
        if (in_array("profile_picture", $columns)) {
            $selectFields[] = "profile_picture";
        }
        $sql = "SELECT " . implode(", ", $selectFields) . " FROM admin_users WHERE id = ? LIMIT 1";
        
        $stmt = $connection->prepare($sql);
        if ($stmt === false) {
            error_log("Admin lookup failed: {$connection->error}");
            return null;
        }
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        
        if (in_array("profile_picture", $columns)) {
            $stmt->bind_result($id, $username, $email, $profilePicture);
        } else {
            $stmt->bind_result($id, $username, $email);
            $profilePicture = "";
        }
        $found = $stmt->fetch();
        $stmt->close();
        $stmt = null;

        if (!$found) {
            return null;
        }

        return [
            "id" => $id,
            "username" => $username,
            "email" => $email,
            "profile_picture" => $profilePicture,
        ];
    } catch (\mysqli_sql_exception $e) {
        error_log("Admin lookup failed: {$e->getMessage()}");
        return null;
    } finally {
        if ($stmt !== null) {
            $stmt->close();
        }
        $connection->close();
    }
}

function updateAdminAccount(
    int $adminId,
    string $username,
    string $email,
    ?string $profilePicture = null,
): bool {
    if ($adminId <= 0 || $username === "" || $email === "") {
        return false;
    }

    $connection = getSashDBConnection();
    if ($connection === null) {
        return false;
    }

    $stmt = null;
    // Check columns first
    $columnsResult = $connection->query("SHOW COLUMNS FROM admin_users");
    $columns = [];
    while ($col = $columnsResult->fetch_assoc()) {
        $columns[] = $col['Field'];
    }
    
    $hasProfilePicture = in_array("profile_picture", $columns);
    
    if (!$hasProfilePicture || $profilePicture === null) {
        $sql = "UPDATE admin_users SET username = ?, email = ? WHERE id = ?";
    } else {
        $sql = "UPDATE admin_users SET username = ?, email = ?, profile_picture = ? WHERE id = ?";
    }

    try {
        $stmt = $connection->prepare($sql);
        if ($stmt === false) {
            error_log("Admin update failed: {$connection->error}");
            return false;
        }

        if (!$hasProfilePicture || $profilePicture === null) {
            $stmt->bind_param("ssi", $username, $email, $adminId);
        } else {
            $stmt->bind_param(
                "sssi",
                $username,
                $email,
                $profilePicture,
                $adminId,
            );
        }

        $stmt->execute();
        $success = $stmt->affected_rows >= 0;
        return $success;
    } catch (\mysqli_sql_exception $e) {
        error_log("Admin update failed: {$e->getMessage()}");
        return false;
    } finally {
        if ($stmt !== null) {
            $stmt->close();
        }
        $connection->close();
    }
}

function logoutAdmin(): void
{
    ensureAuthSession();
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            "",
            time() - 42000,
            $params["path"] ?? "/",
            $params["domain"] ?? "",
            $params["secure"] ?? false,
            $params["httponly"] ?? true,
        );
    }

    session_destroy();
    header("Location: " . file_url("login/login"));
    exit();
}
