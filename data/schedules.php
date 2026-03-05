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
    
    // Normalize days to comma-separated format (form uses '/', database uses ',')
    $rawDays = $data['days'] ?? '';
    if (is_array($rawDays)) {
        $days = implode(',', $rawDays);
    } else {
        // Convert '/' to ',' for consistency
        $days = str_replace('/', ',', $rawDays);
    }
    
    // Convert empty strings to null for foreign keys
    $departmentId = !empty($data['department']) ? $data['department'] : null;
    $roomId = !empty($data['roomId']) ? $data['roomId'] : null;
    $scheduleDate = !empty($data['date']) ? $data['date'] : null;
    $hasConflict = !empty($data['hasConflict']) ? 1 : 0;
    $conflictWith = !empty($data['conflictWith']) ? $data['conflictWith'] : null;
    
    // CIT/CC dual schedule fields
    $isCITCC = !empty($data['isCITCC']) ? 1 : 0;
    $lecRoom = !empty($data['lecRoom']) ? $data['lecRoom'] : null;
    $lecInstructor = !empty($data['lecInstructor']) ? $data['lecInstructor'] : null;
    $lecDays = !empty($data['lecDays']) ? str_replace('/', ',', $data['lecDays']) : null;
    $lecStartTime = !empty($data['lecStartTime']) ? $data['lecStartTime'] : null;
    $lecEndTime = !empty($data['lecEndTime']) ? $data['lecEndTime'] : null;
    $labRoom = !empty($data['labRoom']) ? $data['labRoom'] : null;
    $labInstructor = !empty($data['labInstructor']) ? $data['labInstructor'] : null;
    $labDays = !empty($data['labDays']) ? str_replace('/', ',', $data['labDays']) : null;
    $labStartTime = !empty($data['labStartTime']) ? $data['labStartTime'] : null;
    $labEndTime = !empty($data['labEndTime']) ? $data['labEndTime'] : null;
    
    // Year Level, Term, Block
    $yearLevel = !empty($data['yearLevel']) ? $data['yearLevel'] : null;
    $term = !empty($data['term']) ? $data['term'] : null;
    $block = !empty($data['block']) ? $data['block'] : null;
    
    $sql = "INSERT INTO schedules (class_code, class_name, room_id, room_display, instructor, department_id, class_size, schedule_date, start_time, end_time, days, semester, school_year, has_conflict, conflict_with, is_citcc, lec_room, lec_instructor, lec_days, lec_start_time, lec_end_time, lab_room, lab_instructor, lab_days, lab_start_time, lab_end_time, year_level, term, block) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
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
        $conflictWith,
        $isCITCC,
        $lecRoom,
        $lecInstructor,
        $lecDays,
        $lecStartTime,
        $lecEndTime,
        $labRoom,
        $labInstructor,
        $labDays,
        $labStartTime,
        $labEndTime,
        $yearLevel,
        $term,
        $block
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
    
    // Normalize days to comma-separated format (form uses '/', database uses ',')
    $rawDays = $data['days'] ?? '';
    if (is_array($rawDays)) {
        $days = implode(',', $rawDays);
    } else {
        // Convert '/' to ',' for consistency
        $days = str_replace('/', ',', $rawDays);
    }
    
    // Convert empty strings to null for foreign keys
    $departmentId = !empty($data['department']) ? $data['department'] : null;
    $roomId = !empty($data['roomId']) ? $data['roomId'] : null;
    $scheduleDate = !empty($data['date']) ? $data['date'] : null;
    
    // CIT/CC dual schedule fields
    $isCITCC = !empty($data['isCITCC']) ? 1 : 0;
    $lecRoom = !empty($data['lecRoom']) ? $data['lecRoom'] : null;
    $lecInstructor = !empty($data['lecInstructor']) ? $data['lecInstructor'] : null;
    $lecDays = !empty($data['lecDays']) ? str_replace('/', ',', $data['lecDays']) : null;
    $lecStartTime = !empty($data['lecStartTime']) ? $data['lecStartTime'] : null;
    $lecEndTime = !empty($data['lecEndTime']) ? $data['lecEndTime'] : null;
    $labRoom = !empty($data['labRoom']) ? $data['labRoom'] : null;
    $labInstructor = !empty($data['labInstructor']) ? $data['labInstructor'] : null;
    $labDays = !empty($data['labDays']) ? str_replace('/', ',', $data['labDays']) : null;
    $labStartTime = !empty($data['labStartTime']) ? $data['labStartTime'] : null;
    $labEndTime = !empty($data['labEndTime']) ? $data['labEndTime'] : null;
    
    // Year Level, Term, Block
    $yearLevel = !empty($data['yearLevel']) ? $data['yearLevel'] : null;
    $term = !empty($data['term']) ? $data['term'] : null;
    $block = !empty($data['block']) ? $data['block'] : null;
    
    $sql = "UPDATE schedules SET 
            class_code = ?, class_name = ?, room_id = ?, room_display = ?, instructor = ?, 
            department_id = ?, class_size = ?, schedule_date = ?, start_time = ?, end_time = ?, 
            days = ?, semester = ?, school_year = ?,
            is_citcc = ?, lec_room = ?, lec_instructor = ?, lec_days = ?, lec_start_time = ?, lec_end_time = ?,
            lab_room = ?, lab_instructor = ?, lab_days = ?, lab_start_time = ?, lab_end_time = ?,
            year_level = ?, term = ?, block = ?
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
        $isCITCC,
        $lecRoom,
        $lecInstructor,
        $lecDays,
        $lecStartTime,
        $lecEndTime,
        $labRoom,
        $labInstructor,
        $labDays,
        $labStartTime,
        $labEndTime,
        $yearLevel,
        $term,
        $block,
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
    // Handle days stored with either '/' or ',' delimiter
    $daysRaw = $row['days'] ?? '';
    if ($daysRaw) {
        // If it contains '/', split by '/', otherwise split by ','
        if (strpos($daysRaw, '/') !== false) {
            $daysArray = explode('/', $daysRaw);
        } else {
            $daysArray = explode(',', $daysRaw);
        }
        $daysArray = array_map('trim', $daysArray);
    } else {
        $daysArray = [];
    }
    
    // Format lecture days
    $lecDaysRaw = $row['lec_days'] ?? '';
    if ($lecDaysRaw) {
        $lecDaysRaw = str_replace(',', '/', $lecDaysRaw);
    }
    
    // Format lab days
    $labDaysRaw = $row['lab_days'] ?? '';
    if ($labDaysRaw) {
        $labDaysRaw = str_replace(',', '/', $labDaysRaw);
    }
    
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
        'days' => $daysArray,
        'startTime' => substr($row['start_time'], 0, 5),
        'endTime' => substr($row['end_time'], 0, 5),
        'semester' => $row['semester'],
        'schoolYear' => $row['school_year'],
        'hasConflict' => !empty($row['has_conflict']),
        'conflictWith' => $row['conflict_with'] ?? null,
        // CIT/CC dual schedule fields
        'isCITCC' => !empty($row['is_citcc']),
        'lecRoom' => $row['lec_room'] ?? null,
        'lecInstructor' => $row['lec_instructor'] ?? null,
        'lecDays' => $lecDaysRaw,
        'lecStartTime' => $row['lec_start_time'] ? substr($row['lec_start_time'], 0, 5) : null,
        'lecEndTime' => $row['lec_end_time'] ? substr($row['lec_end_time'], 0, 5) : null,
        'labRoom' => $row['lab_room'] ?? null,
        'labInstructor' => $row['lab_instructor'] ?? null,
        'labDays' => $labDaysRaw,
        'labStartTime' => $row['lab_start_time'] ? substr($row['lab_start_time'], 0, 5) : null,
        'labEndTime' => $row['lab_end_time'] ? substr($row['lab_end_time'], 0, 5) : null,
        // Year Level, Term, Block
        'yearLevel' => $row['year_level'] ?? null,
        'term' => $row['term'] ?? null,
        'block' => $row['block'] ?? null
    ];
}
