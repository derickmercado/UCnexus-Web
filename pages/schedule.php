<?php
// pages/schedule.php
require_once __DIR__ . '/../data/rooms.php';
require_once __DIR__ . '/../data/buildings.php';
require_once __DIR__ . '/../data/schedules.php';

// Check if database is available
$useDatabase = isDatabaseSetup();

// Get room options for dropdown
$roomOptions = getRoomsAsOptions();
$departments = getDepartments();
$timeSlots = getTimeSlots();

// Initialize schedule data (only needed for session-based storage)
if (!$useDatabase && !isset($_SESSION['schedules'])) {
    $_SESSION['schedules'] = [];
}

// =============== SMART AUTO-FILL/AUTO-CORRECT FUNCTIONS ===============

/**
 * Smart match room - finds the best matching room from available options
 * @param string $input - User input (partial room name/code)
 * @param array $roomOptions - Available room options from database
 * @return array - ['matched' => bool, 'room' => full room label, 'original' => input, 'corrected' => bool]
 */
function smartMatchRoom($input, $roomOptions) {
    $input = trim($input);
    if (empty($input)) {
        return ['matched' => false, 'room' => '', 'original' => $input, 'corrected' => false];
    }
    
    // First, check for exact match
    foreach ($roomOptions as $room) {
        if (strcasecmp($room['label'], $input) === 0) {
            return ['matched' => true, 'room' => $room['label'], 'original' => $input, 'corrected' => false];
        }
    }
    
    // Clean input - extract room code (e.g., "M303", "S312")
    $inputLower = strtolower($input);
    $inputClean = preg_replace('/[^a-z0-9]/', '', $inputLower);
    
    // Try to match by room code at the beginning of label
    foreach ($roomOptions as $room) {
        $labelLower = strtolower($room['label']);
        // Check if label starts with the input
        if (strpos($labelLower, $inputLower) === 0) {
            return ['matched' => true, 'room' => $room['label'], 'original' => $input, 'corrected' => true];
        }
    }
    
    // Try partial match - input is contained in label
    $bestMatch = null;
    $bestScore = 0;
    foreach ($roomOptions as $room) {
        $labelLower = strtolower($room['label']);
        $labelClean = preg_replace('/[^a-z0-9]/', '', $labelLower);
        
        // Check if cleaned input is in cleaned label
        if (strpos($labelClean, $inputClean) !== false || strpos($labelLower, $inputLower) !== false) {
            // Score based on how close the match is
            $score = similar_text($inputLower, $labelLower);
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $room['label'];
            }
        }
    }
    
    if ($bestMatch) {
        return ['matched' => true, 'room' => $bestMatch, 'original' => $input, 'corrected' => true];
    }
    
    // Try fuzzy match as last resort
    foreach ($roomOptions as $room) {
        $labelLower = strtolower($room['label']);
        $similarity = 0;
        similar_text($inputLower, $labelLower, $similarity);
        if ($similarity > 60) { // 60% similar
            if ($similarity > $bestScore) {
                $bestScore = $similarity;
                $bestMatch = $room['label'];
            }
        }
    }
    
    if ($bestMatch) {
        return ['matched' => true, 'room' => $bestMatch, 'original' => $input, 'corrected' => true];
    }
    
    // No match found - return original
    return ['matched' => false, 'room' => $input, 'original' => $input, 'corrected' => false];
}

/**
 * Smart parse date - handles various date formats
 * @param string $input - Date string in various formats
 * @return array - ['parsed' => Y-m-d format or null, 'original' => input, 'corrected' => bool]
 */
function smartParseDate($input) {
    $input = trim($input);
    if (empty($input)) {
        return ['parsed' => null, 'original' => $input, 'corrected' => false];
    }
    
    // Try standard strtotime first
    $timestamp = strtotime($input);
    if ($timestamp !== false) {
        return ['parsed' => date('Y-m-d', $timestamp), 'original' => $input, 'corrected' => true];
    }
    
    // Try common date formats manually
    $formats = [
        'd/m/Y', 'd-m-Y', 'd.m.Y',  // DD/MM/YYYY
        'm/d/Y', 'm-d-Y', 'm.d.Y',  // MM/DD/YYYY
        'Y/m/d', 'Y-m-d', 'Y.m.d',  // YYYY/MM/DD
        'd/m/y', 'd-m-y', 'd.m.y',  // DD/MM/YY
        'm/d/y', 'm-d-y', 'm.d.y',  // MM/DD/YY
        'j/n/Y', 'j-n-Y', 'j.n.Y',  // D/M/YYYY (no leading zeros)
        'n/j/Y', 'n-j-Y', 'n.j.Y',  // M/D/YYYY (no leading zeros)
        'F j, Y', 'M j, Y',          // Month name formats
        'j F Y', 'j M Y',
    ];
    
    foreach ($formats as $format) {
        $date = DateTime::createFromFormat($format, $input);
        if ($date !== false) {
            return ['parsed' => $date->format('Y-m-d'), 'original' => $input, 'corrected' => true];
        }
    }
    
    return ['parsed' => null, 'original' => $input, 'corrected' => false];
}

/**
 * Smart parse time - handles various time formats
 * @param string $input - Time string in various formats
 * @return array - ['parsed' => H:i:s format, 'original' => input, 'corrected' => bool]
 */
function smartParseTime($input) {
    $input = trim($input);
    if (empty($input)) {
        return ['parsed' => '00:00:00', 'original' => $input, 'corrected' => false];
    }
    
    // Clean up common variations
    $input = str_ireplace(['am', 'pm', 'a.m.', 'p.m.', 'a.m', 'p.m'], 
                          ['AM', 'PM', 'AM', 'PM', 'AM', 'PM'], $input);
    
    // Try strtotime
    $timestamp = strtotime($input);
    if ($timestamp !== false) {
        return ['parsed' => date('H:i:s', $timestamp), 'original' => $input, 'corrected' => true];
    }
    
    // Try manual parsing for formats like "7:30", "730", "0730"
    if (preg_match('/^(\d{1,2}):?(\d{2})(?:\s*(AM|PM))?$/i', $input, $matches)) {
        $hour = (int)$matches[1];
        $minute = (int)$matches[2];
        $ampm = strtoupper($matches[3] ?? '');
        
        if ($ampm === 'PM' && $hour < 12) $hour += 12;
        if ($ampm === 'AM' && $hour === 12) $hour = 0;
        
        return ['parsed' => sprintf('%02d:%02d:00', $hour, $minute), 'original' => $input, 'corrected' => true];
    }
    
    // Try 4-digit format (0730, 1430)
    if (preg_match('/^(\d{4})$/', $input, $matches)) {
        $hour = (int)substr($matches[1], 0, 2);
        $minute = (int)substr($matches[1], 2, 2);
        if ($hour < 24 && $minute < 60) {
            return ['parsed' => sprintf('%02d:%02d:00', $hour, $minute), 'original' => $input, 'corrected' => true];
        }
    }
    
    return ['parsed' => '00:00:00', 'original' => $input, 'corrected' => false];
}

/**
 * Smart match department - infers department from class code patterns
 * @param string $classCode - Class code (e.g., "IT101", "BSCS301")
 * @param array $departments - Available departments
 * @return array - ['matched' => bool, 'department' => dept id or empty, 'corrected' => bool]
 */
function smartMatchDepartment($classCode, $departments) {
    $classCode = strtoupper(trim($classCode));
    if (empty($classCode) || empty($departments)) {
        return ['matched' => false, 'department' => '', 'corrected' => false];
    }
    
    // Common department code patterns
    $patterns = [
        'CCS' => ['IT', 'CS', 'CIT', 'BSIT', 'BSCS', 'CC', 'ITE', 'PROG', 'WEB', 'DATA'],
        'COE' => ['CE', 'ECE', 'EE', 'BSCE', 'BSECE', 'BSEE', 'ELEC', 'ENGR'],
        'CBA' => ['BA', 'BSBA', 'ACC', 'ACCT', 'MGT', 'MKT', 'FIN', 'ECON', 'BUS'],
        'CNAHS' => ['NUR', 'NURS', 'BSN', 'MED', 'HEALTH', 'PHARMA'],
        'CCJE' => ['CRIM', 'BSCRIM', 'LAW', 'JUSTICE'],
        'COED' => ['ED', 'BSED', 'BEED', 'TEACH', 'EDUC'],
        'CAS' => ['MATH', 'SCI', 'BIO', 'CHEM', 'PHYS', 'ENG', 'LIT', 'HIST', 'PSYCH', 'SOC'],
        'CHTM' => ['HM', 'BSHM', 'TOUR', 'HOTEL', 'CULINARY'],
    ];
    
    // Extract prefix from class code (letters before numbers)
    preg_match('/^([A-Z]+)/', $classCode, $matches);
    $prefix = $matches[1] ?? '';
    
    foreach ($patterns as $deptId => $prefixes) {
        if (in_array($prefix, $prefixes)) {
            if (isset($departments[$deptId])) {
                return ['matched' => true, 'department' => $deptId, 'corrected' => true];
            }
        }
    }
    
    // Try partial match on department names
    foreach ($departments as $deptId => $deptName) {
        if (stripos($deptId, $prefix) !== false || stripos($prefix, $deptId) !== false) {
            return ['matched' => true, 'department' => $deptId, 'corrected' => true];
        }
    }
    
    return ['matched' => false, 'department' => '', 'corrected' => false];
}

/**
 * Smart capitalize name - properly formats instructor names
 * @param string $name - Instructor name
 * @return array - ['formatted' => formatted name, 'original' => input, 'corrected' => bool]
 */
function smartCapitalizeName($name) {
    $name = trim($name);
    if (empty($name)) {
        return ['formatted' => '', 'original' => $name, 'corrected' => false];
    }
    
    $original = $name;
    
    // Convert to title case
    $name = mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
    
    // Handle special cases
    $name = preg_replace_callback('/\b(Mc|Mac|O\')([a-z])/i', function($m) {
        return $m[1] . strtoupper($m[2]);
    }, $name);
    
    // Fix common titles
    $titles = [
        'Dr.' => 'Dr.', 'Dr' => 'Dr.', 'DR' => 'Dr.',
        'Prof.' => 'Prof.', 'Prof' => 'Prof.', 'PROF' => 'Prof.',
        'Mr.' => 'Mr.', 'Mr' => 'Mr.', 'MR' => 'Mr.',
        'Ms.' => 'Ms.', 'Ms' => 'Ms.', 'MS' => 'Ms.',
        'Mrs.' => 'Mrs.', 'Mrs' => 'Mrs.', 'MRS' => 'Mrs.',
        'Engr.' => 'Engr.', 'Engr' => 'Engr.', 'ENGR' => 'Engr.',
        'Atty.' => 'Atty.', 'Atty' => 'Atty.', 'ATTY' => 'Atty.',
    ];
    
    foreach ($titles as $search => $replace) {
        $name = preg_replace('/\b' . preg_quote($search, '/') . '\b/i', $replace, $name);
    }
    
    // Handle suffixes
    $name = preg_replace_callback('/\b(Jr|Sr|Ii|Iii|Iv)\b/i', function($m) {
        $suffix = strtoupper($m[0]);
        return ($suffix === 'II' || $suffix === 'III' || $suffix === 'IV') ? $suffix : ucfirst(strtolower($m[0])) . '.';
    }, $name);
    
    return ['formatted' => $name, 'original' => $original, 'corrected' => $name !== $original];
}

/**
 * Validate instructor name
 * - Must have at least 2 words (first and last name)
 * - Each word must be at least 2 characters
 * - Only letters, spaces, hyphens, periods, and apostrophes allowed
 * - No random gibberish (checks for vowel presence)
 */
function isValidInstructorName($name) {
    $name = trim($name);
    
    // Check if empty
    if (empty($name)) {
        return ['valid' => false, 'message' => 'Instructor name is required.'];
    }
    
    // Only allow letters, spaces, hyphens, periods, and apostrophes
    if (!preg_match('/^[a-zA-Z\s\-\.\'\,]+$/', $name)) {
        return ['valid' => false, 'message' => 'Instructor name can only contain letters, spaces, hyphens, periods, and apostrophes.'];
    }
    
    // Split into words (filter out titles like Dr., Prof., etc.)
    $words = preg_split('/\s+/', $name);
    $words = array_filter($words, function($word) {
        $word = trim($word, '.,');
        return strlen($word) >= 2;
    });
    
    // Must have at least 2 words (first and last name)
    if (count($words) < 2) {
        return ['valid' => false, 'message' => 'Please enter a full name (first and last name).'];
    }
    
    // Check each significant word for validity (must contain vowels - no gibberish)
    $vowels = ['a', 'e', 'i', 'o', 'u', 'y'];
    $titles = ['dr', 'prof', 'mr', 'ms', 'mrs', 'sir', 'ma', 'engr', 'atty'];
    
    foreach ($words as $word) {
        $word = strtolower(trim($word, '.,'));
        
        // Skip common titles
        if (in_array($word, $titles)) {
            continue;
        }
        
        // Word must be at least 2 characters
        if (strlen($word) < 2) {
            continue;
        }
        
        // Check if word contains at least one vowel (filters gibberish like "jlhkgdf")
        $hasVowel = false;
        foreach ($vowels as $vowel) {
            if (strpos($word, $vowel) !== false) {
                $hasVowel = true;
                break;
            }
        }
        
        if (!$hasVowel && strlen($word) > 2) {
            return ['valid' => false, 'message' => 'Please enter a valid instructor name.'];
        }
        
        // Check for too many consecutive consonants (more than 4 is likely gibberish)
        if (preg_match('/[^aeiouy]{5,}/i', $word)) {
            return ['valid' => false, 'message' => 'Please enter a valid instructor name.'];
        }
    }
    
    return ['valid' => true, 'message' => ''];
}

/**
 * Check if a room is available at a specific date and time
 * Returns array with 'available' boolean and 'conflict' info if not available
 */
function checkRoomAvailability($room, $date, $startTime, $endTime, $excludeScheduleId = null) {
    global $useDatabase;
    
    // Get all schedules
    if ($useDatabase) {
        $allSchedules = getAllSchedules();
    } else {
        $allSchedules = $_SESSION['schedules'] ?? [];
    }
    
    foreach ($allSchedules as $schedule) {
        // Skip the schedule being edited
        if ($excludeScheduleId !== null && isset($schedule['id']) && $schedule['id'] == $excludeScheduleId) {
            continue;
        }
        
        // Check if same room and same date
        if (($schedule['room'] ?? '') === $room && ($schedule['date'] ?? '') === $date) {
            $existingStart = $schedule['startTime'] ?? '';
            $existingEnd = $schedule['endTime'] ?? '';
            
            // Check for time overlap
            // Overlap occurs if: (new start < existing end) AND (new end > existing start)
            if ($startTime < $existingEnd && $endTime > $existingStart) {
                return [
                    'available' => false,
                    'conflict' => [
                        'className' => $schedule['className'] ?? 'Unknown Class',
                        'classCode' => $schedule['classCode'] ?? '',
                        'startTime' => date('g:i A', strtotime($existingStart)),
                        'endTime' => date('g:i A', strtotime($existingEnd)),
                        'instructor' => $schedule['instructor'] ?? ''
                    ]
                ];
            }
        }
    }
    
    return ['available' => true, 'conflict' => null];
}

// Store form data for repopulation on validation error
$formData = [
    'classCode' => '',
    'className' => '',
    'scheduleRoom' => '',
    'instructor' => '',
    'scheduleDate' => '',
    'startTime' => '',
    'endTime' => ''
];
$validationError = '';
$showForm = false;
$editMode = false;
$editScheduleId = 0;

/**
 * Validate schedule time - check if AM time is entered when it's already PM
 * Returns array with 'valid' boolean and 'message' if there's an issue
 */
function validateScheduleTime($date, $startTime, $endTime) {
    $today = date('Y-m-d');
    $currentHour = (int)date('H');
    
    // Only validate if schedule is for today
    if ($date !== $today) {
        return ['valid' => true, 'message' => ''];
    }
    
    $startHour = (int)substr($startTime, 0, 2);
    $endHour = (int)substr($endTime, 0, 2);
    
    // If current time is PM (12:00 or later) and user entered AM time (before 12:00)
    if ($currentHour >= 12) {
        // Check if start time is in AM and has already passed
        if ($startHour < 12 && $startHour < $currentHour - 12) {
            $suggestedStart = date('g:i A', strtotime($startTime) + 12 * 3600);
            $suggestedEnd = date('g:i A', strtotime($endTime) + 12 * 3600);
            return [
                'valid' => false,
                'message' => "You entered " . date('g:i A', strtotime($startTime)) . " but it's already " . date('g:i A') . ". Did you mean {$suggestedStart} - {$suggestedEnd} (PM)?"
            ];
        }
        
        // Check if the entered time has already passed today
        if ($startHour < $currentHour) {
            return [
                'valid' => false,
                'message' => "The start time " . date('g:i A', strtotime($startTime)) . " has already passed. It's currently " . date('g:i A') . ". Please select a future time."
            ];
        }
    }
    
    // Validate end time is after start time
    if ($endTime <= $startTime) {
        return [
            'valid' => false,
            'message' => "End time must be after start time."
        ];
    }
    
    return ['valid' => true, 'message' => ''];
}

// Handle adding schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_schedule') {
    // Store form data for repopulation
    $formData = [
        'classCode' => $_POST['classCode'] ?? '',
        'className' => $_POST['className'] ?? '',
        'scheduleRoom' => $_POST['scheduleRoom'] ?? '',
        'instructor' => $_POST['instructor'] ?? '',
        'scheduleDate' => $_POST['scheduleDate'] ?? '',
        'startTime' => $_POST['startTime'] ?? '',
        'endTime' => $_POST['endTime'] ?? ''
    ];
    
    // Validate instructor name first
    $instructorValidation = isValidInstructorName($_POST['instructor'] ?? '');
    
    if (!$instructorValidation['valid']) {
        // Don't redirect - keep form open with data and show error
        $validationError = $instructorValidation['message'];
        $showForm = true;
    } else {
        // Validate schedule time (check for AM/PM issues)
        $timeValidation = validateScheduleTime(
            $_POST['scheduleDate'] ?? '',
            $_POST['startTime'] ?? '',
            $_POST['endTime'] ?? ''
        );
        
        if (!$timeValidation['valid']) {
            $validationError = $timeValidation['message'];
            $showForm = true;
        } else {
            // Check room availability
            $roomCheck = checkRoomAvailability(
                $_POST['scheduleRoom'] ?? '',
                $_POST['scheduleDate'] ?? '',
                $_POST['startTime'] ?? '',
                $_POST['endTime'] ?? ''
            );
            
            if (!$roomCheck['available']) {
                $conflict = $roomCheck['conflict'];
                $validationError = "Room not available! Already booked for \"{$conflict['classCode']} - {$conflict['className']}\" from {$conflict['startTime']} to {$conflict['endTime']} by {$conflict['instructor']}.";
                $showForm = true;
            } else {
                // Validation passed, proceed with saving
                if ($useDatabase) {
                    // Use database
                    $newSchedule = [
                        'classCode' => htmlspecialchars($_POST['classCode'] ?? ''),
                        'className' => htmlspecialchars($_POST['className'] ?? ''),
                        'room' => htmlspecialchars($_POST['scheduleRoom'] ?? ''),
                        'instructor' => htmlspecialchars($_POST['instructor'] ?? ''),
                        'date' => htmlspecialchars($_POST['scheduleDate'] ?? ''),
                        'startTime' => htmlspecialchars($_POST['startTime'] ?? ''),
                        'endTime' => htmlspecialchars($_POST['endTime'] ?? ''),
                        'department' => htmlspecialchars($_POST['department'] ?? ''),
                        'classSize' => intval($_POST['classSize'] ?? 0),
                        'days' => isset($_POST['days']) ? $_POST['days'] : []
                    ];
                    if (addSchedule($newSchedule)) {
                        $_SESSION['success'] = 'Schedule added successfully!';
                    } else {
                        $_SESSION['error'] = 'Failed to add schedule.';
                    }
                } else {
                    // Use session storage
                    $maxId = 0;
                    foreach ($_SESSION['schedules'] as $schedule) {
                        if (isset($schedule['id']) && $schedule['id'] > $maxId) {
                            $maxId = $schedule['id'];
                        }
                    }
                    
                    $newSchedule = [
                        'id' => $maxId + 1,
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
                }
                header('Location: ?page=schedule');
                exit();
            }
        }
    }
}

// Handle deleting schedule
if (isset($_GET['delete_schedule'])) {
    $id = intval($_GET['delete_schedule']);
    if ($useDatabase) {
        if (deleteSchedule($id)) {
            $_SESSION['success'] = 'Schedule deleted successfully!';
        } else {
            $_SESSION['error'] = 'Failed to delete schedule.';
        }
    } else {
        $_SESSION['schedules'] = array_filter($_SESSION['schedules'], function($schedule) use ($id) {
            return $schedule['id'] !== $id;
        });
        $_SESSION['success'] = 'Schedule deleted successfully!';
    }
    header('Location: ?page=schedule');
    exit();
}

// Handle clear all schedules
if (isset($_GET['clear_all_schedules']) && $_GET['clear_all_schedules'] === 'confirm') {
    if ($useDatabase) {
        if (clearAllSchedules()) {
            $_SESSION['success'] = 'All schedules have been cleared!';
        } else {
            $_SESSION['error'] = 'Failed to clear schedules.';
        }
    } else {
        $_SESSION['schedules'] = [];
        $_SESSION['success'] = 'All schedules have been cleared!';
    }
    header('Location: ?page=schedule');
    exit();
}

// Handle CSV import
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'import_csv') {
    if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] === UPLOAD_ERR_OK) {
        $file = fopen($_FILES['csvFile']['tmp_name'], 'r');
        $header = fgetcsv($file); // Skip header
        $importCount = 0;
        $conflictCount = 0;
        $successList = [];
        $conflictList = [];
        $correctionsList = []; // Track auto-corrections made
        
        if ($useDatabase) {
            // Import to database with smart auto-fill
            while (($data = fgetcsv($file)) !== false) {
                if (count($data) >= 6 && !empty(trim($data[0]))) {
                    $corrections = []; // Track corrections for this row
                    
                    // Smart parse date
                    $rawDate = trim($data[4]);
                    $dateResult = smartParseDate($rawDate);
                    $parsedDate = $dateResult['parsed'];
                    if ($dateResult['corrected'] && $dateResult['parsed']) {
                        $corrections[] = "Date: '{$rawDate}' → '" . date('d/m/Y', strtotime($parsedDate)) . "'";
                    }
                    
                    // Smart parse times
                    $rawStartTime = trim($data[5]);
                    $rawEndTime = trim($data[6] ?? '');
                    $startTimeResult = smartParseTime($rawStartTime);
                    $endTimeResult = smartParseTime($rawEndTime);
                    $parsedStartTime = $startTimeResult['parsed'];
                    $parsedEndTime = $endTimeResult['parsed'];
                    
                    if ($startTimeResult['corrected'] && $rawStartTime !== date('H:i:s', strtotime($parsedStartTime))) {
                        $corrections[] = "Start: '{$rawStartTime}' → '" . date('g:i A', strtotime($parsedStartTime)) . "'";
                    }
                    if ($endTimeResult['corrected'] && $rawEndTime !== date('H:i:s', strtotime($parsedEndTime))) {
                        $corrections[] = "End: '{$rawEndTime}' → '" . date('g:i A', strtotime($parsedEndTime)) . "'";
                    }
                    
                    // Smart match room
                    $rawRoom = trim($data[2]);
                    $roomResult = smartMatchRoom($rawRoom, $roomOptions);
                    $room = $roomResult['room'];
                    if ($roomResult['corrected'] && $roomResult['matched']) {
                        $corrections[] = "Room: '{$rawRoom}' → '{$room}'";
                    }
                    
                    // Smart capitalize instructor name
                    $rawInstructor = trim($data[3]);
                    $instructorResult = smartCapitalizeName($rawInstructor);
                    $instructor = $instructorResult['formatted'];
                    if ($instructorResult['corrected']) {
                        $corrections[] = "Instructor: '{$rawInstructor}' → '{$instructor}'";
                    }
                    
                    // Smart match department (auto-fill if empty)
                    $rawDepartment = trim($data[7] ?? '');
                    $classCode = trim($data[0]);
                    if (empty($rawDepartment)) {
                        $deptResult = smartMatchDepartment($classCode, $departments);
                        if ($deptResult['matched']) {
                            $rawDepartment = $deptResult['department'];
                            $corrections[] = "Department: auto-filled as '{$rawDepartment}'";
                        }
                    }
                    
                    // Check for room conflict before adding
                    $hasConflict = false;
                    $conflictWith = null;
                    $conflictDetails = null;
                    
                    if (!empty($room) && !empty($parsedDate)) {
                        $roomCheck = checkRoomAvailability($room, $parsedDate, $parsedStartTime, $parsedEndTime);
                        if (!$roomCheck['available']) {
                            $hasConflict = true;
                            $conflictWith = $roomCheck['conflict']['className'] . ' (' . $roomCheck['conflict']['startTime'] . ' - ' . $roomCheck['conflict']['endTime'] . ')';
                            $conflictDetails = $roomCheck['conflict'];
                            $conflictCount++;
                        }
                    }
                    
                    $formattedDate = $parsedDate ? date('d/m/Y', strtotime($parsedDate)) : 'N/A';
                    $formattedStartTime = date('g:i A', strtotime($parsedStartTime));
                    $formattedEndTime = date('g:i A', strtotime($parsedEndTime));
                    
                    $itemInfo = [
                        'code' => $classCode,
                        'name' => trim($data[1]),
                        'room' => $room,
                        'instructor' => $instructor,
                        'date' => $formattedDate,
                        'time' => $formattedStartTime . ' - ' . $formattedEndTime,
                        'corrections' => $corrections
                    ];
                    
                    // If there's a conflict, DON'T add to database - just track as skipped
                    if ($hasConflict) {
                        $itemInfo['conflictWith'] = $conflictDetails;
                        $conflictList[] = $itemInfo;
                    } else {
                        // No conflict - safe to add
                        $newSchedule = [
                            'classCode' => $classCode,
                            'className' => trim($data[1]),
                            'room' => $room,
                            'instructor' => $instructor,
                            'date' => $parsedDate,
                            'startTime' => $parsedStartTime,
                            'endTime' => $parsedEndTime,
                            'department' => $rawDepartment,
                            'classSize' => intval($data[8] ?? 0),
                            'days' => !empty(trim($data[9] ?? '')) ? explode('/', trim($data[9])) : [],
                            'hasConflict' => false,
                            'conflictWith' => null
                        ];
                        
                        if (addSchedule($newSchedule)) {
                            $importCount++;
                            $successList[] = $itemInfo;
                            
                            if (!empty($corrections)) {
                                $correctionsList[] = [
                                    'code' => $classCode,
                                    'name' => trim($data[1]),
                                    'corrections' => $corrections
                                ];
                            }
                        }
                    }
                }
            }
        } else {
            // Import to session with smart auto-fill
            $maxId = 0;
            foreach ($_SESSION['schedules'] as $schedule) {
                if (isset($schedule['id']) && $schedule['id'] > $maxId) {
                    $maxId = $schedule['id'];
                }
            }
            
            while (($data = fgetcsv($file)) !== false) {
                if (count($data) >= 6 && !empty(trim($data[0]))) {
                    $maxId++;
                    $corrections = [];
                    
                    // Smart parse date
                    $rawDate = trim($data[4]);
                    $dateResult = smartParseDate($rawDate);
                    $parsedDate = $dateResult['parsed'] ?? $rawDate;
                    if ($dateResult['corrected'] && $dateResult['parsed']) {
                        $corrections[] = "Date: '{$rawDate}' → '" . date('d/m/Y', strtotime($parsedDate)) . "'";
                    }
                    
                    // Smart parse times
                    $rawStartTime = trim($data[5]);
                    $rawEndTime = trim($data[6] ?? '');
                    $startTimeResult = smartParseTime($rawStartTime);
                    $endTimeResult = smartParseTime($rawEndTime);
                    $parsedStartTime = $startTimeResult['parsed'];
                    $parsedEndTime = $endTimeResult['parsed'];
                    
                    if ($startTimeResult['corrected']) {
                        $corrections[] = "Start: '{$rawStartTime}' → '" . date('g:i A', strtotime($parsedStartTime)) . "'";
                    }
                    if ($endTimeResult['corrected']) {
                        $corrections[] = "End: '{$rawEndTime}' → '" . date('g:i A', strtotime($parsedEndTime)) . "'";
                    }
                    
                    // Smart match room
                    $rawRoom = trim($data[2]);
                    $roomResult = smartMatchRoom($rawRoom, $roomOptions);
                    $room = $roomResult['room'];
                    if ($roomResult['corrected'] && $roomResult['matched']) {
                        $corrections[] = "Room: '{$rawRoom}' → '{$room}'";
                    }
                    
                    // Smart capitalize instructor name  
                    $rawInstructor = trim($data[3]);
                    $instructorResult = smartCapitalizeName($rawInstructor);
                    $instructor = $instructorResult['formatted'];
                    if ($instructorResult['corrected']) {
                        $corrections[] = "Instructor: '{$rawInstructor}' → '{$instructor}'";
                    }
                    
                    $formattedDate = $parsedDate ? date('d/m/Y', strtotime($parsedDate)) : $rawDate;
                    $formattedStartTime = date('g:i A', strtotime($parsedStartTime));
                    $formattedEndTime = date('g:i A', strtotime($parsedEndTime));
                    
                    $classCode = trim($data[0]);
                    
                    $itemInfo = [
                        'code' => $classCode,
                        'name' => trim($data[1]),
                        'room' => $room,
                        'instructor' => $instructor,
                        'date' => $formattedDate,
                        'time' => $formattedStartTime . ' - ' . $formattedEndTime,
                        'corrections' => $corrections
                    ];
                    
                    // Check for room conflict in session data
                    $hasConflict = false;
                    $conflictDetails = null;
                    
                    if (!empty($room) && !empty($parsedDate)) {
                        // Check against existing session schedules
                        foreach ($_SESSION['schedules'] as $existingSched) {
                            if ($existingSched['room'] === $room && $existingSched['date'] === $parsedDate) {
                                $existingStart = strtotime($existingSched['startTime']);
                                $existingEnd = strtotime($existingSched['endTime']);
                                $newStart = strtotime($parsedStartTime);
                                $newEnd = strtotime($parsedEndTime);
                                
                                // Check time overlap
                                if ($newStart < $existingEnd && $newEnd > $existingStart) {
                                    $hasConflict = true;
                                    $conflictDetails = [
                                        'classCode' => $existingSched['classCode'],
                                        'className' => $existingSched['className'],
                                        'startTime' => date('g:i A', $existingStart),
                                        'endTime' => date('g:i A', $existingEnd),
                                        'instructor' => $existingSched['instructor']
                                    ];
                                    $conflictCount++;
                                    break;
                                }
                            }
                        }
                    }
                    
                    if ($hasConflict) {
                        // Don't add - track as skipped
                        $itemInfo['conflictWith'] = $conflictDetails;
                        $conflictList[] = $itemInfo;
                    } else {
                        // No conflict - safe to add
                        $newSchedule = [
                            'id' => $maxId,
                            'classCode' => htmlspecialchars($classCode),
                            'className' => htmlspecialchars(trim($data[1])),
                            'room' => htmlspecialchars($room),
                            'instructor' => htmlspecialchars($instructor),
                            'date' => htmlspecialchars($parsedDate),
                            'startTime' => htmlspecialchars($parsedStartTime),
                            'endTime' => htmlspecialchars($parsedEndTime)
                        ];
                        $_SESSION['schedules'][] = $newSchedule;
                        $importCount++;
                        $successList[] = $itemInfo;
                        
                        if (!empty($corrections)) {
                            $correctionsList[] = [
                                'code' => $classCode,
                                'name' => trim($data[1]),
                                'corrections' => $corrections
                            ];
                        }
                    }
                }
            }
        }
        fclose($file);
        
        // Store detailed import results in session
        $_SESSION['import_results'] = [
            'total' => $importCount,
            'success_count' => count($successList),
            'conflict_count' => $conflictCount,
            'corrections_count' => count($correctionsList ?? []),
            'success_list' => $successList,
            'conflict_list' => $conflictList,
            'corrections_list' => $correctionsList ?? []
        ];
        
        if ($conflictCount > 0) {
            $_SESSION['warning'] = '⚠️ ' . $conflictCount . ' schedule(s) were SKIPPED due to room conflicts. See details below.';
        }
        $_SESSION['success'] = $importCount . ' schedule(s) imported successfully!' . ($conflictCount > 0 ? ' (' . $conflictCount . ' skipped due to conflicts)' : '');
    } else {
        $_SESSION['error'] = 'Failed to upload file. Please try again.';
    }
    header('Location: ?page=schedule');
    exit();
}

// Handle editing schedule (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_schedule') {
    $editId = intval($_POST['edit_id'] ?? 0);
    
    // Store form data for repopulation
    $formData = [
        'classCode' => $_POST['classCode'] ?? '',
        'className' => $_POST['className'] ?? '',
        'scheduleRoom' => $_POST['scheduleRoom'] ?? '',
        'instructor' => $_POST['instructor'] ?? '',
        'scheduleDate' => $_POST['scheduleDate'] ?? '',
        'startTime' => $_POST['startTime'] ?? '',
        'endTime' => $_POST['endTime'] ?? ''
    ];
    
    // Validate instructor name
    $instructorValidation = isValidInstructorName($_POST['instructor'] ?? '');
    
    if (!$instructorValidation['valid']) {
        $validationError = $instructorValidation['message'];
        $showForm = true;
        $editMode = true;
        $editScheduleId = $editId;
    } else {
        // Validate schedule time (check for AM/PM issues)
        $timeValidation = validateScheduleTime(
            $_POST['scheduleDate'] ?? '',
            $_POST['startTime'] ?? '',
            $_POST['endTime'] ?? ''
        );
        
        if (!$timeValidation['valid']) {
            $validationError = $timeValidation['message'];
            $showForm = true;
            $editMode = true;
            $editScheduleId = $editId;
        } else {
            // Check room availability (exclude current schedule)
            $roomCheck = checkRoomAvailability(
                $_POST['scheduleRoom'] ?? '',
                $_POST['scheduleDate'] ?? '',
                $_POST['startTime'] ?? '',
                $_POST['endTime'] ?? '',
                $editId
            );
            
            if (!$roomCheck['available']) {
                $conflict = $roomCheck['conflict'];
                $validationError = "Room not available! Already booked for \"{$conflict['classCode']} - {$conflict['className']}\" from {$conflict['startTime']} to {$conflict['endTime']} by {$conflict['instructor']}.";
                $showForm = true;
                $editMode = true;
                $editScheduleId = $editId;
            } else {
                // Validation passed, proceed with update
                if ($useDatabase) {
                    $updateData = [
                        'classCode' => htmlspecialchars($_POST['classCode'] ?? ''),
                        'className' => htmlspecialchars($_POST['className'] ?? ''),
                        'room' => htmlspecialchars($_POST['scheduleRoom'] ?? ''),
                    'instructor' => htmlspecialchars($_POST['instructor'] ?? ''),
                    'date' => htmlspecialchars($_POST['scheduleDate'] ?? ''),
                    'startTime' => htmlspecialchars($_POST['startTime'] ?? ''),
                    'endTime' => htmlspecialchars($_POST['endTime'] ?? ''),
                    'department' => htmlspecialchars($_POST['department'] ?? ''),
                    'classSize' => intval($_POST['classSize'] ?? 0),
                    'days' => isset($_POST['days']) ? $_POST['days'] : []
                ];
                if (updateSchedule($editId, $updateData)) {
                    $_SESSION['success'] = 'Schedule updated successfully!';
                } else {
                    $_SESSION['error'] = 'Failed to update schedule.';
                }
            } else {
                // Update in session
                foreach ($_SESSION['schedules'] as &$schedule) {
                    if ($schedule['id'] === $editId) {
                        $schedule['classCode'] = htmlspecialchars($_POST['classCode'] ?? '');
                        $schedule['className'] = htmlspecialchars($_POST['className'] ?? '');
                        $schedule['room'] = htmlspecialchars($_POST['scheduleRoom'] ?? '');
                        $schedule['instructor'] = htmlspecialchars($_POST['instructor'] ?? '');
                        $schedule['date'] = htmlspecialchars($_POST['scheduleDate'] ?? '');
                        $schedule['startTime'] = htmlspecialchars($_POST['startTime'] ?? '');
                        $schedule['endTime'] = htmlspecialchars($_POST['endTime'] ?? '');
                        break;
                    }
                }
                unset($schedule);
                $_SESSION['success'] = 'Schedule updated successfully!';
            }
            header('Location: ?page=schedule');
            exit();
            }
        }
    }
}

// Check if editing existing schedule (GET)
$editScheduleData = null;

if (isset($_GET['edit_schedule']) && !$showForm) {
    $editScheduleId = intval($_GET['edit_schedule']);
    
    if ($useDatabase) {
        $editScheduleData = getScheduleById($editScheduleId);
    } else {
        foreach ($_SESSION['schedules'] ?? [] as $schedule) {
            if ($schedule['id'] === $editScheduleId) {
                $editScheduleData = $schedule;
                break;
            }
        }
    }
    
    if ($editScheduleData) {
        $editMode = true;
        $showForm = true;
        $formData = [
            'classCode' => $editScheduleData['classCode'] ?? '',
            'className' => $editScheduleData['className'] ?? '',
            'scheduleRoom' => $editScheduleData['room'] ?? '',
            'instructor' => $editScheduleData['instructor'] ?? '',
            'scheduleDate' => $editScheduleData['date'] ?? '',
            'startTime' => $editScheduleData['startTime'] ?? '',
            'endTime' => $editScheduleData['endTime'] ?? ''
        ];
    }
}

// CSV export is handled in index.php before HTML output

// Get schedules from appropriate source
$schedules = $useDatabase ? getAllSchedules() : ($_SESSION['schedules'] ?? []);
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
$warning = $_SESSION['warning'] ?? '';
$importResults = $_SESSION['import_results'] ?? null;
if (isset($_SESSION['success'])) unset($_SESSION['success']);
if (isset($_SESSION['error'])) unset($_SESSION['error']);
if (isset($_SESSION['warning'])) unset($_SESSION['warning']);
if (isset($_SESSION['import_results'])) unset($_SESSION['import_results']);

// Filter out past schedules (where date+endTime has passed)
// Keep schedules with no date (recurring) or future dates
$currentDateTime = time();
$schedules = array_filter($schedules, function($schedule) use ($currentDateTime) {
    // If no date is set, keep the schedule (it's recurring)
    if (empty($schedule['date'])) {
        return true;
    }
    $scheduleEndDateTime = strtotime($schedule['date'] . ' ' . ($schedule['endTime'] ?? '23:59'));
    return $scheduleEndDateTime !== false && $scheduleEndDateTime > $currentDateTime;
});

// Sort schedules by date and time (schedules without dates go to the end)
usort($schedules, function($a, $b) {
    $dateA = !empty($a['date']) ? $a['date'] : '9999-12-31';
    $dateB = !empty($b['date']) ? $b['date'] : '9999-12-31';
    $timeA = strtotime($dateA . ' ' . ($a['startTime'] ?? '00:00'));
    $timeB = strtotime($dateB . ' ' . ($b['startTime'] ?? '00:00'));
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

        <?php if (!empty($error)): ?>
            <div class="error-message" style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($warning)): ?>
            <div class="warning-message" style="background: #fff3cd; color: #856404; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #ffc107;">
                <?php echo $warning; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($importResults)): ?>
            <!-- Detailed Import Summary -->
            <div class="import-summary" style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                <h3 style="margin: 0 0 15px 0; color: #333; font-size: 18px;">📋 Import Summary</h3>
                
                <div style="display: flex; gap: 15px; margin-bottom: 15px; flex-wrap: wrap;">
                    <div style="background: #d4edda; padding: 12px 20px; border-radius: 6px; flex: 1; min-width: 120px;">
                        <div style="font-size: 24px; font-weight: bold; color: #155724;"><?php echo $importResults['success_count']; ?></div>
                        <div style="color: #155724; font-size: 13px;">✅ Successfully Added</div>
                    </div>
                    <div style="background: #f8d7da; padding: 12px 20px; border-radius: 6px; flex: 1; min-width: 120px;">
                        <div style="font-size: 24px; font-weight: bold; color: #721c24;"><?php echo $importResults['conflict_count']; ?></div>
                        <div style="color: #721c24; font-size: 13px;">❌ Skipped (Conflicts)</div>
                    </div>
                    <div style="background: #cce5ff; padding: 12px 20px; border-radius: 6px; flex: 1; min-width: 120px;">
                        <div style="font-size: 24px; font-weight: bold; color: #004085;"><?php echo $importResults['total']; ?></div>
                        <div style="color: #004085; font-size: 13px;">📊 Total Imported</div>
                    </div>
                </div>
                
                <?php if (!empty($importResults['conflict_list'])): ?>
                <details style="margin-bottom: 15px;">
                    <summary style="cursor: pointer; font-weight: bold; color: #dc3545; padding: 10px; background: #fff5f5; border-radius: 6px;">
                        ❌ Skipped Due to Conflicts (<?php echo count($importResults['conflict_list']); ?>) - These were NOT added
                    </summary>
                    <div style="max-height: 300px; overflow-y: auto; margin-top: 10px;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                            <thead>
                                <tr style="background: #f8d7da;">
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Code</th>
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Class Name</th>
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Room</th>
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Date</th>
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Time</th>
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Conflicts With</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($importResults['conflict_list'] as $conflict): ?>
                                <tr style="background: #fff5f5;">
                                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($conflict['code']); ?></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($conflict['name']); ?></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($conflict['room']); ?></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($conflict['date']); ?></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($conflict['time']); ?></td>
                                    <td style="padding: 8px; border: 1px solid #ddd; color: #dc3545;">
                                        <strong><?php echo htmlspecialchars($conflict['conflictWith']['className'] ?? 'Unknown'); ?></strong><br>
                                        <small><?php echo htmlspecialchars(($conflict['conflictWith']['classCode'] ?? '') . ' - ' . ($conflict['conflictWith']['startTime'] ?? '') . ' to ' . ($conflict['conflictWith']['endTime'] ?? '')); ?></small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </details>
                <?php endif; ?>
                
                <?php if (!empty($importResults['success_list'])): ?>
                <details>
                    <summary style="cursor: pointer; font-weight: bold; color: #155724; padding: 10px; background: #f0fff0; border-radius: 6px;">
                        ✅ Successfully Added (<?php echo count($importResults['success_list']); ?>) - Click to expand/collapse
                    </summary>
                    <div style="max-height: 300px; overflow-y: auto; margin-top: 10px;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                            <thead>
                                <tr style="background: #d4edda;">
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Code</th>
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Class Name</th>
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Room</th>
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Instructor</th>
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Date</th>
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($importResults['success_list'] as $item): ?>
                                <tr>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($item['code']); ?></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($item['room']); ?></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($item['instructor']); ?></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($item['date']); ?></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($item['time']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </details>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="schedule-toolbar">
            <button class="btn-primary" onclick="toggleScheduleForm()">+ Add Schedule</button>
            <button class="btn-secondary" onclick="toggleCSVImport()">📥 Import CSV</button>
            <a href="?export_csv=true" class="btn-secondary">📤 Export CSV</a>
            <a href="?download_template=true" class="btn-secondary">📄 Download Template</a>
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
            <p class="subtitle">CSV format: Class Code, Class Name, Room, Instructor, Date (YYYY-MM-DD), Start Time (HH:MM), End Time (HH:MM)</p>
            <form method="POST" enctype="multipart/form-data" id="csvImportForm">
                <input type="hidden" name="action" value="import_csv">
                <div class="upload-area" id="uploadArea" onclick="document.getElementById('csvFile').click()" ondrop="handleDrop(event)" ondragover="allowDrop(event)" ondragleave="handleDragLeave(event)">
                    <p id="uploadText">📁 Drag and drop CSV file here or click to select</p>
                    <input type="file" id="csvFile" name="csvFile" accept=".csv" onchange="handleCSVUpload(event)" style="display: none;">
                </div>
                <div id="csvPreview" style="display: none; margin-top: 15px; max-height: 200px; overflow-y: auto;"></div>
                <div style="margin-top: 20px;">
                    <button type="submit" class="btn-primary" id="importBtn" disabled>Import Data</button>
                    <button type="button" class="btn-secondary" onclick="toggleCSVImport()">Cancel</button>
                </div>
                <div id="csvError" class="error-message" style="margin-top: 10px;"></div>
            </form>
        </div>

        <!-- Add/Edit Schedule Form -->
        <div id="addScheduleForm" class="card form-card" style="display: <?php echo $showForm ? 'block' : 'none'; ?>; margin-top: 20px;">
            <h3><?php echo $editMode ? 'Edit Schedule' : 'Add New Schedule'; ?></h3>
            <?php if (!empty($validationError)): ?>
            <div class="warning-message" style="background: #fff3cd; border: 1px solid #ffc107; color: #856404; padding: 12px 16px; border-radius: 6px; margin-bottom: 16px;">
                ⚠️ <?php echo $validationError; ?>
            </div>
            <?php endif; ?>
            <form method="POST" action="?page=schedule">
                <input type="hidden" name="action" value="<?php echo $editMode ? 'edit_schedule' : 'add_schedule'; ?>">
                <?php if ($editMode): ?>
                <input type="hidden" name="edit_id" value="<?php echo $editScheduleId; ?>">
                <?php endif; ?>
                
                <!-- Class Information -->
                <div class="form-section" style="margin-bottom: 20px;">
                    <h4 style="color: #555; margin-bottom: 12px; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Class Information</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Class Code</label>
                            <input type="text" name="classCode" required placeholder="e.g., CIT18, CC3" value="<?php echo htmlspecialchars($formData['classCode']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Class Name</label>
                            <input type="text" name="className" required placeholder="e.g., Introduction to OOP, Mastery of Web" value="<?php echo htmlspecialchars($formData['className']); ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Instructor</label>
                            <input type="text" name="instructor" required placeholder="e.g., Dr. Maria Santos" value="<?php echo htmlspecialchars($formData['instructor']); ?>">
                            <small style="color: #666; font-size: 12px;">Enter full name (first and last name)</small>
                        </div>
                    </div>
                </div>
                
                <!-- Schedule Time (Before Room Selection) -->
                <div class="form-section" style="margin-bottom: 20px; padding: 16px; background: #f8f9fa; border-radius: 8px;">
                    <h4 style="color: #555; margin-bottom: 12px; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">📅 Schedule Date & Time</h4>
                    <p style="color: #666; font-size: 13px; margin-bottom: 12px;">Select date and time first to check room availability</p>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="scheduleDate" required value="<?php echo htmlspecialchars($formData['scheduleDate']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Start Time</label>
                            <input type="time" name="startTime" required value="<?php echo htmlspecialchars($formData['startTime']); ?>">
                        </div>
                        <div class="form-group">
                            <label>End Time</label>
                            <input type="time" name="endTime" required value="<?php echo htmlspecialchars($formData['endTime']); ?>">
                        </div>
                    </div>
                </div>
                
                <!-- Room Selection (After Time) -->
                <div class="form-section" style="margin-bottom: 20px;">
                    <h4 style="color: #555; margin-bottom: 12px; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">🏫 Room Assignment</h4>
                    <p style="color: #666; font-size: 13px; margin-bottom: 12px;">Select date and time first, then search or select an available room</p>
                    <div class="form-row">
                        <div class="form-group" style="position: relative;">
                            <label>Room</label>
                            <input type="text" name="scheduleRoom" id="roomSearchInput" required 
                                   placeholder="-- Select date & time first --"
                                   value="<?php echo htmlspecialchars($formData['scheduleRoom']); ?>"
                                   autocomplete="off"
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                            <div id="roomDropdown" style="display: none; position: absolute; z-index: 1000; background: white; border: 1px solid #ddd; border-top: none; border-radius: 0 0 6px 6px; max-height: 300px; overflow-y: auto; width: 100%; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"></div>
                            <div id="roomAvailabilityStatus" style="margin-top: 8px; font-size: 13px;"></div>
                            <small style="color: #666; font-size: 12px;">Type to search rooms - unavailable rooms shown first for reference</small>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-primary"><?php echo $editMode ? 'Update Schedule' : 'Add Schedule'; ?></button>
                    <button type="button" class="btn-secondary" onclick="<?php echo $editMode ? "window.location.href='?page=schedule'" : 'toggleScheduleForm()'; ?>">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Schedule Calendar View -->
        <div id="scheduleView" style="margin-top: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="color: #333; font-size: 18px; margin: 0;">Upcoming Classes</h3>
                <?php if (!empty($schedules)): ?>
                <a href="?page=schedule&clear_all_schedules=confirm" class="btn-small btn-danger" 
                   onclick="return confirm('Are you sure you want to delete ALL schedules? This action cannot be undone!')">Clear All</a>
                <?php endif; ?>
            </div>
            <div class="schedule-list">
                <?php if (empty($schedules)): ?>
                    <p style="text-align: center; color: #999; grid-column: 1 / -1;">No schedules available</p>
                <?php else: ?>
                    <?php foreach ($schedules as $schedule): 
                        // Format times to 12-hour AM/PM format
                        $startFormatted = date('g:i A', strtotime($schedule['startTime']));
                        $endFormatted = date('g:i A', strtotime($schedule['endTime']));
                        $hasConflict = !empty($schedule['hasConflict']);
                        $conflictStyle = $hasConflict ? 'border: 2px solid #dc3545; background: #fff5f5;' : '';
                    ?>
                        <article class="schedule-item<?php echo $hasConflict ? ' has-conflict' : ''; ?>" style="<?php echo $conflictStyle; ?>">
                            <?php if ($hasConflict): ?>
                            <div class="conflict-warning" style="background: #dc3545; color: white; padding: 6px 10px; font-size: 12px; border-radius: 4px 4px 0 0; margin: -12px -12px 10px -12px;">
                                ⚠️ CONFLICT: <?php echo htmlspecialchars($schedule['conflictWith'] ?? 'Room already booked'); ?>
                            </div>
                            <?php endif; ?>
                            <div class="schedule-header">
                                <div class="schedule-time">
                                    <p class="time-slot"><?php echo $startFormatted . ' - ' . $endFormatted; ?></p>
                                </div>
                                <div class="schedule-actions">
                                    <button class="btn-small" onclick="editSchedule(<?php echo $schedule['id']; ?>)" aria-label="Edit schedule">Edit</button>
                                    <a href="?page=schedule&delete_schedule=<?php echo $schedule['id']; ?>" class="btn-small btn-danger" onclick="return confirm('Are you sure you want to delete this schedule?')" aria-label="Delete schedule">Delete</a>
                                </div>
                            </div>
                                        <div class="schedule-details">
                                <h4 class="schedule-title"><?php echo htmlspecialchars($schedule['className']); ?></h4>
                                <?php 
                                    // Get room building/floor info
                                    $roomBuildingInfo = '';
                                    $roomData = null;
                                    
                                    // Try to find room by roomId first
                                    if (!empty($schedule['roomId'])) {
                                        $roomData = getRoomById($schedule['roomId']);
                                    }
                                    
                                    // If no roomData yet, try to find by matching room display name
                                    if (!$roomData && !empty($schedule['room'])) {
                                        $allRoomOptions = getRoomsAsOptions();
                                        foreach ($allRoomOptions as $roomOpt) {
                                            if ($roomOpt['label'] === $schedule['room']) {
                                                $roomData = getRoomById($roomOpt['value']);
                                                break;
                                            }
                                        }
                                    }
                                    
                                    if ($roomData) {
                                        $buildingData = getBuildingById($roomData['building']);
                                        $buildingName = $buildingData ? $buildingData['fullName'] : ($roomData['building'] ?? 'Unknown');
                                        $floorNum = $roomData['floor'] ?? 1;
                                        $floorSuffix = ($floorNum == 1 ? 'st' : ($floorNum == 2 ? 'nd' : ($floorNum == 3 ? 'rd' : 'th')));
                                        $roomBuildingInfo = $buildingName . ' - ' . $floorNum . $floorSuffix . ' Floor';
                                    }
                                ?>
                                <div class="schedule-meta">
                                    <span><strong>Code:</strong> <?php echo htmlspecialchars($schedule['classCode'] ?? 'N/A'); ?></span>
                                    <span><strong>Room:</strong> <?php echo htmlspecialchars($schedule['room']); ?></span>
                                    <?php if (!empty($roomBuildingInfo)): ?>
                                    <span><strong>Location:</strong> <?php echo htmlspecialchars($roomBuildingInfo); ?></span>
                                    <?php endif; ?>
                                    <span><strong>Instructor:</strong> <?php echo htmlspecialchars($schedule['instructor']); ?></span>
                                    <span><strong>Date:</strong> <?php echo date('d/m/Y', strtotime($schedule['date'])); ?></span>
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
    if (section.style.display === 'none') {
        section.style.display = 'block';
    } else {
        section.style.display = 'none';
        // Reset the form when closing
        resetCSVImport();
    }
}

function resetCSVImport() {
    document.getElementById('csvFile').value = '';
    document.getElementById('csvPreview').style.display = 'none';
    document.getElementById('csvPreview').innerHTML = '';
    document.getElementById('csvError').textContent = '';
    document.getElementById('uploadText').textContent = '📁 Drag and drop CSV file here or click to select';
    document.getElementById('importBtn').disabled = true;
    document.getElementById('uploadArea').style.background = '';
}

function editSchedule(id) {
    window.location.href = '?page=schedule&edit_schedule=' + id;
}

function filterSchedule(filter) {
    alert('Filtering by: ' + filter);
}

function allowDrop(event) {
    event.preventDefault();
    event.stopPropagation();
    document.getElementById('uploadArea').style.background = '#e8eef7';
    document.getElementById('uploadArea').style.borderColor = '#3498db';
}

function handleDragLeave(event) {
    event.preventDefault();
    event.stopPropagation();
    document.getElementById('uploadArea').style.background = '';
    document.getElementById('uploadArea').style.borderColor = '';
}

function handleDrop(event) {
    event.preventDefault();
    event.stopPropagation();
    document.getElementById('uploadArea').style.background = '';
    document.getElementById('uploadArea').style.borderColor = '';
    
    const files = event.dataTransfer.files;
    if (files.length > 0) {
        const fileInput = document.getElementById('csvFile');
        fileInput.files = files;
        processCSVFile(files[0]);
    }
}

function handleCSVUpload(event) {
    const files = event.target.files;
    if (files.length > 0) {
        processCSVFile(files[0]);
    }
}

function processCSVFile(file) {
    const errorDiv = document.getElementById('csvError');
    const previewDiv = document.getElementById('csvPreview');
    const uploadText = document.getElementById('uploadText');
    const importBtn = document.getElementById('importBtn');
    
    // Validate file type
    if (!file.name.toLowerCase().endsWith('.csv')) {
        errorDiv.textContent = 'Please select a valid CSV file.';
        errorDiv.style.color = '#dc3545';
        importBtn.disabled = true;
        return;
    }
    
    // Update UI to show file selected
    uploadText.textContent = '📄 ' + file.name + ' (' + formatFileSize(file.size) + ')';
    errorDiv.textContent = '';
    
    // Read and preview CSV
    const reader = new FileReader();
    reader.onload = function(e) {
        const content = e.target.result;
        const lines = content.split('\n').filter(line => line.trim());
        
        if (lines.length < 2) {
            errorDiv.textContent = 'CSV file appears to be empty or has no data rows.';
            errorDiv.style.color = '#dc3545';
            importBtn.disabled = true;
            return;
        }
        
        // Create preview table
        let html = '<table style="width:100%; border-collapse: collapse; font-size: 13px;">';
        html += '<thead><tr style="background: #f8f9fa;">';
        
        const headers = parseCSVLine(lines[0]);
        headers.forEach(h => {
            html += '<th style="padding: 8px; border: 1px solid #ddd; text-align: left;">' + escapeHtml(h) + '</th>';
        });
        html += '</tr></thead><tbody>';
        
        // Show up to 5 data rows
        const maxPreview = Math.min(lines.length, 6);
        let validRows = 0;
        for (let i = 1; i < maxPreview; i++) {
            const cols = parseCSVLine(lines[i]);
            if (cols.length >= 6 && cols[0].trim()) {
                validRows++;
                html += '<tr>';
                cols.forEach(c => {
                    html += '<td style="padding: 8px; border: 1px solid #ddd;">' + escapeHtml(c) + '</td>';
                });
                html += '</tr>';
            }
        }
        html += '</tbody></table>';
        
        const totalDataRows = lines.length - 1;
        html += '<p style="margin-top: 10px; color: #666; font-size: 13px;">Showing preview of ' + validRows + ' row(s). Total rows to import: ' + totalDataRows + '</p>';
        
        previewDiv.innerHTML = html;
        previewDiv.style.display = 'block';
        
        if (validRows > 0) {
            importBtn.disabled = false;
            errorDiv.textContent = 'Ready to import ' + totalDataRows + ' schedule(s).';
            errorDiv.style.color = '#28a745';
        } else {
            errorDiv.textContent = 'No valid data rows found. Check your CSV format.';
            errorDiv.style.color = '#dc3545';
            importBtn.disabled = true;
        }
    };
    reader.readAsText(file);
}

function parseCSVLine(line) {
    const result = [];
    let current = '';
    let inQuotes = false;
    
    for (let i = 0; i < line.length; i++) {
        const char = line[i];
        if (char === '"') {
            inQuotes = !inQuotes;
        } else if (char === ',' && !inQuotes) {
            result.push(current.trim());
            current = '';
        } else {
            current += char;
        }
    }
    result.push(current.trim());
    return result;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

// ===== ROOM AVAILABILITY CHECKING =====
const selectedRoom = <?php echo json_encode($formData['scheduleRoom']); ?>;
const editScheduleId = <?php echo $editMode ? $editScheduleId : 'null'; ?>;

let allRooms = [];
let availableRoomLabels = [];

function getFloorSuffix(n) {
    if (n === 1) return 'st';
    if (n === 2) return 'nd';
    if (n === 3) return 'rd';
    return 'th';
}

function checkRoomAvailability() {
    const dateInput = document.querySelector('input[name="scheduleDate"]');
    const startTimeInput = document.querySelector('input[name="startTime"]');
    const endTimeInput = document.querySelector('input[name="endTime"]');
    const roomInput = document.getElementById('roomSearchInput');
    const statusDiv = document.getElementById('roomAvailabilityStatus');
    const dropdown = document.getElementById('roomDropdown');
    
    const date = dateInput?.value || '';
    const startTime = startTimeInput?.value || '';
    const endTime = endTimeInput?.value || '';
    
    // Reset room input if date/time not complete
    if (!date || !startTime || !endTime) {
        roomInput.placeholder = '-- Select date & time first --';
        roomInput.disabled = true;
        allRooms = [];
        availableRoomLabels = [];
        statusDiv.innerHTML = '<span style="color: #666;">📅 Please select date, start time, and end time to see available rooms</span>';
        return;
    }
    
    // Validate end time is after start time
    if (endTime <= startTime) {
        statusDiv.innerHTML = '<span style="color: #dc3545;">⚠️ End time must be after start time</span>';
        return;
    }
    
    // Show loading state
    roomInput.placeholder = 'Loading rooms...';
    roomInput.disabled = true;
    statusDiv.innerHTML = '<span style="color: #666;">🔄 Checking room availability...</span>';
    
    // Build URL with parameters
    let url = `?check_room_availability=1&date=${encodeURIComponent(date)}&start_time=${encodeURIComponent(startTime)}&end_time=${encodeURIComponent(endTime)}`;
    if (editScheduleId) {
        url += `&exclude_id=${editScheduleId}`;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            allRooms = data.rooms || [];
            const availableRooms = allRooms.filter(r => r.available);
            const unavailableRooms = allRooms.filter(r => !r.available);
            availableRoomLabels = availableRooms.map(r => r.label);
            
            // Enable input
            roomInput.disabled = false;
            roomInput.placeholder = 'Type to search rooms (e.g., M303, Computer Lab)...';
            
            // Update status
            statusDiv.innerHTML = `<span style="color: #dc3545;">❌ ${unavailableRooms.length} room(s) occupied</span>` +
                ` <span style="color: #28a745; margin-left: 10px;">✅ ${availableRooms.length} room(s) available</span>`;
            
            // If editing and selected room exists, validate it
            if (selectedRoom && roomInput.value === selectedRoom) {
                validateRoomInput();
            }
        })
        .catch(error => {
            console.error('Error checking room availability:', error);
            statusDiv.innerHTML = '<span style="color: #dc3545;">⚠️ Error checking availability. Please try again.</span>';
            roomInput.placeholder = '-- Error loading rooms --';
            roomInput.disabled = true;
        });
}

function showRoomDropdown() {
    const roomInput = document.getElementById('roomSearchInput');
    const dropdown = document.getElementById('roomDropdown');
    const searchTerm = roomInput.value.toLowerCase();
    
    if (allRooms.length === 0) {
        dropdown.style.display = 'none';
        return;
    }
    
    // Filter rooms based on search
    const unavailableRooms = allRooms.filter(r => !r.available && r.label.toLowerCase().includes(searchTerm));
    const availableRooms = allRooms.filter(r => r.available && r.label.toLowerCase().includes(searchTerm));
    
    let html = '';
    
    // Show unavailable rooms FIRST (on top)
    if (unavailableRooms.length > 0) {
        html += '<div style="padding: 6px 12px; background: #fdf0f0; color: #dc3545; font-weight: bold; font-size: 12px;">❌ Unavailable Rooms (for reference)</div>';
        unavailableRooms.forEach(room => {
            const conflictText = room.conflict ? `Booked: ${room.conflict.classCode} (${room.conflict.startTime} - ${room.conflict.endTime})` : 'Occupied';
            const locationInfo = room.building ? `${room.building} - ${room.floor}${getFloorSuffix(room.floor)} Floor` : '';
            html += `<div style="padding: 10px 12px; background: #f9f9f9; color: #999; border-bottom: 1px solid #eee; cursor: not-allowed;">
                <span>❌</span> <strong>${escapeHtml(room.label)}</strong><br>
                <small style="color: #666;">📍 ${escapeHtml(locationInfo)}</small><br>
                <small style="color: #dc3545;">🚫 ${escapeHtml(conflictText)}</small>
            </div>`;
        });
    }
    
    // Show available rooms after
    if (availableRooms.length > 0) {
        html += '<div style="padding: 6px 12px; background: #f0f9f0; color: #28a745; font-weight: bold; font-size: 12px;">✅ Available Rooms (click to select)</div>';
        availableRooms.forEach(room => {
            const locationInfo = room.building ? `${room.building} - ${room.floor}${getFloorSuffix(room.floor)} Floor` : '';
            html += `<div class="room-option" data-value="${escapeHtml(room.label)}" style="padding: 10px 12px; cursor: pointer; border-bottom: 1px solid #eee; background: white;" onmouseover="this.style.background='#e8f4fd'" onmouseout="this.style.background='white'">
                <span style="color: #28a745;">✅</span> <strong>${escapeHtml(room.label)}</strong><br>
                <small style="color: #666;">📍 ${escapeHtml(locationInfo)}</small>
            </div>`;
        });
    }
    
    if (html === '') {
        html = '<div style="padding: 12px; color: #666; text-align: center;">No rooms found matching your search</div>';
    }
    
    dropdown.innerHTML = html;
    dropdown.style.display = 'block';
    
    // Add click handlers to available room options
    dropdown.querySelectorAll('.room-option').forEach(opt => {
        opt.addEventListener('click', function() {
            roomInput.value = this.dataset.value;
            dropdown.style.display = 'none';
            validateRoomInput();
        });
    });
}

function validateRoomInput() {
    const roomInput = document.getElementById('roomSearchInput');
    const value = roomInput.value;
    
    if (availableRoomLabels.includes(value)) {
        roomInput.style.borderColor = '#28a745';
        roomInput.setCustomValidity('');
    } else if (value) {
        roomInput.style.borderColor = '#dc3545';
        roomInput.setCustomValidity('Please select a valid available room from the list');
    } else {
        roomInput.style.borderColor = '#ddd';
        roomInput.setCustomValidity('Please select a room');
    }
}

// Add event listeners to date/time inputs
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.querySelector('input[name="scheduleDate"]');
    const startTimeInput = document.querySelector('input[name="startTime"]');
    const endTimeInput = document.querySelector('input[name="endTime"]');
    const roomInput = document.getElementById('roomSearchInput');
    const dropdown = document.getElementById('roomDropdown');
    
    if (dateInput) dateInput.addEventListener('change', checkRoomAvailability);
    if (startTimeInput) startTimeInput.addEventListener('change', checkRoomAvailability);
    if (endTimeInput) endTimeInput.addEventListener('change', checkRoomAvailability);
    
    if (roomInput) {
        roomInput.addEventListener('focus', showRoomDropdown);
        roomInput.addEventListener('input', function() {
            showRoomDropdown();
            validateRoomInput();
        });
        roomInput.addEventListener('blur', function() {
            // Delay hiding to allow click on dropdown
            setTimeout(() => {
                dropdown.style.display = 'none';
                validateRoomInput();
            }, 200);
        });
    }
    
    // Check availability on page load if editing or form has values
    if (dateInput?.value && startTimeInput?.value && endTimeInput?.value) {
        checkRoomAvailability();
    }
});
</script>
