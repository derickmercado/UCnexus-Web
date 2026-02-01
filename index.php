<?php
session_start();

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
