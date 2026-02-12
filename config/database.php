<?php
/**
 * Database Configuration for UC Nexus
 * Using Railway Cloud MySQL
 */

// Database credentials - Railway Cloud
define('DB_HOST', 'switchyard.proxy.rlwy.net');
define('DB_PORT', '51146');
define('DB_NAME', 'railway');
define('DB_USER', 'root');
define('DB_PASS', 'heLcUrNPJSeOIcQJIBfdOlqqzvGGrFqa');

/**
 * Get database connection using PDO
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // If database doesn't exist, return null (for setup check)
            if (strpos($e->getMessage(), 'Unknown database') !== false) {
                return null;
            }
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

/**
 * Check if database is set up
 */
function isDatabaseSetup() {
    try {
        $pdo = getDBConnection();
        if ($pdo === null) return false;
        
        // Check if tables exist
        $stmt = $pdo->query("SHOW TABLES LIKE 'rooms'");
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Execute a query and return results
 */
function dbQuery($sql, $params = []) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Fetch all results
 */
function dbFetchAll($sql, $params = []) {
    return dbQuery($sql, $params)->fetchAll();
}

/**
 * Fetch single result
 */
function dbFetchOne($sql, $params = []) {
    return dbQuery($sql, $params)->fetch();
}

/**
 * Insert and return last insert ID
 */
function dbInsert($sql, $params = []) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $pdo->lastInsertId();
}

/**
 * Update/Delete and return affected rows
 */
function dbExecute($sql, $params = []) {
    return dbQuery($sql, $params)->rowCount();
}
