<?php
// pages/dashboard.php
require_once __DIR__ . '/../data/buildings.php';
require_once __DIR__ . '/../data/rooms.php';
require_once __DIR__ . '/../data/schedules.php';

// Get real statistics
$totalRooms = getTotalRooms();
$totalSchedules = getTotalScheduledClasses();
$roomStats = getRoomStatistics();
$scheduleStats = getScheduleStatistics();
$buildings = getAllBuildings();
$todaySchedules = getTodaySchedules();
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
            <small><?php echo date('l'); ?></small>
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
        <p class="subtitle">Classes scheduled for today</p>
        <div class="schedule-list">
            <?php foreach ($todaySchedules as $schedule): ?>
            <div class="schedule-item">
                <div class="schedule-time">
                    <strong><?php echo formatTimeRange($schedule['startTime'], $schedule['endTime']); ?></strong>
                </div>
                <div class="schedule-details">
                    <strong><?php echo htmlspecialchars($schedule['classCode']); ?></strong> - 
                    <?php echo htmlspecialchars($schedule['className']); ?>
                    <br>
                    <small>
                        <?php echo htmlspecialchars($schedule['room']); ?> | 
                        <?php echo htmlspecialchars($schedule['instructor']); ?> | 
                        <?php echo $schedule['classSize']; ?> students
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
