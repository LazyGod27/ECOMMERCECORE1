<?php
// =========================================================================
// CustomerSupport/connection.php - Database Connection Configuration
// =========================================================================

// Database Connection Configuration
$host = getenv('DB_HOST') !== false ? getenv('DB_HOST') : '127.0.0.1';
$port = getenv('DB_PORT') !== false ? getenv('DB_PORT') : '3306';
$db_name = getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'core1_marketph';
$username = getenv('DB_USERNAME') !== false ? getenv('DB_USERNAME') : 'core1_marketph';
$password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '123';

// Global PDO connection variable
$pdo = null;

/**
 * Internal helper to build a PDO connection instance.
 */
if (!function_exists('create_pdo_connection')) {
    /**
     * Internal helper to build a PDO connection instance.
     */
    function create_pdo_connection($host, $port, $db_name, $username, $password)
    {
        return new PDO(
            "mysql:host=$host;port=$port;dbname=$db_name;charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
    }
}

if (!function_exists('get_db_connection')) {
    /**
     * Get database connection (Singleton pattern)
     * Returns a PDO connection object
     */
    function get_db_connection()
    {
        global $pdo, $host, $port, $db_name, $username, $password;

        if ($pdo instanceof PDO) {
            return $pdo;
        }

        $candidatePorts = array_unique(array_filter([$port, '3306']));
        $lastException = null;

        foreach ($candidatePorts as $candidatePort) {
            try {
                $pdo = create_pdo_connection($host, $candidatePort, $db_name, $username, $password);
                return $pdo;
            } catch (PDOException $e) {
                $lastException = $e;
            }
        }

        throw new RuntimeException("Database Connection Error: " . ($lastException ? $lastException->getMessage() : "Unknown error"));
    }
}
?>
