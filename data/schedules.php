<?php
/**
 * Assignments/Schedules Data for UC Nexus
 * Handles class schedules and room assignments
 */

// Sample schedule data (this would typically come from a database)
$SCHEDULES = [
    [
        'id' => 1,
        'classCode' => 'IT101',
        'className' => 'Introduction to Computing',
        'roomId' => 'room-M303',
        'room' => 'M303 - Computer Laboratory',
        'instructor' => 'Dr. Maria Santos',
        'department' => 'CCS',
        'classSize' => 35,
        'days' => ['monday', 'wednesday'],
        'startTime' => '07:30',
        'endTime' => '08:50',
        'semester' => '2nd Semester',
        'schoolYear' => '2025-2026'
    ],
    [
        'id' => 2,
        'classCode' => 'IT102',
        'className' => 'Web Development Fundamentals',
        'roomId' => 'room-M304',
        'room' => 'M304 - Computer Laboratory',
        'instructor' => 'Prof. Juan Cruz',
        'department' => 'CCS',
        'classSize' => 38,
        'days' => ['tuesday', 'thursday'],
        'startTime' => '08:50',
        'endTime' => '10:10',
        'semester' => '2nd Semester',
        'schoolYear' => '2025-2026'
    ],
    [
        'id' => 3,
        'classCode' => 'CE201',
        'className' => 'Structural Analysis',
        'roomId' => 'room-S213',
        'room' => 'S213 - CEA Computer Laboratory',
        'instructor' => 'Engr. Pedro Reyes',
        'department' => 'CEA',
        'classSize' => 28,
        'days' => ['monday', 'wednesday', 'friday'],
        'startTime' => '10:10',
        'endTime' => '11:30',
        'semester' => '2nd Semester',
        'schoolYear' => '2025-2026'
    ],
    [
        'id' => 4,
        'classCode' => 'CHEM101',
        'className' => 'General Chemistry',
        'roomId' => 'room-S107',
        'room' => 'S107 - Chemistry Laboratory',
        'instructor' => 'Dr. Ana Lopez',
        'department' => 'CAS',
        'classSize' => 35,
        'days' => ['tuesday', 'thursday'],
        'startTime' => '11:30',
        'endTime' => '12:50',
        'semester' => '2nd Semester',
        'schoolYear' => '2025-2026'
    ],
    [
        'id' => 5,
        'classCode' => 'BIO101',
        'className' => 'General Biology',
        'roomId' => 'room-S229',
        'room' => 'S229 - Biology Laboratory',
        'instructor' => 'Dr. Carlos Garcia',
        'department' => 'CAS',
        'classSize' => 25,
        'days' => ['monday', 'wednesday'],
        'startTime' => '12:50',
        'endTime' => '14:10',
        'semester' => '2nd Semester',
        'schoolYear' => '2025-2026'
    ],
    [
        'id' => 6,
        'classCode' => 'PE101',
        'className' => 'Physical Education 1',
        'roomId' => 'room-G101',
        'room' => 'G101 - Gymnasium',
        'instructor' => 'Coach Roberto Tan',
        'department' => 'PE',
        'classSize' => 50,
        'days' => ['friday'],
        'startTime' => '14:10',
        'endTime' => '15:30',
        'semester' => '2nd Semester',
        'schoolYear' => '2025-2026'
    ],
    [
        'id' => 7,
        'classCode' => 'IT201',
        'className' => 'Object-Oriented Programming',
        'roomId' => 'room-M305',
        'room' => 'M305 - Computer Laboratory',
        'instructor' => 'Prof. Elena Mendoza',
        'department' => 'CCS',
        'classSize' => 40,
        'days' => ['monday', 'wednesday'],
        'startTime' => '15:30',
        'endTime' => '16:50',
        'semester' => '2nd Semester',
        'schoolYear' => '2025-2026'
    ],
    [
        'id' => 8,
        'classCode' => 'PHYS101',
        'className' => 'Physics 1',
        'roomId' => 'room-S016',
        'room' => 'S016 - Physics Lab',
        'instructor' => 'Dr. Antonio Ramos',
        'department' => 'CAS',
        'classSize' => 45,
        'days' => ['tuesday', 'thursday'],
        'startTime' => '16:50',
        'endTime' => '18:10',
        'semester' => '2nd Semester',
        'schoolYear' => '2025-2026'
    ],
    [
        'id' => 9,
        'classCode' => 'HM101',
        'className' => 'Introduction to Hospitality',
        'roomId' => 'room-F501',
        'room' => 'F501 - Classroom/Lecture Hall',
        'instructor' => 'Ms. Sofia Villanueva',
        'department' => 'CHTM',
        'classSize' => 48,
        'days' => ['monday', 'wednesday', 'friday'],
        'startTime' => '08:50',
        'endTime' => '10:10',
        'semester' => '2nd Semester',
        'schoolYear' => '2025-2026'
    ],
    [
        'id' => 10,
        'classCode' => 'EDUC101',
        'className' => 'Principles of Teaching',
        'roomId' => 'room-N201',
        'room' => 'N201 - Classroom/Lecture Hall',
        'instructor' => 'Dr. Isabella Fernandez',
        'department' => 'CED',
        'classSize' => 38,
        'days' => ['tuesday', 'thursday', 'saturday'],
        'startTime' => '07:30',
        'endTime' => '08:50',
        'semester' => '2nd Semester',
        'schoolYear' => '2025-2026'
    ]
];

// Departments list
$DEPARTMENTS = [
    'CCS' => 'College of Computer Studies',
    'CEA' => 'College of Engineering and Architecture',
    'CAS' => 'College of Arts and Sciences',
    'CHTM' => 'College of Hospitality and Tourism Management',
    'CED' => 'College of Education',
    'CBA' => 'College of Business Administration',
    'CON' => 'College of Nursing',
    'PE' => 'Physical Education',
    'SHS' => 'Senior High School',
    'JHS' => 'Junior High School'
];

// =============== HELPER FUNCTIONS ===============

/**
 * Get all schedules
 */
function getAllSchedules() {
    global $SCHEDULES;
    return $SCHEDULES;
}

/**
 * Get schedule by ID
 */
function getScheduleById($id) {
    global $SCHEDULES;
    foreach ($SCHEDULES as $schedule) {
        if ($schedule['id'] == $id) {
            return $schedule;
        }
    }
    return null;
}

/**
 * Get schedules by room
 */
function getSchedulesByRoom($roomId) {
    global $SCHEDULES;
    return array_filter($SCHEDULES, function($schedule) use ($roomId) {
        return $schedule['roomId'] === $roomId;
    });
}

/**
 * Get schedules by day
 */
function getSchedulesByDay($day) {
    global $SCHEDULES;
    return array_filter($SCHEDULES, function($schedule) use ($day) {
        return in_array(strtolower($day), array_map('strtolower', $schedule['days']));
    });
}

/**
 * Get schedules by department
 */
function getSchedulesByDepartment($department) {
    global $SCHEDULES;
    return array_filter($SCHEDULES, function($schedule) use ($department) {
        return $schedule['department'] === $department;
    });
}

/**
 * Get schedules by instructor
 */
function getSchedulesByInstructor($instructor) {
    global $SCHEDULES;
    $instructor = strtolower($instructor);
    return array_filter($SCHEDULES, function($schedule) use ($instructor) {
        return strpos(strtolower($schedule['instructor']), $instructor) !== false;
    });
}

/**
 * Check if a room is available at a specific time slot
 */
function isRoomAvailable($roomId, $day, $startTime, $endTime) {
    global $SCHEDULES;
    
    foreach ($SCHEDULES as $schedule) {
        if ($schedule['roomId'] !== $roomId) continue;
        if (!in_array(strtolower($day), array_map('strtolower', $schedule['days']))) continue;
        
        // Check time overlap
        $schedStart = strtotime($schedule['startTime']);
        $schedEnd = strtotime($schedule['endTime']);
        $checkStart = strtotime($startTime);
        $checkEnd = strtotime($endTime);
        
        // If times overlap, room is not available
        if ($checkStart < $schedEnd && $checkEnd > $schedStart) {
            return false;
        }
    }
    
    return true;
}

/**
 * Get total scheduled classes
 */
function getTotalScheduledClasses() {
    global $SCHEDULES;
    return count($SCHEDULES);
}

/**
 * Get schedules for today
 */
function getTodaySchedules() {
    $today = strtolower(date('l')); // Get current day name
    return getSchedulesByDay($today);
}

/**
 * Get upcoming schedules (within next 7 days)
 */
function getUpcomingSchedules() {
    global $SCHEDULES;
    $upcoming = [];
    $currentTime = date('H:i');
    $today = strtolower(date('l'));
    
    // Simple approach: get all schedules that haven't ended today
    foreach ($SCHEDULES as $schedule) {
        if (in_array($today, array_map('strtolower', $schedule['days']))) {
            if ($schedule['endTime'] > $currentTime) {
                $upcoming[] = $schedule;
            }
        }
    }
    
    return $upcoming;
}

/**
 * Get departments list
 */
function getDepartments() {
    global $DEPARTMENTS;
    return $DEPARTMENTS;
}

/**
 * Get schedule statistics
 */
function getScheduleStatistics() {
    global $SCHEDULES, $DEPARTMENTS;
    
    $stats = [
        'total' => count($SCHEDULES),
        'byDepartment' => [],
        'byDay' => [
            'monday' => 0, 'tuesday' => 0, 'wednesday' => 0,
            'thursday' => 0, 'friday' => 0, 'saturday' => 0
        ],
        'totalStudents' => 0
    ];
    
    foreach ($SCHEDULES as $schedule) {
        // Count by department
        $dept = $schedule['department'];
        if (!isset($stats['byDepartment'][$dept])) {
            $stats['byDepartment'][$dept] = 0;
        }
        $stats['byDepartment'][$dept]++;
        
        // Count by day
        foreach ($schedule['days'] as $day) {
            $day = strtolower($day);
            if (isset($stats['byDay'][$day])) {
                $stats['byDay'][$day]++;
            }
        }
        
        // Total students
        $stats['totalStudents'] += $schedule['classSize'];
    }
    
    return $stats;
}

/**
 * Format days array to string
 */
function formatDays($days) {
    $dayAbbrev = [
        'monday' => 'M', 'tuesday' => 'T', 'wednesday' => 'W',
        'thursday' => 'Th', 'friday' => 'F', 'saturday' => 'S'
    ];
    
    $formatted = [];
    foreach ($days as $day) {
        $day = strtolower($day);
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
