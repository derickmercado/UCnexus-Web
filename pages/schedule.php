<?php
// pages/schedule.php
// Initialize schedule data (would come from database in production)
if (!isset($_SESSION['schedules'])) {
    $_SESSION['schedules'] = [
        ['id' => 1, 'classCode' => 'OOP101', 'className' => 'Introduction to OOP', 'room' => 'Room 101', 'instructor' => 'Dr. Smith', 'date' => '2026-01-20', 'startTime' => '08:00', 'endTime' => '09:30'],
        ['id' => 2, 'classCode' => 'WEB102', 'className' => 'Mastery of Web', 'room' => 'Room 102', 'instructor' => 'Prof. Johnson', 'date' => '2026-01-20', 'startTime' => '10:00', 'endTime' => '11:30'],
        ['id' => 3, 'classCode' => 'WEB201', 'className' => 'Web Development', 'room' => 'Lab A', 'instructor' => 'Ms. Davis', 'date' => '2026-01-21', 'startTime' => '13:00', 'endTime' => '14:30']
    ];
}

// Handle adding schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_schedule') {
    $newSchedule = [
        'id' => count($_SESSION['schedules']) + 1,
        'classCode' => htmlspecialchars($_POST['classCode'] ?? ''),
        'className' => htmlspecialchars($_POST['className'] ?? ''),
        'room' => htmlspecialchars($_POST['scheduleRoom'] ?? ''),
        'instructor' => htmlspecialchars($_POST['instructor'] ?? ''),
        'date' => htmlspecialchars($_POST['scheduleDate'] ?? ''),
        'startTime' => htmlspecialchars($_POST['startTime'] ?? ''),
        'endTime' => htmlspecialchars($_POST['endTime'] ?? '')
    ];
    $_SESSION['schedules'][] = $newSchedule;
    $_SESSION['success'] = 'Schedule added successfully!';
    header('Location: ?page=schedule');
    exit();
}

// Handle deleting schedule
if (isset($_GET['delete_schedule'])) {+
    $id = intval($_GET['delete_schedule']);
    $_SESSION['schedules'] = array_filter($_SESSION['schedules'], function($schedule) use ($id) {
        return $schedule['id'] !== $id;
    });
    $_SESSION['success'] = 'Schedule deleted successfully!';
    header('Location: ?page=schedule');
    exit();
}

// Handle CSV import
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'import_csv') {
    if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] === UPLOAD_ERR_OK) {
        $file = fopen($_FILES['csvFile']['tmp_name'], 'r');
        $header = fgetcsv($file); // Skip header
        
        while (($data = fgetcsv($file)) !== false) {
            if (count($data) >= 6) {
                $newSchedule = [
                    'id' => count($_SESSION['schedules']) + 1,
                    'classCode' => htmlspecialchars(trim($data[0])),
                    'className' => htmlspecialchars(trim($data[1])),
                    'room' => htmlspecialchars(trim($data[2])),
                    'instructor' => htmlspecialchars(trim($data[3])),
                    'date' => htmlspecialchars(trim($data[4])),
                    'startTime' => htmlspecialchars(trim($data[5])),
                    'endTime' => htmlspecialchars(trim($data[6] ?? ''))
                ];
                $_SESSION['schedules'][] = $newSchedule;
            }
        }
        fclose($file);
        $_SESSION['success'] = 'Schedules imported successfully!';
    }
    header('Location: ?page=schedule');
    exit();
}

// Handle CSV export
if (isset($_GET['export_csv'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="schedules.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Class Code', 'Class Name', 'Room', 'Instructor', 'Date', 'Start Time', 'End Time']);
    
    foreach ($_SESSION['schedules'] as $schedule) {
        fputcsv($output, [$schedule['classCode'], $schedule['className'], $schedule['room'], $schedule['instructor'], $schedule['date'], $schedule['startTime'], $schedule['endTime']]);
    }
    fclose($output);
    exit();
}

$schedules = $_SESSION['schedules'] ?? [];
$success = $_SESSION['success'] ?? '';
if (isset($_SESSION['success'])) unset($_SESSION['success']);

// Sort schedules by date and time
usort($schedules, function($a, $b) {
    $timeA = strtotime($a['date'] . ' ' . $a['startTime']);
    $timeB = strtotime($b['date'] . ' ' . $b['startTime']);
    return $timeA - $timeB;
});
?>

<section id="schedule" class="tab-content active">
    <div class="card">
        <h2>Schedule Overview</h2>
        <p class="subtitle">View and manage class schedules</p>

        <?php if (!empty($success)): ?>
            <div class="success-message" style="background: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <div class="schedule-toolbar">
            <button class="btn-primary" onclick="toggleScheduleForm()">+ Add Schedule</button>
            <button class="btn-secondary" onclick="toggleCSVImport()">📥 Import CSV</button>
            <a href="?page=schedule&export_csv=true" class="btn-secondary">📤 Export CSV</a>
            <div class="filter-group">
                <label>View By:</label>
                <select onchange="filterSchedule(this.value)" class="filter-select">
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="all">All Schedules</option>
                </select>
            </div>
        </div>

        <!-- CSV Import Section -->
        <div id="csvImportSection" class="card form-card" style="display: none; margin-top: 20px;">
            <h3>Import Schedules from CSV</h3>
            <p class="subtitle">CSV format: Class Code, Class Name, Room, Instructor, Date, Start Time, End Time</p>
            <form method="POST" enctype="multipart/form-data" onsubmit="return confirm('Are you sure you want to import these schedules?')">
                <input type="hidden" name="action" value="import_csv">
                <div class="upload-area" id="uploadArea" ondrop="handleDrop(event)" ondragover="allowDrop(event)">
                    <p>📁 Drag and drop CSV file here or click to select</p>
                    <input type="file" id="csvFile" name="csvFile" accept=".csv" onchange="handleCSVUpload(event)" style="display: none;">
                </div>
                <div style="margin-top: 20px;">
                    <button type="submit" class="btn-primary">Import Data</button>
                    <button type="button" class="btn-secondary" onclick="toggleCSVImport()">Cancel</button>
                </div>
                <div id="csvError" class="error-message"></div>
            </form>
        </div>

        <!-- Add Schedule Form -->
        <div id="addScheduleForm" class="card form-card" style="display: none; margin-top: 20px;">
            <h3>Add New Schedule</h3>
            <form method="POST" action="?page=schedule">
                <input type="hidden" name="action" value="add_schedule">
                <div class="form-row">
                    <div class="form-group">
                        <label>Class Code</label>
                        <input type="text" name="classCode" required placeholder="e.g., CIT18, CC3">
                    </div>
                    <div class="form-group">
                        <label>Class Name</label>
                        <input type="text" name="className" required placeholder="e.g., Introduction to OOP, Mastery of Web">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Room</label>
                        <select name="scheduleRoom" required>
                            <option value="">Select a room</option>
                            <option value="Room 101">Room 101</option>
                            <option value="Room 102">Room 102</option>
                            <option value="Lab A">Lab A</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Instructor</label>
                        <input type="text" name="instructor" required placeholder="e.g., Dr. Smith">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="scheduleDate" required>
                    </div>
                    <div class="form-group">
                        <label>Start Time</label>
                        <input type="time" name="startTime" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>End Time</label>
                        <input type="time" name="endTime" required>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Add Schedule</button>
                    <button type="button" class="btn-secondary" onclick="toggleScheduleForm()">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Schedule Calendar View -->
        <div id="scheduleView" style="margin-top: 20px;">
            <h3 style="margin-bottom: 16px; color: #333; font-size: 18px;">Upcoming Classes</h3>
            <div class="schedule-list">
                <?php if (empty($schedules)): ?>
                    <p style="text-align: center; color: #999; grid-column: 1 / -1;">No schedules available</p>
                <?php else: ?>
                    <?php foreach ($schedules as $schedule): ?>
                        <article class="schedule-item">
                            <div class="schedule-header">
                                <div class="schedule-time">
                                    <p class="time-slot"><?php echo htmlspecialchars($schedule['startTime'] . ' - ' . $schedule['endTime']); ?></p>
                                </div>
                                <div class="schedule-actions">
                                    <button class="btn-small" onclick="editSchedule(<?php echo $schedule['id']; ?>)" aria-label="Edit schedule">Edit</button>
                                    <a href="?page=schedule&delete_schedule=<?php echo $schedule['id']; ?>" class="btn-small btn-danger" onclick="return confirm('Are you sure you want to delete this schedule?')" aria-label="Delete schedule">Delete</a>
                                </div>
                            </div>
                            <div class="schedule-details">
                                <h4 class="schedule-title"><?php echo htmlspecialchars($schedule['className']); ?></h4>
                                <div class="schedule-meta">
                                    <span><strong>Code:</strong> <?php echo htmlspecialchars($schedule['classCode'] ?? 'N/A'); ?></span>
                                    <span><strong>Room:</strong> <?php echo htmlspecialchars($schedule['room']); ?></span>
                                    <span><strong>Instructor:</strong> <?php echo htmlspecialchars($schedule['instructor']); ?></span>
                                    <span><strong>Date:</strong> <?php echo date('M j, Y', strtotime($schedule['date'])); ?></span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
function toggleScheduleForm() {
    const form = document.getElementById('addScheduleForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

function toggleCSVImport() {
    const section = document.getElementById('csvImportSection');
    section.style.display = section.style.display === 'none' ? 'block' : 'none';
}

function editSchedule(id) {
    alert('Edit functionality to be implemented with backend');
}

function filterSchedule(filter) {
    // This would require dynamic filtering with PHP or enhanced JavaScript
    alert('Filtering by: ' + filter);
}

function allowDrop(event) {
    event.preventDefault();
    event.target.closest('.upload-area').style.background = '#e8eef7';
}

function handleDrop(event) {
    event.preventDefault();
    event.target.closest('.upload-area').style.background = '';
    
    const files = event.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('csvFile').files = files;
    }
}

function handleCSVUpload(event) {
    const files = event.target.files;
    if (files.length > 0 && files[0].name.endsWith('.csv')) {
        document.getElementById('csvError').textContent = 'CSV file selected. Click Import to proceed.';
    }
}
</script>
