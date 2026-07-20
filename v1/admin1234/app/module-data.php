<?php
declare(strict_types=1);

require_once APP_ROOT . "/config/database.php";

/**
 * Lazily reuses the shared module connection so multiple helpers can run without extra opens.
 */
function getModuleConnection(): ?\mysqli
{
    static $connection = null;
    static $tried = false;

    if ($connection !== null) {
        return $connection;
    }

    if ($tried) {
        return null;
    }
    $tried = true;

    try {
        $connection = getSashDBConnection();
    } catch (\mysqli_sql_exception $e) {
        error_log("Module DB connection failed: {$e->getMessage()}");
        $connection = null;
        return null;
    }

    register_shutdown_function(function () use (&$connection) {
        if ($connection !== null) {
            $connection->close();
            $connection = null;
        }
    });

    return $connection;
}

function tryFetchRows(?\mysqli $connection, string $sql): array
{
    $rows = [];
    if ($connection === null) {
        return $rows;
    }

    try {
        if ($result = $connection->query($sql)) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            $result->free();
        }
    } catch (\mysqli_sql_exception $e) {
        error_log("Query failed: {$e->getMessage()}");
        return [];
    }

    return $rows;
}

function getTableColumns(?\mysqli $connection, string $table): array
{
    if ($connection === null) {
        return [];
    }

    try {
        $safeTable = str_replace("`", "``", $table);
        $result = $connection->query("SHOW COLUMNS FROM `{$safeTable}`");
        $columns = [];
        while ($row = $result->fetch_assoc()) {
            $columns[$row["Field"]] = true;
        }
        $result->free();
        return $columns;
    } catch (\mysqli_sql_exception $e) {
        error_log("Column lookup failed for {$table}: {$e->getMessage()}");
        return [];
    }
}

function getListingItems(): array
{
    $rows = tryFetchRows(
        getModuleConnection(),
        "SELECT id, name, position, start_date, salary FROM listing_items ORDER BY id",
    );
    return $rows !== [] ? $rows : getListingItemsFallback();
}

function getListingItemsFallback(): array
{
    return [
        [
            "id" => 1,
            "name" => "Bella Chloe",
            "position" => "System Developer",
            "start_date" => "2018-03-12",
            "salary" => '$654,765',
        ],
        [
            "id" => 2,
            "name" => "Donna Bond",
            "position" => "Account Manager",
            "start_date" => "2012-02-21",
            "salary" => '$543,654',
        ],
        [
            "id" => 3,
            "name" => "Kyle Newton",
            "position" => "Lead Designer",
            "start_date" => "2015-07-08",
            "salary" => '$498,121',
        ],
    ];
}

function getListingItemById(int $id): ?array
{
    $connection = getModuleConnection();
    if ($connection === null) {
        return null;
    }

    try {
        $stmt = $connection->prepare(
            "SELECT id, name, position, start_date, salary FROM listing_items WHERE id = ? LIMIT 1",
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    } catch (\mysqli_sql_exception $e) {
        error_log("Listing fetch failed: {$e->getMessage()}");
        return null;
    }
}

function saveListingItem(array $data): ?int
{
    $connection = getModuleConnection();
    if ($connection === null) {
        return null;
    }

    $id = !empty($data["id"]) ? (int) $data["id"] : null;
    $name = $data["name"] ?? "";
    $position = $data["position"] ?? "";
    $startDate = $data["start_date"] ?? "";
    $salary = $data["salary"] ?? "";

    try {
        if ($id) {
            $stmt = $connection->prepare(
                "UPDATE listing_items SET name = ?, position = ?, start_date = ?, salary = ? WHERE id = ?",
            );
            $stmt->bind_param(
                "ssssi",
                $name,
                $position,
                $startDate,
                $salary,
                $id,
            );
            $stmt->execute();
            $stmt->close();
            return $id;
        }

        $stmt = $connection->prepare(
            "INSERT INTO listing_items (name, position, start_date, salary) VALUES (?, ?, ?, ?)",
        );
        $stmt->bind_param("ssss", $name, $position, $startDate, $salary);
        $stmt->execute();
        $newId = $stmt->insert_id;
        $stmt->close();
        return $newId ?: null;
    } catch (\mysqli_sql_exception $e) {
        error_log("Saving listing failed: {$e->getMessage()}");
        return null;
    }
}

function deleteListingItem(int $id): bool
{
    $connection = getModuleConnection();
    if ($connection === null) {
        return false;
    }

    try {
        $stmt = $connection->prepare("DELETE FROM listing_items WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected > 0;
    } catch (\mysqli_sql_exception $e) {
        error_log("Listing delete failed: {$e->getMessage()}");
        return false;
    }
}

function getProfileRow(): ?array
{
    $connection = getModuleConnection();
    if ($connection === null) {
        return null;
    }

    try {
        $query =
            "SELECT id, full_name, job_title, location, country, languages, email, phone, about, experience, company FROM profiles ORDER BY id DESC LIMIT 1";
        if ($result = $connection->query($query)) {
            $row = $result->fetch_assoc();
            $result->free();
            return $row ?: null;
        }
    } catch (\mysqli_sql_exception $e) {
        error_log("Profile fetch failed: {$e->getMessage()}");
    }

    return null;
}

function getProfileDetails(): array
{
    $row = getProfileRow();
    if ($row !== null) {
        unset($row["id"]);
        return $row;
    }
    return getProfileFallback();
}

function getProfileFallback(): array
{
    return [
        "full_name" => "Percy Kewshun",
        "job_title" => "Web Developer",
        "location" => "San Francisco, CA",
        "country" => "USA",
        "languages" => "English, German, Spanish",
        "email" => "georgeme@abc.com",
        "phone" => "+125 254 3562",
        "about" =>
            "Very well thought out and articulate description of my professional background and expertise. I have over 10 years of experience in web development, specializing in front-end technologies and modern frameworks.",
        "experience" =>
            "My passion lies in creating intuitive and visually stunning user interfaces that provide exceptional user experiences.",
        "company" => "BetaSoft LLC",
    ];
}

function saveProfileDetails(array $data): bool
{
    $connection = getModuleConnection();
    if ($connection === null) {
        return false;
    }

    $fullName = trim($data["full_name"] ?? "");
    $jobTitle = trim($data["job_title"] ?? "");
    $location = trim($data["location"] ?? "");
    $country = trim($data["country"] ?? "");
    $languages = trim($data["languages"] ?? "");
    $email = trim($data["email"] ?? "");
    $phone = trim($data["phone"] ?? "");
    $about = trim($data["about"] ?? "");
    $experience = trim($data["experience"] ?? "");
    $company = trim($data["company"] ?? "");

    // Require minimum fields to avoid empty rows being created.
    if ($fullName === "" || $jobTitle === "") {
        return false;
    }

    $row = getProfileRow();

    try {
        if ($row !== null && isset($row["id"])) {
            $stmt = $connection->prepare(
                "UPDATE profiles SET full_name = ?, job_title = ?, location = ?, country = ?, languages = ?, email = ?, phone = ?, about = ?, experience = ?, company = ? WHERE id = ?",
            );
            if ($stmt === false) {
                error_log(
                    "Profile update prepare failed: {$connection->error}",
                );
                return false;
            }
            $stmt->bind_param(
                "ssssssssssi",
                $fullName,
                $jobTitle,
                $location,
                $country,
                $languages,
                $email,
                $phone,
                $about,
                $experience,
                $company,
                $row["id"],
            );
        } else {
            $stmt = $connection->prepare(
                "INSERT INTO profiles (full_name, job_title, location, country, languages, email, phone, about, experience, company) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            );
            if ($stmt === false) {
                error_log(
                    "Profile insert prepare failed: {$connection->error}",
                );
                return false;
            }
            $stmt->bind_param(
                "ssssssssss",
                $fullName,
                $jobTitle,
                $location,
                $country,
                $languages,
                $email,
                $phone,
                $about,
                $experience,
                $company,
            );
        }

        $stmt->execute();
        $stmt->close();
        return true;
    } catch (\mysqli_sql_exception $e) {
        error_log("Profile save failed: {$e->getMessage()}");
        return false;
    }
}

function getDashboardMetrics(): array
{
    $rows = tryFetchRows(
        getModuleConnection(),
        "SELECT metric_label, metric_value, trend_direction, trend_value, trend_period FROM dashboard_metrics ORDER BY id",
    );
    return $rows !== [] ? $rows : getDashboardMetricsFallback();
}

function getDashboardMetricsFallback(): array
{
    return [
        [
            "metric_label" => "Total Users",
            "metric_value" => "44,278",
            "trend_direction" => "up",
            "trend_value" => "5%",
            "trend_period" => "Last week",
        ],
        [
            "metric_label" => "Total Profit",
            "metric_value" => '$67,987',
            "trend_direction" => "down",
            "trend_value" => "0.75%",
            "trend_period" => "Last 6 days",
        ],
        [
            "metric_label" => "Total Expenses",
            "metric_value" => '$76,965',
            "trend_direction" => "up",
            "trend_value" => "0.9%",
            "trend_period" => "Last 9 days",
        ],
        [
            "metric_label" => "Total Cost",
            "metric_value" => '$59,765',
            "trend_direction" => "up",
            "trend_value" => "0.6%",
            "trend_period" => "Last year",
        ],
    ];
}

function getInquiries(): array
{
    $connection = getModuleConnection();
    $columns = getTableColumns($connection, "inquiries");
    if ($columns === []) {
        return [];
    }

    $emailSelect = isset($columns["email"]) ? "`email`" : "'' AS `email`";
    $mobileSelect = isset($columns["mobile"])
        ? "`mobile`"
        : (isset($columns["phone"]) ? "`phone` AS `mobile`" : "'' AS `mobile`");
    $messageSelect = isset($columns["message"]) ? "`message`" : "'' AS `message`";
    $createdSelect = isset($columns["created_at"]) ? "`created_at`" : "NULL AS `created_at`";
    $orderBy = isset($columns["created_at"]) ? "`created_at` DESC" : "`id` DESC";

    $rows = tryFetchRows(
        $connection,
        "SELECT `id`, `name`, {$emailSelect}, {$mobileSelect}, {$messageSelect}, {$createdSelect} FROM `inquiries` ORDER BY {$orderBy}",
    );
    return $rows !== [] ? $rows : [];
}

function deleteInquiry(int $id): bool
{
    $connection = getModuleConnection();
    if ($connection === null) {
        return false;
    }

    try {
        $stmt = $connection->prepare("DELETE FROM `inquiries` WHERE `id` = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected > 0;
    } catch (\mysqli_sql_exception $e) {
        error_log("Inquiry delete failed: {$e->getMessage()}");
    }

    try {
        $safeId = (int) $id;
        $connection->query("DELETE FROM `inquiries` WHERE `id` = {$safeId} LIMIT 1");
        return $connection->affected_rows > 0;
    } catch (\mysqli_sql_exception $e) {
        error_log("Inquiry delete fallback failed: {$e->getMessage()}");
        return false;
    }
}

function deleteInquiryByDetails(
    string $name,
    string $email,
    string $mobile,
    string $message,
    string $createdAt
): bool {
    $connection = getModuleConnection();
    if ($connection === null || $name === "" || $email === "" || $createdAt === "") {
        return false;
    }

    try {
        $stmt = $connection->prepare(
            "DELETE FROM `inquiries`
             WHERE `name` = ?
               AND `email` = ?
               AND `mobile` = ?
               AND `message` = ?
               AND `created_at` = ?
             LIMIT 1"
        );
        $stmt->bind_param("sssss", $name, $email, $mobile, $message, $createdAt);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected > 0;
    } catch (\mysqli_sql_exception $e) {
        error_log("Inquiry detail delete failed: {$e->getMessage()}");
        return false;
    }
}
