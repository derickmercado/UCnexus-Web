<?php
/**
 * Database Setup Script for UC Nexus
 * Run this script to create and initialize the database
 */

session_start();

// Check if user is logged in (for security)
$isLoggedIn = isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'];

// Database credentials - Railway Cloud
$host = 'switchyard.proxy.rlwy.net';
$port = '51146';
$user = 'root';
$pass = 'heLcUrNPJSeOIcQJIBfdOlqqzvGGrFqa';
$dbname = 'railway';

$message = '';
$error = '';
$success = false;

// Handle setup action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        // Connect to MySQL and select the database
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        if ($_POST['action'] === 'setup') {
            // Read and execute schema file
            $schemaFile = __DIR__ . '/schema.sql';
            
            if (!file_exists($schemaFile)) {
                throw new Exception("Schema file not found: $schemaFile");
            }
            
            $sql = file_get_contents($schemaFile);
            
            // Split SQL by semicolons (handling multi-line statements)
            $statements = array_filter(array_map('trim', preg_split('/;[\r\n]+/', $sql)));
            
            foreach ($statements as $statement) {
                if (!empty($statement) && !preg_match('/^--/', $statement)) {
                    $pdo->exec($statement);
                }
            }
            
            $success = true;
            $message = 'Database setup completed successfully! All tables and data have been created.';
        }
        
        if ($_POST['action'] === 'reset') {
            // Drop and recreate database
            $pdo->exec("DROP DATABASE IF EXISTS `$dbname`");
            
            // Read and execute schema file
            $schemaFile = __DIR__ . '/schema.sql';
            $sql = file_get_contents($schemaFile);
            $statements = array_filter(array_map('trim', preg_split('/;[\r\n]+/', $sql)));
            
            foreach ($statements as $statement) {
                if (!empty($statement) && !preg_match('/^--/', $statement)) {
                    $pdo->exec($statement);
                }
            }
            
            $success = true;
            $message = 'Database has been reset successfully! All data cleared and reinitialized.';
        }
        
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Check current database status
$dbStatus = 'not_created';
$tableCount = 0;
$roomCount = 0;
$scheduleCount = 0;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Count tables
    $stmt = $pdo->query("SHOW TABLES");
    $tableCount = $stmt->rowCount();
    
    if ($tableCount > 0) {
        $dbStatus = 'ready';
        
        // Count rooms
        $stmt = $pdo->query("SELECT COUNT(*) FROM rooms");
        $roomCount = $stmt->fetchColumn();
        
        // Count schedules
        $stmt = $pdo->query("SELECT COUNT(*) FROM schedules");
        $scheduleCount = $stmt->fetchColumn();
    } else {
        $dbStatus = 'empty';
    }
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Unknown database') !== false) {
        $dbStatus = 'not_created';
    } else {
        $error = 'Cannot connect to MySQL: ' . $e->getMessage();
        $dbStatus = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UC Nexus - Database Setup</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
        }
        .card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 {
            color: #1a1a2e;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 25px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .status-ready { background: #d4edda; color: #155724; }
        .status-not-created { background: #fff3cd; color: #856404; }
        .status-empty { background: #d1ecf1; color: #0c5460; }
        .status-error { background: #f8d7da; color: #721c24; }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        .stat-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-item h3 {
            font-size: 24px;
            color: #3498db;
        }
        .stat-item p {
            color: #666;
            font-size: 13px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            text-decoration: none;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .btn-primary {
            background: #3498db;
            color: white;
        }
        .btn-primary:hover {
            background: #2980b9;
        }
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        .btn-danger:hover {
            background: #c0392b;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .info-box {
            background: #e8f4fd;
            border-left: 4px solid #3498db;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        .info-box h4 {
            color: #2980b9;
            margin-bottom: 8px;
        }
        .info-box p, .info-box li {
            color: #555;
            font-size: 14px;
            line-height: 1.6;
        }
        .info-box ul {
            margin-left: 20px;
            margin-top: 10px;
        }
        
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
            color: #856404;
        }
        
        code {
            background: #f1f1f1;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Consolas', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>🗄️ UC Nexus Database Setup</h1>
            <p class="subtitle">Initialize and manage your MySQL database</p>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <span class="status-badge status-<?php echo $dbStatus; ?>">
                <?php
                switch ($dbStatus) {
                    case 'ready': echo '✓ Database Ready'; break;
                    case 'not_created': echo '⚠ Database Not Created'; break;
                    case 'empty': echo '○ Database Empty'; break;
                    case 'error': echo '✗ Connection Error'; break;
                }
                ?>
            </span>
            
            <?php if ($dbStatus === 'ready'): ?>
                <div class="stats">
                    <div class="stat-item">
                        <h3><?php echo $tableCount; ?></h3>
                        <p>Tables</p>
                    </div>
                    <div class="stat-item">
                        <h3><?php echo $roomCount; ?></h3>
                        <p>Rooms</p>
                    </div>
                    <div class="stat-item">
                        <h3><?php echo $scheduleCount; ?></h3>
                        <p>Schedules</p>
                    </div>
                </div>
            <?php endif; ?>
            
            <div style="margin-top: 25px;">
                <?php if ($dbStatus === 'not_created' || $dbStatus === 'empty'): ?>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="setup">
                        <button type="submit" class="btn btn-primary">🚀 Setup Database</button>
                    </form>
                <?php endif; ?>
                
                <?php if ($dbStatus === 'ready'): ?>
                    <a href="../index.php" class="btn btn-primary">← Back to Application</a>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('WARNING: This will DELETE all data and reset the database. Are you sure?');">
                        <input type="hidden" name="action" value="reset">
                        <button type="submit" class="btn btn-danger">🔄 Reset Database</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <h2 style="margin-bottom: 15px;">📋 Setup Instructions</h2>
            
            <div class="info-box">
                <h4>Prerequisites</h4>
                <ul>
                    <li>XAMPP with Apache and MySQL running</li>
                    <li>Database name: <code>ucnexus_db</code></li>
                    <li>Default credentials: <code>root</code> with no password</li>
                </ul>
            </div>
            
            <div class="info-box">
                <h4>What Gets Created</h4>
                <ul>
                    <li><strong>buildings</strong> - 6 campus buildings</li>
                    <li><strong>rooms</strong> - 196 rooms with types and capacities</li>
                    <li><strong>room_types</strong> - Room categories</li>
                    <li><strong>departments</strong> - 10 academic departments</li>
                    <li><strong>schedules</strong> - Class schedules</li>
                    <li><strong>time_slots</strong> - Standard time periods</li>
                </ul>
            </div>
            
            <?php if ($dbStatus === 'error'): ?>
                <div class="warning">
                    <strong>Troubleshooting:</strong>
                    <ul>
                        <li>Make sure XAMPP MySQL is running</li>
                        <li>Check if the credentials match your MySQL setup</li>
                        <li>Try accessing phpMyAdmin directly at <a href="http://localhost/phpmyadmin" target="_blank">localhost/phpmyadmin</a></li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2 style="margin-bottom: 15px;">🔧 Manual Setup (Optional)</h2>
            <p style="color: #666; margin-bottom: 15px;">If automatic setup fails, you can manually import the schema:</p>
            <ol style="margin-left: 20px; color: #555; line-height: 2;">
                <li>Open <a href="http://localhost/phpmyadmin" target="_blank">phpMyAdmin</a></li>
                <li>Create a new database named <code>ucnexus_db</code></li>
                <li>Select the database and go to "Import" tab</li>
                <li>Import the file: <code>config/schema.sql</code></li>
            </ol>
        </div>
    </div>
</body>
</html>
