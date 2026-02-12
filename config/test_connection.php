<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'switchyard.proxy.rlwy.net';
$port = '51146';
$dbname = 'railway';
$user = 'root';
$pass = 'heLcUrNPJSeOIcQJIBfdOlqqzvGGrFqa';

echo "<h2>Testing Railway Connection</h2>";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "<p style='color:green'>✓ Connected successfully!</p>";
    
    // Try creating a simple table
    $pdo->exec("CREATE TABLE IF NOT EXISTS test_connection (id INT PRIMARY KEY, name VARCHAR(50))");
    echo "<p style='color:green'>✓ Test table created!</p>";
    
    // Show all tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>Tables in database: " . (count($tables) > 0 ? implode(', ', $tables) : 'none') . "</p>";
    
    // Clean up test table
    $pdo->exec("DROP TABLE IF EXISTS test_connection");
    echo "<p style='color:green'>✓ Test table cleaned up!</p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>✗ Error: " . $e->getMessage() . "</p>";
}
?>
