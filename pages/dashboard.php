<?php
// pages/dashboard.php
require_once __DIR__ . '/../data/buildings.php';
require_once __DIR__ . '/../data/rooms.php';
require_once __DIR__ . '/../data/schedules.php';

// Check if database is available
$useDatabase = isDatabaseSetup();

// Initialize session schedules if not using database
if (!$useDatabase && !isset($_SESSION['schedules'])) {
    $_SESSION['schedules'] = [];
}

// Get schedules from appropriate source
$allSchedules = $useDatabase ? getAllSchedules() : ($_SESSION['schedules'] ?? []);

// Get real statistics
$totalRooms = getTotalRooms();
$totalSchedules = count($allSchedules);
$roomStats = getRoomStatistics();
$buildings = getAllBuildings();

// Get today's schedules
$today = date('Y-m-d');
$currentTime = date('H:i');
if ($useDatabase) {
    $todaySchedules = getTodaySchedules();
} else {
    $todaySchedules = array_filter($allSchedules, function($schedule) use ($today) {
        return isset($schedule['date']) && $schedule['date'] === $today;
    });
}

// Filter out classes that have already ended
$todaySchedules = array_filter($todaySchedules, function($schedule) use ($currentTime) {
    $endTime = $schedule['endTime'] ?? '23:59';
    return $endTime > $currentTime;
});

// Sort by start time
usort($todaySchedules, function($a, $b) {
    return strcmp($a['startTime'] ?? '', $b['startTime'] ?? '');
});

// Calculate total students
if ($useDatabase) {
    $stats = getScheduleStatistics();
    $totalStudents = $stats['totalStudents'];
} else {
    $totalStudents = 0;
    foreach ($allSchedules as $schedule) {
        $totalStudents += isset($schedule['classSize']) ? $schedule['classSize'] : 0;
    }
}
?>
<section id="dashboard" class="tab-content active">
    <div class="dashboard-grid">
        <div class="stat-card">
            <h3>Total Rooms</h3>
            <p class="stat-number"><?php echo $totalRooms; ?></p>
            <small>Across <?php echo count($buildings); ?> buildings</small>
        </div>
        <div class="stat-card">
            <h3>Scheduled Classes</h3>
            <p class="stat-number"><?php echo $totalSchedules; ?></p>
            <small>Active schedules</small>
        </div>
        <div class="stat-card">
            <h3>Today's Classes</h3>
            <p class="stat-number"><?php echo count($todaySchedules); ?></p>
            <small>Remaining today</small>
        </div>
        <div class="stat-card">
            <h3>Total Capacity</h3>
            <p class="stat-number"><?php echo number_format($roomStats['totalCapacity']); ?></p>
            <small>Avg: <?php echo $roomStats['avgCapacity']; ?> per room</small>
        </div>
    </div>

    <!-- Buildings Overview -->
    <div class="card">
        <h2>Buildings Overview</h2>
        <p class="subtitle">Quick view of all campus buildings</p>
        <div class="buildings-grid">
            <?php foreach ($buildings as $building): ?>
            <div class="building-card" style="border-left: 4px solid <?php echo $building['color']; ?>">
                <h4><?php echo htmlspecialchars($building['fullName']); ?></h4>
                <p><strong><?php echo $building['totalRooms']; ?></strong> rooms | <strong><?php echo $building['floors']; ?></strong> floors</p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Today's Schedule -->
    <?php if (count($todaySchedules) > 0): ?>
    <div class="card">
        <h2>Today's Classes (<?php echo date('l, F j'); ?>)</h2>
        <p class="subtitle">Upcoming classes for today</p>
        <div class="schedule-list">
            <?php foreach ($todaySchedules as $schedule): 
                // Format times to 12-hour AM/PM format
                $startFormatted = date('g:i A', strtotime($schedule['startTime']));
                $endFormatted = date('g:i A', strtotime($schedule['endTime']));
            ?>
            <div class="schedule-item">
                <div class="schedule-time">
                    <strong><?php echo $startFormatted . ' - ' . $endFormatted; ?></strong>
                </div>
                <div class="schedule-details">
                    <strong><?php echo htmlspecialchars($schedule['classCode'] ?? 'N/A'); ?></strong> - 
                    <?php echo htmlspecialchars($schedule['className'] ?? 'N/A'); ?>
                    <br>
                    <small>
                        <?php echo htmlspecialchars($schedule['room'] ?? 'N/A'); ?> | 
                        <?php echo htmlspecialchars($schedule['instructor'] ?? 'N/A'); ?>
                    </small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Room Types Summary -->
    <div class="card">
        <h2>Room Types</h2>
        <p class="subtitle">Distribution of room types</p>
        <div class="room-types-grid">
            <?php 
            $roomTypes = getRoomTypes();
            foreach ($roomStats['byType'] as $type => $count): 
                $typeName = isset($roomTypes[$type]) ? $roomTypes[$type] : ucfirst(str_replace('-', ' ', $type));
            ?>
            <div class="room-type-item">
                <span class="type-name"><?php echo $typeName; ?></span>
                <span class="type-count"><?php echo $count; ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="action-buttons">
            <a href="?page=schedule" class="btn-action">View Schedule</a>
            <a href="?page=ai-helper" class="btn-action">Ask AI Helper</a>
        </div>
    </div>
</section>
