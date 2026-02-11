<?php
session_start();

// Set timezone to Manila, Philippines
date_default_timezone_set('Asia/Manila');

// Handle CSV export before any HTML output
if (isset($_GET['export_csv']) && isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn']) {
    // Include data files to check database availability
    require_once __DIR__ . '/data/schedules.php';
    $useDatabase = isDatabaseSetup();
    $schedules = $useDatabase ? getAllSchedules() : ($_SESSION['schedules'] ?? []);
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="schedules_' . date('Y-m-d') . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    $output = fopen('php://output', 'w');
    // Add BOM for Excel UTF-8 compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($output, ['Class Code', 'Class Name', 'Room', 'Instructor', 'Date', 'Start Time', 'End Time', 'Department', 'Class Size', 'Days']);
    
    foreach ($schedules as $schedule) {
        $days = isset($schedule['days']) ? (is_array($schedule['days']) ? implode('/', $schedule['days']) : $schedule['days']) : '';
        fputcsv($output, [
            $schedule['classCode'] ?? '',
            $schedule['className'] ?? '',
            $schedule['room'] ?? '',
            $schedule['instructor'] ?? '',
            $schedule['date'] ?? '',
            $schedule['startTime'] ?? '',
            $schedule['endTime'] ?? '',
            $schedule['department'] ?? '',
            $schedule['classSize'] ?? '',
            $days
        ]);
    }
    fclose($output);
    exit();
}

// Handle CSV template download
if (isset($_GET['download_template']) && isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn']) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="schedule_template.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    $output = fopen('php://output', 'w');
    // Add BOM for Excel UTF-8 compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($output, ['Class Code', 'Class Name', 'Room', 'Instructor', 'Date', 'Start Time', 'End Time', 'Department', 'Class Size', 'Days']);
    // Add sample rows
    fputcsv($output, ['IT101', 'Introduction to Computing', 'M303 - Computer Laboratory', 'Dr. Maria Santos', '2026-02-15', '07:30', '08:50', 'CCS', '40', 'monday/wednesday/friday']);
    fputcsv($output, ['IT102', 'Web Development', 'M304 - Computer Laboratory', 'Prof. Juan Cruz', '2026-02-16', '08:50', '10:10', 'CCS', '35', 'tuesday/thursday']);
    fclose($output);
    exit();
}

// Handle AJAX room availability check
if (isset($_GET['check_room_availability']) && isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn']) {
    header('Content-Type: application/json');
    
    require_once __DIR__ . '/data/rooms.php';
    require_once __DIR__ . '/data/schedules.php';
    
    $date = $_GET['date'] ?? '';
    $startTime = $_GET['start_time'] ?? '';
    $endTime = $_GET['end_time'] ?? '';
    $excludeId = isset($_GET['exclude_id']) ? intval($_GET['exclude_id']) : null;
    
    $roomOptions = getRoomsAsOptions();
    $result = [];
    
    foreach ($roomOptions as $option) {
        $roomLabel = $option['label'];
        $available = true;
        $conflictInfo = null;
        
        // Only check availability if date and times are provided
        if (!empty($date) && !empty($startTime) && !empty($endTime)) {
            // Get all schedules and check for conflicts
            $allSchedules = getAllSchedules();
            
            foreach ($allSchedules as $schedule) {
                // Skip the schedule being edited
                if ($excludeId !== null && isset($schedule['id']) && $schedule['id'] == $excludeId) {
                    continue;
                }
                
                // Check if same room and same date
                if (($schedule['room'] ?? '') === $roomLabel && ($schedule['date'] ?? '') === $date) {
                    $existingStart = $schedule['startTime'] ?? '';
                    $existingEnd = $schedule['endTime'] ?? '';
                    
                    // Check for time overlap
                    if ($startTime < $existingEnd && $endTime > $existingStart) {
                        $available = false;
                        $conflictInfo = [
                            'className' => $schedule['className'] ?? 'Unknown',
                            'classCode' => $schedule['classCode'] ?? '',
                            'startTime' => date('g:i A', strtotime($existingStart)),
                            'endTime' => date('g:i A', strtotime($existingEnd)),
                            'instructor' => $schedule['instructor'] ?? ''
                        ];
                        break;
                    }
                }
            }
        }
        
        $result[] = [
            'value' => $option['value'],
            'label' => $roomLabel,
            'building' => $option['building'] ?? 'Unknown',
            'buildingFull' => $option['buildingFull'] ?? '',
            'floor' => $option['floor'] ?? 1,
            'available' => $available,
            'conflict' => $conflictInfo
        ];
    }
    
    echo json_encode(['rooms' => $result]);
    exit();
}

// Default credentials
define('DEFAULT_USERNAME', 'admin');
define('DEFAULT_PASSWORD', 'admin123');

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === DEFAULT_USERNAME && $password === DEFAULT_PASSWORD) {
        $_SESSION['isLoggedIn'] = true;
        $_SESSION['adminName'] = $username;
        $_SESSION['loginError'] = '';
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['loginError'] = 'Invalid username or password. Demo: admin / admin123';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}

$isLoggedIn = isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'];
$adminName = $_SESSION['adminName'] ?? 'Admin';
$loginError = $_SESSION['loginError'] ?? '';

// Clear login error after displaying
if (isset($_SESSION['loginError'])) {
    unset($_SESSION['loginError']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UC Nexus - Admin Portal</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/ai-helper.css">
    <link rel="stylesheet" href="css/schedule.css">
</head>
<body>
    <?php if (!$isLoggedIn): ?>
        <!-- Login Container -->
        <div id="loginContainer" class="login-wrapper">
            <div class="login-logo">
                <img src="assets/uclogo.png" alt="UC Logo" class="login-logo-img">
            </div>
            <div class="login-box">
                <div class="login-header">
                    <p>Admin Portal</p>
                </div>
                <form method="POST" action="index.php">
                    <input type="hidden" name="action" value="login">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required placeholder="Enter username">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Enter password">
                    </div>
                    <button type="submit" class="btn-login">Login</button>
                    <div class="login-hint">
                        <small>Demo: admin / admin123</small>
                    </div>
                </form>
                <?php if (!empty($loginError)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($loginError); ?></div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Navbar -->
        <nav class="navbar">
            <a href="?page=dashboard" class="navbar-brand">
                <img src="assets/uclogo.png" alt="UC Logo" class="navbar-logo">
                <div class="navbar-title">
                    <h1></h1>
                    <p>Admin Portal</p>
                </div>
            </a>
            <div class="navbar-user">
                <span>Welcome, <?php echo htmlspecialchars(ucfirst($adminName)); ?></span>
                <a href="?logout=true">Logout</a>
            </div>
        </nav>

        <!-- Dashboard Container -->
        <div id="dashboardContainer" class="dashboard-wrapper">
            <!-- Sidebar -->
            <aside class="sidebar">
                <div class="sidebar-header">
                    <h2>UC Nexus</h2>
                    <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
                </div>
                <nav class="sidebar-nav">
                    <a href="?page=dashboard" class="nav-item <?php echo (!isset($_GET['page']) || $_GET['page'] === 'dashboard') ? 'active' : ''; ?>">
                        📊 Dashboard
                    </a>
                    <a href="?page=schedule" class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] === 'schedule') ? 'active' : ''; ?>">
                        📅 Schedule Overview
                    </a>
                    <a href="?page=ai-helper" class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] === 'ai-helper') ? 'active' : ''; ?>">
                        🤖 AI Helper
                    </a>
                </nav>
                
            </aside>

            <!-- Main Content -->
            <main class="main-content">
                <!-- Header -->
                <header class="top-header">
                    <h1 id="pageTitle">
                        <?php
                            $page = $_GET['page'] ?? 'dashboard';
                            $titles = [
                                'dashboard' => 'Dashboard',
                                'ai-helper' => 'AI Assistant',
                                'schedule' => 'Schedule Overview'
                            ];
                            echo $titles[$page] ?? 'Dashboard';
                        ?>
                    </h1>
                    <div class="header-user">
                        <span id="adminName"><?php echo htmlspecialchars(ucfirst($adminName)); ?></span>
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='30' r='20' fill='%234CAF50'/%3E%3Cpath d='M 20 80 Q 50 60 80 80' fill='%234CAF50'/%3E%3C/svg%3E" alt="Admin Avatar" class="avatar">
                    </div>
                </header>

                <!-- Content Areas -->
                <div class="content">
                    <?php
                        $page = $_GET['page'] ?? 'dashboard';
                        
                        switch ($page) {
                            case 'ai-helper':
                                include 'pages/ai-helper.php';
                                break;
                            case 'schedule':
                                include 'pages/schedule.php';
                                break;
                            case 'dashboard':
                            default:
                                include 'pages/dashboard.php';
                                break;
                        }
                    ?>
                </div>
            </main>
        </div>
    <?php endif; ?>

    <script src="js/common.js"></script>
</body>
</html>
