<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300);

// Local XAMPP Database
$host = 'localhost';
$port = '3306';
$dbname = 'ucnexus_db';
$user = 'root';
$pass = '';

echo "<h2>Importing Schema to Local Database</h2>";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "<p style='color:green'>✓ Connected to local database!</p>";
    
    // Read schema file
    $schemaFile = __DIR__ . '/schema.sql';
    $sql = file_get_contents($schemaFile);
    
    // Remove comments
    $sql = preg_replace('/--.*$/m', '', $sql);
    
    // Split by semicolon followed by newline (to handle multi-line statements)
    $statements = preg_split('/;\s*[\r\n]+/', $sql);
    
    $successCount = 0;
    $errorCount = 0;
    $errors = [];
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (empty($statement)) continue;
        
        try {
            $pdo->exec($statement);
            $successCount++;
        } catch (PDOException $e) {
            $errorCount++;
            // Only show unique errors
            $errorMsg = $e->getMessage();
            if (!in_array($errorMsg, $errors)) {
                $errors[] = $errorMsg;
            }
        }
    }
    
    echo "<p style='color:green'>✓ Executed $successCount statements successfully!</p>";
    
    if ($errorCount > 0) {
        echo "<p style='color:orange'>⚠ $errorCount statements had errors (may be duplicates or already exist):</p>";
        echo "<ul>";
        foreach (array_slice($errors, 0, 5) as $err) {
            echo "<li style='font-size:12px'>$err</li>";
        }
        if (count($errors) > 5) {
            echo "<li>...and " . (count($errors) - 5) . " more</li>";
        }
        echo "</ul>";
    }
    
    // Show all tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<h3>Tables in database (" . count($tables) . "):</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        $countStmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
        $count = $countStmt->fetchColumn();
        echo "<li><strong>$table</strong> - $count rows</li>";
    }
    echo "</ul>";
    
    echo "<p style='color:green; font-size:18px'><strong>✓ Import complete! You can now access your app from any device.</strong></p>";
    echo "<p><a href='../index.php'>Go to UC Nexus</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>✗ Error: " . $e->getMessage() . "</p>";
}
?>
