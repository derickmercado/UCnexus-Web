<?php
/**
 * Schedules Data for UC Nexus
 * Database-connected version
 */

require_once __DIR__ . '/../config/database.php';

// =============== HELPER FUNCTIONS ===============

/**
 * Get all schedules from database
 */
function getAllSchedules() {
    if (!isDatabaseSetup()) {
        return [];
    }
    
    $schedules = dbFetchAll("SELECT * FROM schedules WHERE is_active = 1 ORDER BY COALESCE(schedule_date, '9999-12-31'), start_time");
    
    return array_map(function($s) {
        return formatScheduleFromDB($s);
    }, $schedules);
}

/**
 * Get schedule by ID
 */
function getScheduleById($id) {
    if (!isDatabaseSetup()) {
        return null;
    }
    
    $schedule = dbFetchOne("SELECT * FROM schedules WHERE id = ?", [$id]);
    
    if ($schedule) {
        return formatScheduleFromDB($schedule);
    }
    
    return null;
}

/**
 * Get schedules by room
 */
function getSchedulesByRoom($roomId) {
    if (!isDatabaseSetup()) {
        return [];
    }
    
    $schedules = dbFetchAll(
        "SELECT * FROM schedules WHERE room_id = ? AND is_active = 1 ORDER BY schedule_date, start_time",
        [$roomId]
    );
    
    return array_map(function($s) {
        return formatScheduleFromDB($s);
    }, $schedules);
}

/**
 * Get schedules by day
 */
function getSchedulesByDay($day) {
    if (!isDatabaseSetup()) {
        return [];
    }
    
    $day = strtolower($day);
    $schedules = dbFetchAll(
        "SELECT * FROM schedules WHERE is_active = 1 AND LOWER(days) LIKE ? ORDER BY start_time",
        ['%' . $day . '%']
    );
    
    return array_map(function($s) {
        return formatScheduleFromDB($s);
    }, $schedules);
}

/**
 * Get schedules by date
 */
function getSchedulesByDate($date) {
    if (!isDatabaseSetup()) {
        return [];
    }
    
    $schedules = dbFetchAll(
        "SELECT * FROM schedules WHERE schedule_date = ? AND is_active = 1 ORDER BY start_time",
        [$date]
    );
    
    return array_map(function($s) {
        return formatScheduleFromDB($s);
    }, $schedules);
}

/**
 * Get schedules by department
 */
function getSchedulesByDepartment($department) {
    if (!isDatabaseSetup()) {
        return [];
    }
    
    $schedules = dbFetchAll(
        "SELECT * FROM schedules WHERE department_id = ? AND is_active = 1 ORDER BY schedule_date, start_time",
        [$department]
    );
    
    return array_map(function($s) {
        return formatScheduleFromDB($s);
    }, $schedules);
}

/**
 * Get schedules by instructor
 */
function getSchedulesByInstructor($instructor) {
    if (!isDatabaseSetup()) {
        return [];
    }
    
    $schedules = dbFetchAll(
        "SELECT * FROM schedules WHERE instructor LIKE ? AND is_active = 1 ORDER BY schedule_date, start_time",
        ['%' . $instructor . '%']
    );
    
    return array_map(function($s) {
        return formatScheduleFromDB($s);
    }, $schedules);
}

/**
 * Check if a room is available at a specific time slot
 */
function isRoomAvailable($roomId, $day, $startTime, $endTime, $excludeScheduleId = null) {
    if (!isDatabaseSetup()) {
        return true;
    }
    
    $day = strtolower($day);
    
    $sql = "SELECT COUNT(*) as count FROM schedules 
            WHERE room_id = ? 
            AND is_active = 1 
            AND LOWER(days) LIKE ?
            AND (
                (start_time < ? AND end_time > ?) OR
                (start_time < ? AND end_time > ?) OR
                (start_time >= ? AND end_time <= ?)
            )";
    
    $params = [
        $roomId,
        '%' . $day . '%',
        $endTime, $startTime,
        $endTime, $startTime,
        $startTime, $endTime
    ];
    
    if ($excludeScheduleId) {
        $sql .= " AND id != ?";
        $params[] = $excludeScheduleId;
    }
    
    $result = dbFetchOne($sql, $params);
    
    return (int)$result['count'] === 0;
}

/**
 * Get total scheduled classes
 */
function getTotalScheduledClasses() {
    if (!isDatabaseSetup()) {
        return 0;
    }
    
    $result = dbFetchOne("SELECT COUNT(*) as count FROM schedules WHERE is_active = 1");
    return (int)$result['count'];
}

/**
 * Get schedules for today
 */
function getTodaySchedules() {
    if (!isDatabaseSetup()) {
        return [];
    }
    
    $today = date('Y-m-d');
    $dayName = strtolower(date('l'));
    
    // Include today's date OR recurring schedules (NULL date with matching day name)
    $schedules = dbFetchAll(
        "SELECT * FROM schedules 
         WHERE is_active = 1 
         AND (schedule_date = ? OR (schedule_date IS NULL AND LOWER(days) LIKE ?))
         ORDER BY start_time",
        [$today, '%' . $dayName . '%']
    );
    
    return array_map(function($s) {
        return formatScheduleFromDB($s);
    }, $schedules);
}

/**
 * Get upcoming schedules
 */
function getUpcomingSchedules() {
    if (!isDatabaseSetup()) {
        return [];
    }
    
    $today = date('Y-m-d');
    $currentTime = date('H:i:s');
    
    // Include schedules with NULL dates (recurring), future dates, or today with end time not passed
    $schedules = dbFetchAll(
        "SELECT * FROM schedules 
         WHERE is_active = 1 
         AND (schedule_date IS NULL OR schedule_date >= ? OR (schedule_date = ? AND end_time > ?))
         ORDER BY COALESCE(schedule_date, '9999-12-31'), start_time
         LIMIT 10",
        [$today, $today, $currentTime]
    );
    
    return array_map(function($s) {
        return formatScheduleFromDB($s);
    }, $schedules);
}

/**
 * Get departments list from database
 */
function getDepartments() {
    if (!isDatabaseSetup()) {
        return [];
    }
    
    $departments = dbFetchAll("SELECT id, name FROM departments ORDER BY name");
    $result = [];
    
    foreach ($departments as $dept) {
        $result[$dept['id']] = $dept['name'];
    }

    return $result;
}

/**
 * Get schedule statistics
 */
function getScheduleStatistics() {
    if (!isDatabaseSetup()) {
        return [
            'total' => 0,
            'byDepartment' => [],
            'byDay' => [
                'monday' => 0, 'tuesday' => 0, 'wednesday' => 0,
                'thursday' => 0, 'friday' => 0, 'saturday' => 0
            ],
            'totalStudents' => 0
        ];
    }
    
    $stats = [
        'total' => 0,
        'byDepartment' => [],
        'byDay' => [
            'monday' => 0, 'tuesday' => 0, 'wednesday' => 0,
            'thursday' => 0, 'friday' => 0, 'saturday' => 0
        ],
        'totalStudents' => 0
    ];
    
    // Get totals
    $totals = dbFetchOne("SELECT COUNT(*) as total, COALESCE(SUM(class_size), 0) as students FROM schedules WHERE is_active = 1");
    $stats['total'] = (int)$totals['total'];
    $stats['totalStudents'] = (int)$totals['students'];
    
    // Get by department
    $byDept = dbFetchAll("SELECT department_id, COUNT(*) as count FROM schedules WHERE is_active = 1 AND department_id IS NOT NULL GROUP BY department_id");
    foreach ($byDept as $row) {
        $stats['byDepartment'][$row['department_id']] = (int)$row['count'];
    }
    
    // Get by day (count schedules that have each day in their days field)
    $schedules = dbFetchAll("SELECT days FROM schedules WHERE is_active = 1");
    foreach ($schedules as $schedule) {
        $days = explode(',', strtolower($schedule['days'] ?? ''));
        foreach ($days as $day) {
            $day = trim($day);
            if (isset($stats['byDay'][$day])) {
                $stats['byDay'][$day]++;
            }
        }
    }
    
    return $stats;
}

/**
 * Format days array to string
 */
function formatDays($days) {
    if (is_string($days)) {
        $days = explode(',', $days);
    }
    
    $dayAbbrev = [
        'monday' => 'M', 'tuesday' => 'T', 'wednesday' => 'W',
        'thursday' => 'Th', 'friday' => 'F', 'saturday' => 'S'
    ];
    
    $formatted = [];
    foreach ($days as $day) {
        $day = strtolower(trim($day));
        if (isset($dayAbbrev[$day])) {
            $formatted[] = $dayAbbrev[$day];
        }
    }
    
    return implode('/', $formatted);
}

/**
 * Format time range
 */
function formatTimeRange($startTime, $endTime) {
    return date('g:i A', strtotime($startTime)) . ' - ' . date('g:i A', strtotime($endTime));
}

/**
 * Add a new schedule to database
 */
function addSchedule($data) {
    if (!isDatabaseSetup()) {
        return false;
    }
    
    $days = is_array($data['days'] ?? null) ? implode(',', $data['days']) : ($data['days'] ?? '');
    
    // Convert empty strings to null for foreign keys
    $departmentId = !empty($data['department']) ? $data['department'] : null;
    $roomId = !empty($data['roomId']) ? $data['roomId'] : null;
    $scheduleDate = !empty($data['date']) ? $data['date'] : null;
    $hasConflict = !empty($data['hasConflict']) ? 1 : 0;
    $conflictWith = !empty($data['conflictWith']) ? $data['conflictWith'] : null;
    
    $sql = "INSERT INTO schedules (class_code, class_name, room_id, room_display, instructor, department_id, class_size, schedule_date, start_time, end_time, days, semester, school_year, has_conflict, conflict_with) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $id = dbInsert($sql, [
        $data['classCode'] ?? '',
        $data['className'] ?? '',
        $roomId,
        $data['room'] ?? '',
        $data['instructor'] ?? '',
        $departmentId,
        $data['classSize'] ?? 0,
        $scheduleDate,
        $data['startTime'] ?? '00:00',
        $data['endTime'] ?? '00:00',
        $days,
        $data['semester'] ?? '',
        $data['schoolYear'] ?? '',
        $hasConflict,
        $conflictWith
    ]);
    
    return $id;
}

/**
 * Update schedule in database
 */
function updateSchedule($scheduleId, $data) {
    if (!isDatabaseSetup()) {
        return false;
    }
    
    $days = is_array($data['days'] ?? null) ? implode(',', $data['days']) : ($data['days'] ?? '');
    
    // Convert empty strings to null for foreign keys
    $departmentId = !empty($data['department']) ? $data['department'] : null;
    $roomId = !empty($data['roomId']) ? $data['roomId'] : null;
    $scheduleDate = !empty($data['date']) ? $data['date'] : null;
    
    $sql = "UPDATE schedules SET 
            class_code = ?, class_name = ?, room_id = ?, room_display = ?, instructor = ?, 
            department_id = ?, class_size = ?, schedule_date = ?, start_time = ?, end_time = ?, 
            days = ?, semester = ?, school_year = ?
            WHERE id = ?";
    
    dbExecute($sql, [
        $data['classCode'] ?? '',
        $data['className'] ?? '',
        $roomId,
        $data['room'] ?? '',
        $data['instructor'] ?? '',
        $departmentId,
        $data['classSize'] ?? 0,
        $scheduleDate,
        $data['startTime'] ?? '00:00',
        $data['endTime'] ?? '00:00',
        $days,
        $data['semester'] ?? '',
        $data['schoolYear'] ?? '',
        $scheduleId
    ]);
    
    return true;
}

/**
 * Delete schedule (hard delete from database)
 */
function deleteSchedule($scheduleId) {
    if (!isDatabaseSetup()) {
        return false;
    }
    
    dbExecute("DELETE FROM schedules WHERE id = ?", [$scheduleId]);
    return true;
}

/**
 * Soft delete schedule (set inactive)
 */
function softDeleteSchedule($scheduleId) {
    if (!isDatabaseSetup()) {
        return false;
    }
    
    dbExecute("UPDATE schedules SET is_active = 0 WHERE id = ?", [$scheduleId]);
    return true;
}

/**
 * Clear all schedules from database
 */
function clearAllSchedules() {
    if (!isDatabaseSetup()) {
        return false;
    }
    
    dbExecute("DELETE FROM schedules");
    return true;
}

/**
 * Helper function to format schedule from database row
 */
function formatScheduleFromDB($row) {
    return [
        'id' => (int)$row['id'],
        'classCode' => $row['class_code'],
        'className' => $row['class_name'],
        'roomId' => $row['room_id'],
        'room' => $row['room_display'],
        'instructor' => $row['instructor'],
        'department' => $row['department_id'],
        'classSize' => (int)$row['class_size'],
        'date' => $row['schedule_date'],
        'days' => $row['days'] ? explode(',', $row['days']) : [],
        'startTime' => substr($row['start_time'], 0, 5),
        'endTime' => substr($row['end_time'], 0, 5),
        'semester' => $row['semester'],
        'schoolYear' => $row['school_year'],
        'hasConflict' => !empty($row['has_conflict']),
        'conflictWith' => $row['conflict_with'] ?? null
    ];
}
