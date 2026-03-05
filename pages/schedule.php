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

// =============== BSIT WEB TECHNOLOGY CURRICULUM COURSES ===============
// =============== CURRICULUM COURSES BY YEAR LEVEL AND SEMESTER ===============
$coursesByYearAndSem = [
    '1st Year' => [
        '1st Term' => [
            'CC1' => 'Computing Fundamentals',
            'CC2' => 'Introduction to Computer Programming',
            'CC7' => 'Human Computer Interaction',
            'Engl 100' => 'Purposive Communication',
            'Hist 100' => 'Readings in Philippine History',
            'Math 100' => 'Mathematics in the Modern World',
            'NSTP 1' => 'National Service Training Program 1',
            'PATHFit 1' => 'Movement Competency Training',
        ],
        '2nd Term' => [
            'CC3' => 'Object Oriented Programming',
            'CC4' => 'Data Structures and Algorithms',
            'CC8' => 'Introduction to Statistical Methods',
            'CC21' => 'Introduction to ERP',
            'Psych 100' => 'Understanding the Self',
            'Techno 100' => 'Technopreneurship',
            'PATHFit 2' => 'Exercise-based Fitness Activities',
        ],
        '3rd Term' => [
            'CC9' => 'Discrete Structures',
            'CC10' => 'Introduction to Networks',
            'CC11' => 'Communication in the Workplace',
            'CC12' => 'Statistical Design and Analysis',
            'CC13' => 'Systems Analysis and Design',
            'CC22' => 'Introduction to Platform Technologies',
            'NSTP 2' => 'National Service Training Program 2',
            'PATHFit 3' => 'Martial Arts',
        ],
    ],
    '2nd Year' => [
        '1st Term' => [
            'CC5' => 'Information Management',
            'CIT1' => 'Quantitative Analysis',
            'CIT5' => 'Accounting Essentials',
            'CIT14' => 'Web Technologies',
            'CIT24' => 'Switching, Routing and Wireless Essentials',
            'Soc Sci 101N' => 'Ethics',
            'PATHFit 4' => 'Outdoor and Adventure Activities',
        ],
        '2nd Term' => [
            'CC14' => 'Web Application Development',
            'CC18' => 'Social and Professional Issues',
            'CC19' => 'Data Mining',
            'CIT3' => 'IT Project Management',
            'CIT15' => 'Multimedia Systems',
            'CORDI 101' => 'Cordilleras: History and Socio-Cultural Heritage',
            'Science 100' => 'Science, Technology and Society',
        ],
        '3rd Term' => [
            'CC15' => 'Systems Integration and Architecture',
            'CC16' => 'IT Security',
            'CIT4' => 'Introduction to Integrative Programming and Technologies',
            'CIT16' => 'IT Technopreneurship',
            'FL 100' => 'Foreign Culture and Language',
            'Soc Sci 100' => 'Art Appreciation',
        ],
    ],
    '3rd Year' => [
        '1st Term' => [
            'CC6' => 'Emerging Technologies in IT',
            'CC17' => 'Mobile Application Design and Development',
            'CIT6' => 'Capstone Project 1',
            'CIT17' => 'Web Information System',
            'Soc Sci 103N' => 'The Contemporary World',
        ],
        '2nd Term' => [
            'CIT7' => 'Capstone Project 2',
            'CIT18' => 'Mastery in Web Technology',
            'Hist 101' => 'The Life and Works of Rizal',
        ],
        '3rd Term' => [
            'CIT8' => 'IT Internship',
        ],
    ],
];

// Build coursesByYear for backward compatibility (flat by year)
$coursesByYear = [];
foreach ($coursesByYearAndSem as $year => $semesters) {
    $coursesByYear[$year] = [];
    foreach ($semesters as $sem => $courses) {
        foreach ($courses as $code => $name) {
            $coursesByYear[$year][$code] = $name;
        }
    }
}

// Flat list for lookups (auto-fill, JS, etc.)
$curriculumCourses = [];
foreach ($coursesByYear as $year => $courses) {
    foreach ($courses as $code => $name) {
        $curriculumCourses[$code] = $name;
    }
}

// Build reverse lookup: course code => year level
$courseYearMap = [];
foreach ($coursesByYear as $year => $courses) {
    foreach ($courses as $code => $name) {
        $courseYearMap[$code] = $year;
    }
}

// =============== FIXED TIME SLOTS ===============
$fixedTimeSlots = [
    '07:30' => '08:50',
    '08:50' => '10:10',
    '10:10' => '11:30',
    '11:30' => '12:50',
    '12:50' => '14:10',
    '14:10' => '15:30',
    '15:30' => '16:50',
    '16:50' => '18:10',
    '18:10' => '19:30',
];

// =============== FACULTY INSTRUCTORS ===============
$instructorsList = [
    // Full-time Faculty Members
    'ALMAREZ, Joshua Carbie D.' => 'BSIT',
    'ASPIRAS, Laurie Lynne F.' => 'BSCS',
    'BALAY-ODAO, Gem P.' => 'BSCS',
    'BAYANI, Eugene Frank G.' => 'BSCS, MCGA, MDM',
    'BENINSIG, Melinda A.' => 'BSEd Math, MIT, MA Math',
    'BERNALDEZ, Razielle Jeyna Mae F.' => 'BSCS',
    'BUSAING, Kayralyn P.' => 'BSCS',
    'CATALA, Paul Terese L.' => 'BSIT',
    'CONCEPCION, Natividad B.' => 'BSICS, MCS, DIT',
    'DELA CRUZ, Jhunfel S.' => 'BSIT',
    'ELEGADO, Thea Melaine C.' => 'BSFA',
    'FAGYAN, Zyra Yell A.' => 'BSIT',
    'FLORES, Hans Harold L.' => 'BSIT',
    'FORYASEN, Zen Lee D.' => 'BSCS',
    'FRONDA, Roma Joy D.' => 'BS Math, MS Math',
    'GAMA, Adrianne Nicole M.' => 'BSFA',
    'GAYYED, Arnemie B.' => 'BSIT',
    'GONZALES, Christine T.' => 'BSIT',
    'HANBAL, Ibrahim F.' => 'BSCS, MIT',
    'JACINTO, Philip Irving G.' => 'BSIT, MIT',
    'MALABANAN, Don Harl C.' => 'BSCS, MSCS',
    'MANG-USAN, Walter L.' => 'BSCS, MIT',
    'MARTIREZ, Jessie D.' => 'BSIT',
    'MAYNIGO, Janelle P.' => 'BSIT',
    'MEING, Luis William C.' => 'BSIT, MSCS',
    'MIRADOR, Aldith Faith P.' => 'BSIT',
    'MOLTIO, Bretz Harllynne M.' => 'BSIT',
    'NICOLAS, Venn Edward P.' => 'BSIT',
    'ORTIZ, Marie Grace V.' => 'BS Math, MIT, MAEd Math',
    'PANGAN, Efraim Jededia Z.' => 'BSCS',
    'PATI, Felipe Jr. D.' => 'BSECE, MCS, PhD Mgmt',
    'PERALTA, Joan M.' => 'BSCS, MIT, DIT',
    'QUITALEG, Anna Rhodora M.' => 'BSCS, MIT, MATE TVS',
    'REFORMADO, Lovely Jenn' => 'BSCS, MIT',
    'REYES, Leonard Prim Francis G.' => 'BSICS, MIT',
    'TABIOS, Mc Caulay M.' => 'BSIT',
    'TAMAYO, Dynah M.' => 'BSIT',
    'TAMONDONG, Genes Fidel A.' => 'BSCS',
    'VALDEZ, Mishael M.' => 'BSCS',
    // Part-time Faculty Members
    'HIDALGO, Reynald Jay' => 'BSCS, MSCS, DIT',
    'KIWENG, Clyde A.' => 'BSIT',
    'PANGILINAN, Jose Marie' => 'BSCE, MSCS, DS',
    'PERALTA, Gian Troi K.' => 'BSIT',
    // College of Education (CED) Faculty Members
    'AGAPAY, Yoshio Stevens G.' => 'BSEd SS, MAEd SS',
    'AGNAWA, Marcelino M. Jr.' => 'BSEd PEHM, MAEd PE, PhD Ed Ad',
    'AM-UNA, Alexander H.' => 'BSED Math, MAEd Math',
    'ATOMPAG, Sheryl M.' => 'BSEd Math, MA AS, PhD in Educ',
    'BAGASOL, Mariano T. Jr.' => 'BSEd SS, MAEd SS, PhD Ed Mgmt',
    'BANGTOWAN, Kathleen P.' => 'BSEd MAPEH, MAEd PE',
    'BISCOCHO, Alma L.' => 'BSEd Engl, MAEd Engl',
    'BUCCAT, Ria R.' => 'BEEd SPED, MAEd Ed Mgmt',
    'CACLINI, Joana Kim B.' => 'BEEd',
    'CALUGAN, Janice A.' => 'BSEd Engl, MAEd ESL',
    'CANTOR, Armand John P.' => 'BSEd MAPEH, MAEd PE, PhD in Educ',
    'CASELDO, Dante L.' => 'BSEd Math, MAEd Math, PhD Ed Mgmt',
    'CONG-O, Danilo L.' => 'BSEd PE, MAT PE, PhD Ed Ad',
    'CORPUZ, Gertrude V.' => 'AB Hist, BSEd Hist, MA SS, EdD',
    'DEPAYNOS, Jonas L.' => 'BSEd Math, MA AS, PhD in Educ',
    'DE VERA, Jeric A.' => 'BSEd PEHM, MAEd PE',
    'DINAMLING, Shayne Klarisse E.' => 'BEEd, MAEd Ed Mgmt',
    'DINDIN, Jennifer M.' => 'BSC, AB PE, MA PE, PhD Mgmt',
    'ENDRANO, Apollo A.' => 'BSEd Hist, MAEd SS, PhD Ed Mgmt',
    'EPISTOLA, Lety C.' => 'BSED HE, MAEd Fil, PhD Lang Ed',
    'ESPIRITU, J-Lyn C.' => 'BSEd Math, MAEd Math, PhD Ed Mgmt',
    'FELIX, Nino R.' => 'BSEd PE, MAEd PE, PhD Ed Ad',
    'FERRER, Christine Joy C.' => 'BSEd Math, MAEd Math',
    'FLORES, Klaire Ann A.' => 'BSEd Hist, MST SS',
    'GUINTO, Joan P.' => 'BSEd Engl, MAEd Engl, PhD Educ',
    'GUNABAN, Mary Geraldine B.' => 'AB Engl, MAT Eng, PhD Lang Ed',
    'IGLESIAS, Jonathan C.' => 'BSEd Math, MAEd Math',
    'ISICAN, Illuminada R.' => 'BSEd Fil, MA Admin, MAEd Fil',
    'JANEO, Joshua C.' => 'BSEd Math, MAEd Math',
    'JUDAN, Ronald F.' => 'BSECE, MAEd Math, ME ECE',
    'KAWI, Patricia Rose I.' => 'BEEd SPEd',
    'MALECDAN, Paul T.' => 'BSEd PEHM, MA Ed Ad, PhD Ed Ad',
    'MATIAS, Demerie Joy I.' => 'BEEd Music & Arts',
    'METRA, Renner P.' => 'BSEd MAPEH, MAEd PE',
    'NABUNAT, Charlene A.' => 'BSEd MAPEH, MSPE',
    'NABUNAT-PACIENTE, Irene' => 'BSEd Soc Sci, MAEd Ed Mgmt',
    'NATIOLA, Peejay C.' => 'BSEd PE, MAEd PE, PhD in Educ',
    'NINALGA, Leo Patrick E.' => 'BSEd MAPEH, MAEd PE',
    'OYAM, Donna Marie A.' => 'BSEd Engl, MAEd Fil, MAT Psych, PhD Ed Mgmt',
    'PALAO-AY, Garickson I.' => 'BSEd MAPEH, MAEd PE',
    'PALAROAN, Sheena T.' => 'BSEd Fil, MAEd Cul Ed',
    'PALOS, Roda S.' => 'BSEd Fil, MAEd Cul Ed',
    'PATNAO, James L. Jr.' => 'BSEd Math, MAEd Math',
    'PEREY, Gemma M.' => 'BSEd Home Econ., MAT THE, MAEd Fil, PhD Ed Mgmt',
    'RINGOR, Mary Glo D.' => 'BSEd MAPEH, MSPE',
    'SAGUBO, Elizabeth M.' => 'BEEd, MAEd Elem Ed, PhD Ed Mgmt',
    'SERRANO, Paul Louie B.' => 'BSEd Mus & Art, MAEd PE',
    'SORIANO, John Billy B.' => 'BSEd Soc Sci, MST SS',
    'SUMANOY, Mark Anthony G.' => 'BSEd Math, MA Math',
    'VALERA, Arvin D.' => 'BSEd Soc Sci, MA SS',
    'VINCOY, Jonathan Jose S.' => 'BSEd MAPEH, MAEd PE',
];

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
 * Check if a room is available at specific days and time
 * Returns array with 'available' boolean and 'conflict' info if not available
 */
function checkRoomAvailability($room, $days, $startTime, $endTime, $excludeScheduleId = null) {
    global $useDatabase;
    
    // Get all schedules
    if ($useDatabase) {
        $allSchedules = getAllSchedules();
    } else {
        $allSchedules = $_SESSION['schedules'] ?? [];
    }
    
    // Parse the days string into an array
    $newDays = array_map('trim', explode('/', $days));
    $newDays = array_map('strtolower', $newDays);
    
    foreach ($allSchedules as $schedule) {
        // Skip the schedule being edited
        if ($excludeScheduleId !== null && isset($schedule['id']) && $schedule['id'] == $excludeScheduleId) {
            continue;
        }
        
        // Check main room field (lecture room for CIT/CC courses)
        if (($schedule['room'] ?? '') === $room) {
            // Parse existing schedule's days
            $eDays = $schedule['days'] ?? '';
            if (is_array($eDays)) {
                $existingDays = array_map('trim', $eDays);
            } else {
                $existingDays = array_map('trim', explode('/', $eDays));
            }
            $existingDays = array_map('strtolower', $existingDays);
            
            // Check for day overlap
            $dayOverlap = !empty(array_intersect($newDays, $existingDays));
            
            if ($dayOverlap) {
                $existingStart = $schedule['startTime'] ?? '';
                $existingEnd = $schedule['endTime'] ?? '';
                
                // Check for time overlap
                if ($startTime < $existingEnd && $endTime > $existingStart) {
                    return [
                        'available' => false,
                        'conflict' => [
                            'className' => $schedule['className'] ?? 'Unknown Class',
                            'classCode' => $schedule['classCode'] ?? '',
                            'startTime' => date('g:i A', strtotime($existingStart)),
                            'endTime' => date('g:i A', strtotime($existingEnd)),
                            'instructor' => $schedule['instructor'] ?? '',
                            'days' => $schedule['days'] ?? '',
                            'type' => 'Lecture'
                        ]
                    ];
                }
            }
        }
        
        // Also check lab room field for CIT/CC courses
        $labRoom = $schedule['labRoom'] ?? '';
        if (!empty($labRoom) && $labRoom === $room) {
            // Parse lab days
            $labDaysRaw = $schedule['labDays'] ?? '';
            if (is_array($labDaysRaw)) {
                $existingLabDays = array_map('trim', $labDaysRaw);
            } else {
                $existingLabDays = array_map('trim', explode('/', $labDaysRaw));
            }
            $existingLabDays = array_map('strtolower', $existingLabDays);
            
            // Check for day overlap with lab schedule
            $labDayOverlap = !empty(array_intersect($newDays, $existingLabDays));
            
            if ($labDayOverlap) {
                $labStart = $schedule['labStartTime'] ?? '';
                $labEnd = $schedule['labEndTime'] ?? '';
                
                // Check for time overlap with lab schedule
                if (!empty($labStart) && !empty($labEnd) && $startTime < $labEnd && $endTime > $labStart) {
                    return [
                        'available' => false,
                        'conflict' => [
                            'className' => ($schedule['className'] ?? 'Unknown Class') . ' (Lab)',
                            'classCode' => $schedule['classCode'] ?? '',
                            'startTime' => date('g:i A', strtotime($labStart)),
                            'endTime' => date('g:i A', strtotime($labEnd)),
                            'instructor' => $schedule['labInstructor'] ?? $schedule['instructor'] ?? '',
                            'days' => $labDaysRaw,
                            'type' => 'Laboratory'
                        ]
                    ];
                }
            }
        }
    }
    
    return ['available' => true, 'conflict' => null];
}

/**
 * Check if a schedule is a duplicate (same class code + overlapping days + overlapping time)
 * This prevents the exact same class from being scheduled twice on the same day/time
 */
function checkDuplicateSchedule($classCode, $days, $startTime, $endTime, $excludeScheduleId = null) {
    global $useDatabase;
    
    if ($useDatabase) {
        $allSchedules = getAllSchedules();
    } else {
        $allSchedules = $_SESSION['schedules'] ?? [];
    }
    
    // Parse new days
    $newDays = array_map('strtolower', array_map('trim', explode('/', $days)));
    
    foreach ($allSchedules as $schedule) {
        if ($excludeScheduleId !== null && isset($schedule['id']) && $schedule['id'] == $excludeScheduleId) {
            continue;
        }
        
        // Check same class code
        if (strcasecmp($schedule['classCode'] ?? '', $classCode) !== 0) {
            continue;
        }
        
        // Parse existing days
        $eDays = $schedule['days'] ?? '';
        if (is_array($eDays)) {
            $existingDays = array_map('trim', $eDays);
        } else {
            $existingDays = array_map('trim', explode('/', $eDays));
        }
        $existingDays = array_map('strtolower', $existingDays);
        
        // Check day overlap
        if (empty(array_intersect($newDays, $existingDays))) {
            continue;
        }
        
        // Check time overlap
        $existingStart = $schedule['startTime'] ?? '';
        $existingEnd = $schedule['endTime'] ?? '';
        if ($startTime < $existingEnd && $endTime > $existingStart) {
            return [
                'duplicate' => true,
                'existing' => [
                    'classCode' => $schedule['classCode'] ?? '',
                    'className' => $schedule['className'] ?? '',
                    'room' => $schedule['room'] ?? '',
                    'days' => is_array($eDays) ? implode('/', $eDays) : $eDays,
                    'startTime' => date('g:i A', strtotime($existingStart)),
                    'endTime' => date('g:i A', strtotime($existingEnd)),
                    'instructor' => $schedule['instructor'] ?? ''
                ]
            ];
        }
    }
    
    return ['duplicate' => false, 'existing' => null];
}

// Store form data for repopulation on validation error
$formData = [
    'yearLevel' => '',
    'term' => '',
    'block' => '',
    'classCode' => '',
    'className' => '',
    'scheduleRoom' => '',
    'instructor' => '',
    'scheduleDays' => '',
    'startTime' => '',
    'endTime' => ''
];
$validationError = '';
$showForm = false;
$editMode = false;
$editScheduleId = 0;

/**
 * Validate schedule date and time
 * - Rejects dates before today
 * - Rejects times that have already passed for today's schedule
 * Returns array with 'valid' boolean and 'message' if there's an issue
 */
function validateScheduleTime($date, $startTime, $endTime) {
    $today = date('Y-m-d');
    $currentTime = date('H:i:s');
    
    // Check if date is in the past (before today)
    if ($date < $today) {
        return [
            'valid' => false,
            'message' => "Cannot schedule for a past date (" . date('d/m/Y', strtotime($date)) . "). Today is " . date('d/m/Y') . ". Please select today or a future date."
        ];
    }
    
    // Validate end time is after start time
    if ($endTime <= $startTime) {
        return [
            'valid' => false,
            'message' => "End time must be after start time."
        ];
    }
    
    // If schedule is for today, check if start time has already passed
    if ($date === $today) {
        if ($startTime < $currentTime) {
            return [
                'valid' => false,
                'message' => "The start time " . date('g:i A', strtotime($startTime)) . " has already passed. It's currently " . date('g:i A') . ". Please select a future time."
            ];
        }
    }
    
    return ['valid' => true, 'message' => ''];
}

// Handle adding schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_schedule') {
    $classCode = strtoupper(trim($_POST['classCode'] ?? ''));
    $isCITCC = (strpos($classCode, 'CIT') === 0 || strpos($classCode, 'CC') === 0);
    
    // Store form data for repopulation
    $formData = [
        'yearLevel' => $_POST['yearLevel'] ?? '',
        'term' => $_POST['term'] ?? '',
        'block' => $_POST['block'] ?? '',
        'classCode' => $_POST['classCode'] ?? '',
        'className' => $_POST['className'] ?? '',
        'scheduleRoom' => $_POST['scheduleRoom'] ?? '',
        'instructor' => $_POST['instructor'] ?? '',
        'scheduleDate' => '', // Legacy - not used anymore
        'days' => $_POST['scheduleDays'] ?? '',
        'startTime' => $_POST['startTime'] ?? '',
        'endTime' => $_POST['endTime'] ?? '',
        // Dual schedule fields for CIT/CC
        'lecDays' => $_POST['lecDays'] ?? '',
        'lecStartTime' => $_POST['lecStartTime'] ?? '',
        'lecEndTime' => $_POST['lecEndTime'] ?? '',
        'lecRoom' => $_POST['lecRoom'] ?? '',
        'lecInstructor' => $_POST['lecInstructor'] ?? '',
        'labDays' => $_POST['labDays'] ?? '',
        'labStartTime' => $_POST['labStartTime'] ?? '',
        'labEndTime' => $_POST['labEndTime'] ?? '',
        'labRoom' => $_POST['labRoom'] ?? '',
        'labInstructor' => $_POST['labInstructor'] ?? ''
    ];
    
    if ($isCITCC) {
        // CIT/CC Course: Handle dual schedule (Lecture + Laboratory)
        $lecDays = $_POST['lecDays'] ?? '';
        $lecStartTime = $_POST['lecStartTime'] ?? '';
        $lecEndTime = $_POST['lecEndTime'] ?? '';
        $lecRoom = $_POST['lecRoom'] ?? '';
        $lecInstructor = $_POST['lecInstructor'] ?? '';
        $labDays = $_POST['labDays'] ?? '';
        $labStartTime = $_POST['labStartTime'] ?? '';
        $labEndTime = $_POST['labEndTime'] ?? '';
        $labRoom = $_POST['labRoom'] ?? '';
        $labInstructor = $_POST['labInstructor'] ?? '';
        
        // Validate lecture schedule
        if (empty($lecDays)) {
            $validationError = "Please select at least one day for the Lecture schedule.";
            $showForm = true;
        } else if (empty($lecStartTime) || empty($lecEndTime)) {
            $validationError = "Please select start and end time for the Lecture schedule.";
            $showForm = true;
        } else if (empty($lecRoom)) {
            $validationError = "Please select a room for the Lecture schedule.";
            $showForm = true;
        } else if (empty($lecInstructor)) {
            $validationError = "Please select an instructor for the Lecture schedule.";
            $showForm = true;
        }
        // Validate laboratory schedule
        else if (empty($labDays)) {
            $validationError = "Please select at least one day for the Laboratory schedule.";
            $showForm = true;
        } else if (empty($labStartTime) || empty($labEndTime)) {
            $validationError = "Please select start and end time for the Laboratory schedule.";
            $showForm = true;
        } else if (empty($labRoom)) {
            $validationError = "Please select a room for the Laboratory schedule.";
            $showForm = true;
        } else if (empty($labInstructor)) {
            $validationError = "Please select an instructor for the Laboratory schedule.";
            $showForm = true;
        } else {
            // Check room availability for lecture
            $lecRoomCheck = checkRoomAvailability($lecRoom, $lecDays, $lecStartTime, $lecEndTime);
            if (!$lecRoomCheck['available']) {
                $conflict = $lecRoomCheck['conflict'];
                $validationError = "Lecture room not available! {$lecRoom} is already booked for \"{$conflict['classCode']}\" from {$conflict['startTime']} to {$conflict['endTime']}.";
                $showForm = true;
            } else {
                // Check room availability for laboratory
                $labRoomCheck = checkRoomAvailability($labRoom, $labDays, $labStartTime, $labEndTime);
                if (!$labRoomCheck['available']) {
                    $conflict = $labRoomCheck['conflict'];
                    $validationError = "Laboratory room not available! {$labRoom} is already booked for \"{$conflict['classCode']}\" from {$conflict['startTime']} to {$conflict['endTime']}.";
                    $showForm = true;
                } else {
                    // All validations passed - create SINGLE combined schedule with both lec and lab
                    $className = htmlspecialchars($_POST['className'] ?? '');
                    
                    if ($useDatabase) {
                        // Create combined schedule with lecture as main + lab in dedicated fields
                        $combinedSchedule = [
                            'classCode' => htmlspecialchars($classCode),
                            'className' => $className,
                            'yearLevel' => htmlspecialchars($_POST['yearLevel'] ?? ''),
                            'term' => htmlspecialchars($_POST['term'] ?? ''),
                            'block' => htmlspecialchars($_POST['block'] ?? ''),
                            // Main fields store lecture info
                            'room' => htmlspecialchars($lecRoom),
                            'instructor' => htmlspecialchars($lecInstructor),
                            'days' => htmlspecialchars($lecDays),
                            'startTime' => htmlspecialchars($lecStartTime),
                            'endTime' => htmlspecialchars($lecEndTime),
                            'department' => htmlspecialchars($_POST['department'] ?? ''),
                            'classSize' => intval($_POST['classSize'] ?? 0),
                            // CIT/CC flag
                            'isCITCC' => true,
                            // Lecture fields (duplicate for clarity)
                            'lecRoom' => htmlspecialchars($lecRoom),
                            'lecInstructor' => htmlspecialchars($lecInstructor),
                            'lecDays' => htmlspecialchars($lecDays),
                            'lecStartTime' => htmlspecialchars($lecStartTime),
                            'lecEndTime' => htmlspecialchars($lecEndTime),
                            // Laboratory fields
                            'labRoom' => htmlspecialchars($labRoom),
                            'labInstructor' => htmlspecialchars($labInstructor),
                            'labDays' => htmlspecialchars($labDays),
                            'labStartTime' => htmlspecialchars($labStartTime),
                            'labEndTime' => htmlspecialchars($labEndTime)
                        ];
                        
                        if (addSchedule($combinedSchedule)) {
                            $_SESSION['success'] = 'CIT/CC Schedule (Lecture + Laboratory) added successfully!';
                        } else {
                            $_SESSION['error'] = 'Failed to add schedule.';
                        }
                    } else {
                        // Use session storage - create single combined schedule
                        $maxId = 0;
                        foreach ($_SESSION['schedules'] as $schedule) {
                            if (isset($schedule['id']) && $schedule['id'] > $maxId) {
                                $maxId = $schedule['id'];
                            }
                        }
                        
                        // Create combined schedule
                        $_SESSION['schedules'][] = [
                            'id' => $maxId + 1,
                            'classCode' => htmlspecialchars($classCode),
                            'className' => $className,
                            'yearLevel' => htmlspecialchars($_POST['yearLevel'] ?? ''),
                            'term' => htmlspecialchars($_POST['term'] ?? ''),
                            'block' => htmlspecialchars($_POST['block'] ?? ''),
                            // Main fields store lecture info
                            'room' => htmlspecialchars($lecRoom),
                            'instructor' => htmlspecialchars($lecInstructor),
                            'days' => htmlspecialchars($lecDays),
                            'startTime' => htmlspecialchars($lecStartTime),
                            'endTime' => htmlspecialchars($lecEndTime),
                            // CIT/CC flag
                            'isCITCC' => true,
                            // Laboratory fields
                            'labRoom' => htmlspecialchars($labRoom),
                            'labInstructor' => htmlspecialchars($labInstructor),
                            'labDays' => htmlspecialchars($labDays),
                            'labStartTime' => htmlspecialchars($labStartTime),
                            'labEndTime' => htmlspecialchars($labEndTime)
                        ];
                        
                        $_SESSION['success'] = 'CIT/CC Schedule (Lecture + Laboratory) added successfully!';
                    }
                    header('Location: ?page=schedule');
                    exit();
                }
            }
        }
    } else {
        // Regular course: Handle single schedule
        $selectedDays = $_POST['scheduleDays'] ?? '';
        if (empty($selectedDays)) {
            $validationError = "Please select at least one day for the schedule.";
            $showForm = true;
        } else {
            // Check room availability
            $roomCheck = checkRoomAvailability(
                $_POST['scheduleRoom'] ?? '',
                $_POST['scheduleDays'] ?? '',
                $_POST['startTime'] ?? '',
                $_POST['endTime'] ?? ''
            );
            
            if (!$roomCheck['available']) {
                $conflict = $roomCheck['conflict'];
                $validationError = "Room not available! Already booked for \"{$conflict['classCode']} - {$conflict['className']}\" from {$conflict['startTime']} to {$conflict['endTime']} by {$conflict['instructor']}.";
                $showForm = true;
            } else {
                // Check for duplicate schedule (same class code + same day + same time)
                $dupCheck = checkDuplicateSchedule(
                    $_POST['classCode'] ?? '',
                    $_POST['scheduleDays'] ?? '',
                    $_POST['startTime'] ?? '',
                    $_POST['endTime'] ?? ''
                );
                
                if ($dupCheck['duplicate']) {
                    $dup = $dupCheck['existing'];
                    $validationError = "Duplicate schedule! \"{$dup['classCode']} - {$dup['className']}\" is already scheduled on {$dup['days']} from {$dup['startTime']} to {$dup['endTime']} in {$dup['room']}.";
                    $showForm = true;
                } else {
                // Validation passed, proceed with saving
                if ($useDatabase) {
                    // Use database
                    $newSchedule = [
                        'classCode' => htmlspecialchars($_POST['classCode'] ?? ''),
                        'className' => htmlspecialchars($_POST['className'] ?? ''),
                        'yearLevel' => htmlspecialchars($_POST['yearLevel'] ?? ''),
                        'term' => htmlspecialchars($_POST['term'] ?? ''),
                        'block' => htmlspecialchars($_POST['block'] ?? ''),
                        'room' => htmlspecialchars($_POST['scheduleRoom'] ?? ''),
                        'instructor' => htmlspecialchars($_POST['instructor'] ?? ''),
                        'days' => htmlspecialchars($_POST['scheduleDays'] ?? ''),
                        'startTime' => htmlspecialchars($_POST['startTime'] ?? ''),
                        'endTime' => htmlspecialchars($_POST['endTime'] ?? ''),
                        'department' => htmlspecialchars($_POST['department'] ?? ''),
                        'classSize' => intval($_POST['classSize'] ?? 0)
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
                        'yearLevel' => htmlspecialchars($_POST['yearLevel'] ?? ''),
                        'term' => htmlspecialchars($_POST['term'] ?? ''),
                        'block' => htmlspecialchars($_POST['block'] ?? ''),
                        'room' => htmlspecialchars($_POST['scheduleRoom'] ?? ''),
                        'instructor' => htmlspecialchars($_POST['instructor'] ?? ''),
                        'days' => htmlspecialchars($_POST['scheduleDays'] ?? ''),
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
        $skipCount = 0;
        $successList = [];
        $conflictList = [];
        $skippedList = [];
        $correctionsList = []; // Track auto-corrections made
        
        if ($useDatabase) {
            // Import to database with smart auto-fill
            while (($data = fgetcsv($file)) !== false) {
                if (count($data) >= 6 && !empty(trim($data[0]))) {
                    $corrections = []; // Track corrections for this row
                    
                    // Days field (column index 4)
                    $rawDays = trim($data[4] ?? '');
                    
                    // Validate days - skip row if empty
                    if (empty($rawDays)) {
                        $skipCount++;
                        $skippedList[] = [
                            'code' => trim($data[0]),
                            'name' => trim($data[1] ?? ''),
                            'reason' => 'No days specified'
                        ];
                        continue;
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
                    $isDuplicate = false;
                    $conflictWith = null;
                    $conflictDetails = null;
                    
                    // Check for duplicate schedule (same class code + same day + same time)
                    if (!empty($rawDays)) {
                        $dupCheck = checkDuplicateSchedule($classCode, $rawDays, $parsedStartTime, $parsedEndTime);
                        if ($dupCheck['duplicate']) {
                            $isDuplicate = true;
                            $conflictDetails = $dupCheck['existing'];
                            $conflictCount++;
                        }
                    }
                    
                    if (!$isDuplicate && !empty($room) && !empty($rawDays)) {
                        $roomCheck = checkRoomAvailability($room, $rawDays, $parsedStartTime, $parsedEndTime);
                        if (!$roomCheck['available']) {
                            $hasConflict = true;
                            $conflictWith = $roomCheck['conflict']['className'] . ' (' . $roomCheck['conflict']['startTime'] . ' - ' . $roomCheck['conflict']['endTime'] . ')';
                            $conflictDetails = $roomCheck['conflict'];
                            $conflictCount++;
                        }
                    }
                    
                    $formattedStartTime = date('g:i A', strtotime($parsedStartTime));
                    $formattedEndTime = date('g:i A', strtotime($parsedEndTime));
                    
                    $itemInfo = [
                        'code' => $classCode,
                        'name' => trim($data[1]),
                        'room' => $room,
                        'instructor' => $instructor,
                        'date' => $rawDays,
                        'time' => $formattedStartTime . ' - ' . $formattedEndTime,
                        'corrections' => $corrections
                    ];
                    
                    // If there's a conflict or duplicate, DON'T add to database - just track as skipped
                    if ($hasConflict || $isDuplicate) {
                        if ($isDuplicate) {
                            $conflictDetails['_reason'] = 'Duplicate schedule';
                        }
                        $itemInfo['conflictWith'] = $conflictDetails;
                        $conflictList[] = $itemInfo;
                    } else {
                        // No conflict - safe to add
                        $newSchedule = [
                            'classCode' => $classCode,
                            'className' => trim($data[1]),
                            'room' => $room,
                            'instructor' => $instructor,
                            'days' => $rawDays,
                            'startTime' => $parsedStartTime,
                            'endTime' => $parsedEndTime,
                            'department' => $rawDepartment,
                            'classSize' => intval($data[8] ?? 0),
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
                    
                    // Days field (column index 4)
                    $rawDays = trim($data[4] ?? '');
                    
                    // Validate days - skip row if empty
                    if (empty($rawDays)) {
                        $skipCount++;
                        $skippedList[] = [
                            'code' => trim($data[0]),
                            'name' => trim($data[1] ?? ''),
                            'reason' => 'No days specified'
                        ];
                        continue;
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
                    
                    $formattedStartTime = date('g:i A', strtotime($parsedStartTime));
                    $formattedEndTime = date('g:i A', strtotime($parsedEndTime));
                    
                    $classCode = trim($data[0]);
                    
                    $itemInfo = [
                        'code' => $classCode,
                        'name' => trim($data[1]),
                        'room' => $room,
                        'instructor' => $instructor,
                        'date' => $rawDays,
                        'time' => $formattedStartTime . ' - ' . $formattedEndTime,
                        'corrections' => $corrections
                    ];
                    
                    // Check for duplicate schedule (same class code + overlapping day/time)
                    $isDuplicate = false;
                    if (!empty($classCode) && !empty($rawDays)) {
                        $dupCheck = checkDuplicateSchedule($classCode, $rawDays, $parsedStartTime, $parsedEndTime);
                        if ($dupCheck['duplicate']) {
                            $isDuplicate = true;
                            $conflictDetails = $dupCheck['existing'];
                            $conflictCount++;
                        }
                    }
                    
                    // Check for room conflict using days-based check
                    $hasConflict = false;
                    
                    if (!$isDuplicate && !empty($room) && !empty($rawDays)) {
                        $roomCheck = checkRoomAvailability($room, $rawDays, $parsedStartTime, $parsedEndTime);
                        if (!$roomCheck['available']) {
                            $hasConflict = true;
                            $conflictDetails = $roomCheck['conflict'];
                            $conflictCount++;
                        }
                    }
                    
                    if ($hasConflict || $isDuplicate) {
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
                            'days' => htmlspecialchars($rawDays),
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
            'corrections_list' => $correctionsList ?? [],
            'skip_count' => $skipCount,
            'skipped_list' => $skippedList
        ];
        
        $warnings = [];
        if ($conflictCount > 0) {
            $warnings[] = $conflictCount . ' schedule(s) were SKIPPED due to room conflicts.';
        }
        if ($skipCount > 0) {
            $warnings[] = $skipCount . ' schedule(s) were SKIPPED because no days were specified.';
        }
        if (!empty($warnings)) {
            $_SESSION['warning'] = '⚠️ ' . implode(' ', $warnings) . ' See details below.';
        }
        $_SESSION['success'] = $importCount . ' schedule(s) imported successfully!' . ($conflictCount + $skipCount > 0 ? ' (' . ($conflictCount + $skipCount) . ' skipped)' : '');
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
        'yearLevel' => $_POST['yearLevel'] ?? '',
        'term' => $_POST['term'] ?? '',
        'block' => $_POST['block'] ?? '',
        'classCode' => $_POST['classCode'] ?? '',
        'className' => $_POST['className'] ?? '',
        'scheduleRoom' => $_POST['scheduleRoom'] ?? '',
        'instructor' => $_POST['instructor'] ?? '',
        'scheduleDays' => $_POST['scheduleDays'] ?? '',
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
        // Validate day selection
        if (empty($_POST['scheduleDays'])) {
            $validationError = 'Please select at least one day.';
            $showForm = true;
            $editMode = true;
            $editScheduleId = $editId;
        } else {
            // Check room availability (exclude current schedule)
            $roomCheck = checkRoomAvailability(
                $_POST['scheduleRoom'] ?? '',
                $_POST['scheduleDays'] ?? '',
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
                // Check for duplicate schedule (exclude current)
                $dupCheck = checkDuplicateSchedule(
                    $_POST['classCode'] ?? '',
                    $_POST['scheduleDays'] ?? '',
                    $_POST['startTime'] ?? '',
                    $_POST['endTime'] ?? '',
                    $editId
                );
                
                if ($dupCheck['duplicate']) {
                    $dup = $dupCheck['existing'];
                    $validationError = "Duplicate schedule! \"{$dup['classCode']} - {$dup['className']}\" is already scheduled on {$dup['days']} from {$dup['startTime']} to {$dup['endTime']} in {$dup['room']}.";
                    $showForm = true;
                    $editMode = true;
                    $editScheduleId = $editId;
                } else {
                // Validation passed, proceed with update
                if ($useDatabase) {
                    $updateData = [
                        'classCode' => htmlspecialchars($_POST['classCode'] ?? ''),
                        'className' => htmlspecialchars($_POST['className'] ?? ''),
                        'yearLevel' => htmlspecialchars($_POST['yearLevel'] ?? ''),
                        'term' => htmlspecialchars($_POST['term'] ?? ''),
                        'block' => htmlspecialchars($_POST['block'] ?? ''),
                        'room' => htmlspecialchars($_POST['scheduleRoom'] ?? ''),
                        'instructor' => htmlspecialchars($_POST['instructor'] ?? ''),
                        'days' => htmlspecialchars($_POST['scheduleDays'] ?? ''),
                        'startTime' => htmlspecialchars($_POST['startTime'] ?? ''),
                        'endTime' => htmlspecialchars($_POST['endTime'] ?? ''),
                        'department' => htmlspecialchars($_POST['department'] ?? ''),
                        'classSize' => intval($_POST['classSize'] ?? 0)
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
                        $schedule['yearLevel'] = htmlspecialchars($_POST['yearLevel'] ?? '');
                        $schedule['term'] = htmlspecialchars($_POST['term'] ?? '');
                        $schedule['block'] = htmlspecialchars($_POST['block'] ?? '');
                        $schedule['classCode'] = htmlspecialchars($_POST['classCode'] ?? '');
                        $schedule['className'] = htmlspecialchars($_POST['className'] ?? '');
                        $schedule['room'] = htmlspecialchars($_POST['scheduleRoom'] ?? '');
                        $schedule['instructor'] = htmlspecialchars($_POST['instructor'] ?? '');
                        $schedule['days'] = htmlspecialchars($_POST['scheduleDays'] ?? '');
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
            'yearLevel' => $editScheduleData['yearLevel'] ?? '',
            'term' => $editScheduleData['term'] ?? '',
            'block' => $editScheduleData['block'] ?? '',
            'classCode' => $editScheduleData['classCode'] ?? '',
            'className' => $editScheduleData['className'] ?? '',
            'scheduleRoom' => $editScheduleData['room'] ?? '',
            'instructor' => $editScheduleData['instructor'] ?? '',
            'scheduleDays' => $editScheduleData['days'] ?? '',
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

// Determine current view: 'add' for add schedule page, 'overview' for schedule list, 'block_detail' for block details
$currentView = $_GET['view'] ?? 'overview';
// Get block detail parameters
$viewBlock = $_GET['block'] ?? '';
$viewYear = $_GET['year'] ?? '';
// If editing or form needs to be shown, switch to add view
if ($showForm || $editMode || isset($_GET['edit_schedule'])) {
    $currentView = 'add';
}
?>

<section id="schedule" class="tab-content active">
    <div class="card">
        <!-- Navigation Tabs -->
        <div style="display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 15px;">
            <a href="?page=schedule&view=overview" class="btn-<?php echo ($currentView === 'overview' || $currentView === 'block_detail') ? 'primary' : 'secondary'; ?>" style="text-decoration: none;">
                📋 Schedules Overview
            </a>
            <a href="?page=schedule&view=add" class="btn-<?php echo $currentView === 'add' ? 'primary' : 'secondary'; ?>" style="text-decoration: none;">
                ➕ Add Schedule
            </a>
        </div>

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

        <?php if ($currentView === 'overview'): ?>
        <!-- ========== SCHEDULES OVERVIEW VIEW ========== -->
        <h2>Schedule Overview</h2>
        <p class="subtitle">View and manage class schedules</p>

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
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Course Name</th>
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Room</th>
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Days</th>
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
                
                <?php if (!empty($importResults['skipped_list'])): ?>
                <details style="margin-bottom: 15px;">
                    <summary style="cursor: pointer; font-weight: bold; color: #856404; padding: 10px; background: #fff3cd; border-radius: 6px;">
                        ⚠️ Skipped - Missing Days (<?php echo count($importResults['skipped_list']); ?>) - These were NOT added
                    </summary>
                    <div style="max-height: 300px; overflow-y: auto; margin-top: 10px;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                            <thead>
                                <tr style="background: #ffeeba;">
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Code</th>
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Course Name</th>
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($importResults['skipped_list'] as $skipped): ?>
                                <tr style="background: #fffbea;">
                                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($skipped['code']); ?></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($skipped['name']); ?></td>
                                    <td style="padding: 8px; border: 1px solid #ddd; color: #856404;">⚠️ <?php echo htmlspecialchars($skipped['reason']); ?></td>
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
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Course Name</th>
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Room</th>
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Instructor</th>
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Days</th>
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
            <button class="btn-secondary" onclick="toggleCSVImport()">📥 Import CSV</button>
            <a href="?export_csv=true" class="btn-secondary">📤 Export CSV</a>
            <a href="?download_template=true" class="btn-secondary">📄 Download Template</a>
        </div>
        
        <!-- View Mode Toggle -->
        <div class="view-mode-toggle" style="display: flex; gap: 10px; margin-top: 15px; padding: 12px 15px; background: #e8f5e9; border-radius: 8px; align-items: center;">
            <label style="font-size: 13px; font-weight: 600; color: #1c361d; margin-right: 10px;">View Mode:</label>
            <button type="button" id="viewModeBlock" onclick="setViewMode('block')" class="view-mode-btn active" style="padding: 8px 16px; border: 2px solid #1c361d; background: #1c361d; color: #fff; border-radius: 6px; cursor: pointer; font-weight: 500; transition: all 0.2s;">
                📦 Per Block
            </button>
            <button type="button" id="viewModeSchedule" onclick="setViewMode('schedule')" class="view-mode-btn" style="padding: 8px 16px; border: 2px solid #1c361d; background: #fff; color: #1c361d; border-radius: 6px; cursor: pointer; font-weight: 500; transition: all 0.2s;">
                📋 Per Schedule
            </button>
        </div>

        <!-- Filter Options -->
        <div class="filter-options" style="display: flex; gap: 15px; flex-wrap: wrap; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
            <div class="filter-group">
                <label style="font-size: 13px; font-weight: 600; color: #555; display: block; margin-bottom: 5px;">Year Level</label>
                <select id="filterYear" onchange="applyFilters()" class="filter-select" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; min-width: 140px;">
                    <option value="all">All Years</option>
                    <option value="1st Year">1st Year</option>
                    <option value="2nd Year">2nd Year</option>
                    <option value="3rd Year">3rd Year</option>
                </select>
            </div>
            <div class="filter-group">
                <label style="font-size: 13px; font-weight: 600; color: #555; display: block; margin-bottom: 5px;">Course Type</label>
                <select id="filterType" onchange="applyFilters()" class="filter-select" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; min-width: 140px;">
                    <option value="all">All Types</option>
                    <option value="citcc">CIT/CC Courses</option>
                    <option value="gened">Gen Ed Courses</option>
                    <option value="pe">PE/NSTP</option>
                </select>
            </div>
            <div class="filter-group">
                <label style="font-size: 13px; font-weight: 600; color: #555; display: block; margin-bottom: 5px;">Day</label>
                <select id="filterDay" onchange="applyFilters()" class="filter-select" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; min-width: 140px;">
                    <option value="all">All Days</option>
                    <option value="monday">Monday</option>
                    <option value="tuesday">Tuesday</option>
                    <option value="wednesday">Wednesday</option>
                    <option value="thursday">Thursday</option>
                    <option value="friday">Friday</option>
                    <option value="saturday">Saturday</option>
                </select>
            </div>
            <div class="filter-group">
                <label style="font-size: 13px; font-weight: 600; color: #555; display: block; margin-bottom: 5px;">Block</label>
                <select id="filterBlock" onchange="applyFilters()" class="filter-select" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; min-width: 140px;">
                    <option value="all">All Blocks</option>
                    <?php 
                    // Get unique blocks from schedules
                    $uniqueBlocks = [];
                    foreach ($schedules as $sched) {
                        $block = $sched['block'] ?? '';
                        if (!empty($block) && !in_array($block, $uniqueBlocks)) {
                            $uniqueBlocks[] = $block;
                        }
                    }
                    sort($uniqueBlocks);
                    foreach ($uniqueBlocks as $block): ?>
                        <option value="<?php echo htmlspecialchars($block); ?>"><?php echo htmlspecialchars($block); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label style="font-size: 13px; font-weight: 600; color: #555; display: block; margin-bottom: 5px;">Instructor</label>
                <select id="filterInstructor" onchange="applyFilters()" class="filter-select" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; min-width: 180px;">
                    <option value="all">All Instructors</option>
                    <?php 
                    // Get unique instructors from schedules
                    $uniqueInstructors = [];
                    foreach ($schedules as $sched) {
                        $inst = $sched['instructor'] ?? '';
                        if (!empty($inst) && !in_array($inst, $uniqueInstructors)) {
                            $uniqueInstructors[] = $inst;
                        }
                        $labInst = $sched['labInstructor'] ?? '';
                        if (!empty($labInst) && !in_array($labInst, $uniqueInstructors)) {
                            $uniqueInstructors[] = $labInst;
                        }
                    }
                    sort($uniqueInstructors);
                    foreach ($uniqueInstructors as $inst): ?>
                        <option value="<?php echo htmlspecialchars($inst); ?>"><?php echo htmlspecialchars($inst); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group" style="display: flex; align-items: flex-end;">
                <button type="button" onclick="resetFilters()" class="btn-secondary" style="padding: 8px 12px;">Reset Filters</button>
            </div>
        </div>

        <!-- CSV Import Section -->
        <div id="csvImportSection" class="card form-card" style="display: none; margin-top: 20px;">
            <h3>Import Schedules from CSV</h3>
            <p class="subtitle">CSV format: Class Code, Course Name, Room, Instructor, Days (e.g. Monday/Wednesday), Start Time (HH:MM), End Time (HH:MM)</p>
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
        <?php endif; ?>
        <!-- END OVERVIEW: Import/Filter Section -->

        <?php if ($currentView === 'add'): ?>
        <!-- ========== ADD SCHEDULE VIEW ========== -->
        <h2><?php echo $editMode ? 'Edit Schedule' : 'Add New Schedule'; ?></h2>
        <p class="subtitle">Fill in the details below to <?php echo $editMode ? 'update' : 'create'; ?> a schedule</p>

        <!-- Add/Edit Schedule Form -->
        <div id="addScheduleForm" class="card form-card" style="margin-top: 20px;">
            <?php if (!empty($validationError)): ?>
            <div class="warning-message" style="background: #fff3cd; border: 1px solid #ffc107; color: #856404; padding: 12px 16px; border-radius: 6px; margin-bottom: 16px;">
                ⚠️ <?php echo $validationError; ?>
            </div>
            <?php endif; ?>
            <form method="POST" action="?page=schedule" id="scheduleForm">
                <input type="hidden" name="action" value="<?php echo $editMode ? 'edit_schedule' : 'add_schedule'; ?>">
                <?php if ($editMode): ?>
                <input type="hidden" name="edit_id" value="<?php echo $editScheduleId; ?>">
                <?php endif; ?>
                
                <!-- Class Information -->
                <div class="form-section" style="margin-bottom: 20px;">
                    <h4 style="color: #555; margin-bottom: 12px; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Class Information</h4>
                    
                    <!-- Row 1: Year Level, Term, Block -->
                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label>Year Level</label>
                            <select name="yearLevel" id="yearLevelSelect" required onchange="filterClassCodesByYearAndTerm()">
                                <option value="">-- Select Year Level --</option>
                                <option value="1st Year" <?php echo ($formData['yearLevel'] === '1st Year') ? 'selected' : ''; ?>>1st Year</option>
                                <option value="2nd Year" <?php echo ($formData['yearLevel'] === '2nd Year') ? 'selected' : ''; ?>>2nd Year</option>
                                <option value="3rd Year" <?php echo ($formData['yearLevel'] === '3rd Year') ? 'selected' : ''; ?>>3rd Year</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Term</label>
                            <select name="term" id="termSelect" required onchange="filterClassCodesByYearAndTerm()">
                                <option value="">-- Select Term --</option>
                                <option value="1st Term" <?php echo ($formData['term'] === '1st Term') ? 'selected' : ''; ?>>1st Term</option>
                                <option value="2nd Term" <?php echo ($formData['term'] === '2nd Term') ? 'selected' : ''; ?>>2nd Term</option>
                                <option value="3rd Term" <?php echo ($formData['term'] === '3rd Term') ? 'selected' : ''; ?>>3rd Term</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Block</label>
                            <select name="block" id="blockSelect" required>
                                <option value="">-- Select Year Level First --</option>
                            </select>
                            <small style="color: #666; font-size: 12px;">Select Year Level to see blocks</small>
                        </div>
                    </div>
                    
                    <!-- Row 2: Class Code, Course Name -->
                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label>Class Code</label>
                            <select name="classCode" id="classCodeSelect" required onchange="autoFillClassName()">
                                <option value="">-- Select Year Level & Term First --</option>
                            </select>
                            <small style="color: #666; font-size: 12px;">Select Year Level and Term to see available courses</small>
                        </div>
                        <div class="form-group">
                            <label>Course Name</label>
                            <input type="text" name="className" id="classNameInput" required readonly placeholder="Auto-filled from Class Code" value="<?php echo htmlspecialchars($formData['className']); ?>" style="background-color: #f5f5f5;">
                        </div>
                    </div>
                </div>
                
                <?php
                // Define days array for all schedule sections
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                ?>
                
                <!-- CIT/CC DUAL SCHEDULE SECTIONS (Lecture + Laboratory) -->
                <div id="dualScheduleContainer" style="display: none;">
                    <div style="background: #e3f2fd; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;">
                        <p style="margin: 0; color: #1565c0; font-weight: 500;">📋 CIT/CC courses require both Lecture and Laboratory schedules</p>
                        <small style="color: #1976d2;">Fill in the schedule details for both sessions below</small>
                    </div>
                    
                    <!-- LECTURE Section -->
                    <div class="form-section" style="margin-bottom: 20px; padding: 16px; background: #f0f7ff; border-radius: 8px; border-left: 4px solid #1976d2;">
                        <h4 style="color: #1976d2; margin-bottom: 12px; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">📚 Lecture Schedule</h4>
                        
                        <!-- Lecture Day Selection -->
                        <div class="form-group" style="margin-bottom: 16px;">
                            <label style="display: block; margin-bottom: 8px;">Lecture Days</label>
                            <div id="lecDayButtonsContainer" style="display: flex; flex-wrap: wrap; gap: 8px;">
                                <?php 
                                $lecSelectedDays = isset($formData['lecDays']) ? (is_array($formData['lecDays']) ? $formData['lecDays'] : explode('/', $formData['lecDays'])) : [];
                                foreach ($days as $day): 
                                    $isSelected = in_array($day, $lecSelectedDays) || in_array(strtolower($day), $lecSelectedDays);
                                ?>
                                <button type="button" 
                                        class="day-btn lec-day-btn <?php echo $isSelected ? 'selected' : ''; ?>" 
                                        data-day="<?php echo $day; ?>"
                                        onclick="toggleDualDay(this, 'lec')"
                                        style="padding: 10px 16px; border: 2px solid <?php echo $isSelected ? '#1976d2' : '#ddd'; ?>; 
                                               background: <?php echo $isSelected ? '#1976d2' : '#fff'; ?>; 
                                               color: <?php echo $isSelected ? '#fff' : '#333'; ?>; 
                                               border-radius: 6px; cursor: pointer; font-weight: 500; transition: all 0.2s;">
                                    <?php echo $day; ?>
                                </button>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="lecDays" id="lecDaysInput" value="<?php echo htmlspecialchars($formData['lecDays'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Lecture Start Time</label>
                                <select name="lecStartTime" id="lecStartTimeSelect" onchange="autoFillDualEndTime('lec')">
                                    <option value="">-- Select Time --</option>
                                    <?php foreach ($fixedTimeSlots as $start => $end): 
                                        $startDisplay = date('g:i A', strtotime($start));
                                    ?>
                                    <option value="<?php echo $start; ?>" <?php echo (($formData['lecStartTime'] ?? '') === $start) ? 'selected' : ''; ?>>
                                        <?php echo $startDisplay; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Lecture End Time</label>
                                <input type="text" id="lecEndTimeDisplay" readonly 
                                       value="<?php echo !empty($formData['lecEndTime']) ? date('g:i A', strtotime($formData['lecEndTime'])) : ''; ?>" 
                                       style="background-color: #e9ecef; cursor: not-allowed;" placeholder="Auto-filled">
                                <input type="hidden" name="lecEndTime" id="lecEndTimeValue" value="<?php echo htmlspecialchars($formData['lecEndTime'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <!-- Lecture Instructor -->
                        <div class="form-row">
                            <div class="form-group" style="position: relative;">
                                <label>Lecture Instructor</label>
                                <input type="text" id="lecInstructorSearchInput" 
                                       placeholder="Type to search instructors..."
                                       value="<?php echo htmlspecialchars($formData['lecInstructor'] ?? ''); ?>"
                                       autocomplete="off"
                                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                                <input type="hidden" name="lecInstructor" id="lecInstructorValue" value="<?php echo htmlspecialchars($formData['lecInstructor'] ?? ''); ?>">
                                <div id="lecInstructorDropdown" style="display: none; position: absolute; z-index: 1000; background: white; border: 1px solid #ddd; border-top: none; border-radius: 0 0 6px 6px; max-height: 250px; overflow-y: auto; width: 100%; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"></div>
                                <small style="color: #666; font-size: 12px;">Type to search and select lecture instructor</small>
                            </div>
                        </div>
                        
                        <!-- Lecture Room -->
                        <div class="form-row">
                            <div class="form-group" style="position: relative;">
                                <label>Lecture Room</label>
                                <input type="text" name="lecRoom" id="lecRoomSearchInput" 
                                       placeholder="-- Select days & time first --"
                                       value="<?php echo htmlspecialchars($formData['lecRoom'] ?? ''); ?>"
                                       autocomplete="off"
                                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                                <div id="lecRoomDropdown" style="display: none; position: absolute; z-index: 1000; background: white; border: 1px solid #ddd; border-top: none; border-radius: 0 0 6px 6px; max-height: 300px; overflow-y: auto; width: 100%; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"></div>
                                <div id="lecRoomAvailabilityStatus" style="margin-top: 8px; font-size: 13px;"></div>
                                <small style="color: #666; font-size: 12px;">Lecture rooms only (no computer labs)</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- LABORATORY Section -->
                    <div class="form-section" style="margin-bottom: 20px; padding: 16px; background: #fff8e1; border-radius: 8px; border-left: 4px solid #f9a825;">
                        <h4 style="color: #f57f17; margin-bottom: 12px; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">🖥️ Laboratory Schedule</h4>
                        
                        <!-- Lab Day Selection -->
                        <div class="form-group" style="margin-bottom: 16px;">
                            <label style="display: block; margin-bottom: 8px;">Laboratory Days</label>
                            <div id="labDayButtonsContainer" style="display: flex; flex-wrap: wrap; gap: 8px;">
                                <?php 
                                $labSelectedDays = isset($formData['labDays']) ? (is_array($formData['labDays']) ? $formData['labDays'] : explode('/', $formData['labDays'])) : [];
                                foreach ($days as $day): 
                                    $isSelected = in_array($day, $labSelectedDays) || in_array(strtolower($day), $labSelectedDays);
                                ?>
                                <button type="button" 
                                        class="day-btn lab-day-btn <?php echo $isSelected ? 'selected' : ''; ?>" 
                                        data-day="<?php echo $day; ?>"
                                        onclick="toggleDualDay(this, 'lab')"
                                        style="padding: 10px 16px; border: 2px solid <?php echo $isSelected ? '#f9a825' : '#ddd'; ?>; 
                                               background: <?php echo $isSelected ? '#f9a825' : '#fff'; ?>; 
                                               color: <?php echo $isSelected ? '#fff' : '#333'; ?>; 
                                               border-radius: 6px; cursor: pointer; font-weight: 500; transition: all 0.2s;">
                                    <?php echo $day; ?>
                                </button>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="labDays" id="labDaysInput" value="<?php echo htmlspecialchars($formData['labDays'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Laboratory Start Time</label>
                                <select name="labStartTime" id="labStartTimeSelect" onchange="autoFillDualEndTime('lab')">
                                    <option value="">-- Select Time --</option>
                                    <?php foreach ($fixedTimeSlots as $start => $end): 
                                        $startDisplay = date('g:i A', strtotime($start));
                                    ?>
                                    <option value="<?php echo $start; ?>" <?php echo (($formData['labStartTime'] ?? '') === $start) ? 'selected' : ''; ?>>
                                        <?php echo $startDisplay; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Laboratory End Time</label>
                                <input type="text" id="labEndTimeDisplay" readonly 
                                       value="<?php echo !empty($formData['labEndTime']) ? date('g:i A', strtotime($formData['labEndTime'])) : ''; ?>" 
                                       style="background-color: #e9ecef; cursor: not-allowed;" placeholder="Auto-filled">
                                <input type="hidden" name="labEndTime" id="labEndTimeValue" value="<?php echo htmlspecialchars($formData['labEndTime'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <!-- Lab Instructor -->
                        <div class="form-row">
                            <div class="form-group" style="position: relative;">
                                <label>Laboratory Instructor</label>
                                <input type="text" id="labInstructorSearchInput" 
                                       placeholder="Type to search instructors..."
                                       value="<?php echo htmlspecialchars($formData['labInstructor'] ?? ''); ?>"
                                       autocomplete="off"
                                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                                <input type="hidden" name="labInstructor" id="labInstructorValue" value="<?php echo htmlspecialchars($formData['labInstructor'] ?? ''); ?>">
                                <div id="labInstructorDropdown" style="display: none; position: absolute; z-index: 1000; background: white; border: 1px solid #ddd; border-top: none; border-radius: 0 0 6px 6px; max-height: 250px; overflow-y: auto; width: 100%; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"></div>
                                <small style="color: #666; font-size: 12px;">Type to search and select lab instructor</small>
                            </div>
                        </div>
                        
                        <!-- Lab Room -->
                        <div class="form-row">
                            <div class="form-group" style="position: relative;">
                                <label>Laboratory Room</label>
                                <input type="text" name="labRoom" id="labRoomSearchInput" 
                                       placeholder="-- Select days & time first --"
                                       value="<?php echo htmlspecialchars($formData['labRoom'] ?? ''); ?>"
                                       autocomplete="off"
                                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                                <div id="labRoomDropdown" style="display: none; position: absolute; z-index: 1000; background: white; border: 1px solid #ddd; border-top: none; border-radius: 0 0 6px 6px; max-height: 300px; overflow-y: auto; width: 100%; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"></div>
                                <div id="labRoomAvailabilityStatus" style="margin-top: 8px; font-size: 13px;"></div>
                                <small style="color: #666; font-size: 12px;">Computer Laboratory rooms only</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Regular Schedule Section (for non-CIT/CC courses) -->
                <div id="regularScheduleContainer">
                <!-- Schedule Time (Before Room Selection) -->
                <div class="form-section" style="margin-bottom: 20px; padding: 16px; background: #f8f9fa; border-radius: 8px;">
                    <h4 style="color: #555; margin-bottom: 12px; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">📅 Schedule Days & Time</h4>
                    <p style="color: #666; font-size: 13px; margin-bottom: 12px;">Select days and time slot for the schedule</p>
                    
                    <!-- Day Selection Buttons -->
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 8px;">Days</label>
                        <div id="dayButtonsContainer" style="display: flex; flex-wrap: wrap; gap: 8px;">
                            <?php 
                            $selectedDays = isset($formData['scheduleDays']) ? (is_array($formData['scheduleDays']) ? $formData['scheduleDays'] : explode('/', $formData['scheduleDays'])) : [];
                            foreach ($days as $day): 
                                $isSelected = in_array($day, $selectedDays) || in_array(strtolower($day), $selectedDays);
                            ?>
                            <button type="button" 
                                    class="day-btn <?php echo $isSelected ? 'selected' : ''; ?>" 
                                    data-day="<?php echo $day; ?>"
                                    onclick="toggleDay(this)"
                                    style="padding: 10px 16px; border: 2px solid <?php echo $isSelected ? '#28a745' : '#ddd'; ?>; 
                                           background: <?php echo $isSelected ? '#28a745' : '#fff'; ?>; 
                                           color: <?php echo $isSelected ? '#fff' : '#333'; ?>; 
                                           border-radius: 6px; cursor: pointer; font-weight: 500; transition: all 0.2s;">
                                <?php echo $day; ?>
                            </button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="scheduleDays" id="scheduleDaysInput" value="<?php echo htmlspecialchars(is_array($formData['scheduleDays'] ?? '') ? implode('/', $formData['scheduleDays']) : ($formData['scheduleDays'] ?? '')); ?>">
                        <small style="color: #666; font-size: 12px; margin-top: 6px; display: block;">Click to select/deselect days. At least one day is required.</small>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Start Time</label>
                            <select name="startTime" id="startTimeSelect" required onchange="autoFillEndTime()">
                                <option value="">-- Select Time --</option>
                                <?php foreach ($fixedTimeSlots as $start => $end): 
                                    $startDisplay = date('g:i A', strtotime($start));
                                ?>
                                <option value="<?php echo $start; ?>" <?php echo ($formData['startTime'] === $start) ? 'selected' : ''; ?>>
                                    <?php echo $startDisplay; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>End Time</label>
                            <input type="text" name="endTimeDisplay" id="endTimeDisplay" readonly required 
                                   value="<?php echo !empty($formData['endTime']) ? date('g:i A', strtotime($formData['endTime'])) : ''; ?>" 
                                   style="background-color: #e9ecef; cursor: not-allowed;"
                                   placeholder="Auto-filled">
                            <input type="hidden" name="endTime" id="endTimeValue" value="<?php echo htmlspecialchars($formData['endTime']); ?>">
                        </div>
                    </div>
                    
                    <!-- Instructor for Regular Schedule -->
                    <div class="form-row">
                        <div class="form-group" style="position: relative;">
                            <label>Instructor</label>
                            <input type="text" id="instructorSearchInput" 
                                   placeholder="Type to search instructors..."
                                   value="<?php echo htmlspecialchars($formData['instructor'] ?? ''); ?>"
                                   autocomplete="off"
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                            <input type="hidden" name="instructor" id="instructorValue" value="<?php echo htmlspecialchars($formData['instructor'] ?? ''); ?>">
                            <div id="instructorDropdown" style="display: none; position: absolute; z-index: 1000; background: white; border: 1px solid #ddd; border-top: none; border-radius: 0 0 6px 6px; max-height: 250px; overflow-y: auto; width: 100%; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"></div>
                            <small style="color: #666; font-size: 12px;">Type to search and select an instructor</small>
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
                </div><!-- End regularScheduleContainer -->
                
                <div class="form-actions">
                    <button type="submit" class="btn-primary"><?php echo $editMode ? 'Update Schedule' : 'Add Schedule'; ?></button>
                    <button type="button" class="btn-secondary" onclick="window.location.href='?page=schedule&view=overview'">Cancel</button>
                </div>
            </form>
        </div>
        <?php endif; ?>
        <!-- END ADD SCHEDULE VIEW -->

        <?php if ($currentView === 'overview'): ?>
        <!-- ========== PER BLOCK VIEW ========== -->
        <div id="blockView" style="margin-top: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="color: #333; font-size: 18px; margin: 0;">📦 Schedules by Block</h3>
                <?php if (!empty($schedules)): ?>
                <a href="?page=schedule&clear_all_schedules=confirm" class="btn-small btn-danger" 
                   onclick="return confirm('Are you sure you want to delete ALL schedules? This action cannot be undone!')">Clear All</a>
                <?php endif; ?>
            </div>

            <?php if (empty($schedules)): ?>
                <p style="text-align: center; color: #999;">No schedules available</p>
            <?php else: ?>
                <?php
                // Group schedules by year level first, then by block
                $blocksByYear = ['1st Year' => [], '2nd Year' => [], '3rd Year' => [], 'Other' => []];
                foreach ($schedules as $schedule) {
                    $year = $schedule['yearLevel'] ?? '';
                    if (empty($year)) {
                        $code = $schedule['classCode'] ?? '';
                        $year = $courseYearMap[$code] ?? 'Other';
                    }
                    $block = $schedule['block'] ?? 'Unassigned';
                    if (empty($block)) $block = 'Unassigned';
                    
                    if (!isset($blocksByYear[$year][$block])) {
                        $blocksByYear[$year][$block] = [
                            'schedules' => [],
                            'term' => $schedule['term'] ?? ''
                        ];
                    }
                    $blocksByYear[$year][$block]['schedules'][] = $schedule;
                }
                // Remove empty year groups
                $blocksByYear = array_filter($blocksByYear, function($blocks) { return !empty($blocks); });
                ?>

                <?php foreach ($blocksByYear as $yearLabel => $yearBlocks): 
                    // Sort blocks within each year
                    ksort($yearBlocks);
                    $totalBlocksInYear = count($yearBlocks);
                ?>
                <div style="margin-bottom: 28px;" data-year-block-section="<?php echo htmlspecialchars($yearLabel); ?>">
                    <h4 style="color: #1a73e8; font-size: 16px; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 2px solid #1a73e8;">
                        📚 <?php echo htmlspecialchars($yearLabel); ?>
                        <span style="font-size: 13px; color: #666; font-weight: normal; margin-left: 8px;">(<?php echo $totalBlocksInYear; ?> block<?php echo $totalBlocksInYear !== 1 ? 's' : ''; ?>)</span>
                    </h4>
                    
                    <div class="block-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px;">
                        <?php foreach ($yearBlocks as $blockName => $blockData): 
                            $blockSchedules = $blockData['schedules'];
                            $courseCount = count($blockSchedules);
                            $term = $blockData['term'];
                            
                            // Get unique instructors in this block
                            $blockInstructors = [];
                            foreach ($blockSchedules as $sched) {
                                if (!empty($sched['instructor']) && !in_array($sched['instructor'], $blockInstructors)) {
                                    $blockInstructors[] = $sched['instructor'];
                                }
                                if (!empty($sched['labInstructor']) && !in_array($sched['labInstructor'], $blockInstructors)) {
                                    $blockInstructors[] = $sched['labInstructor'];
                                }
                            }
                            
                            // Block card color
                            $blockColor = '#2e7d32';
                            
                            $blockId = 'block_' . preg_replace('/[^a-zA-Z0-9]/', '_', $blockName . '_' . $yearLabel);
                        ?>
                        <div class="block-card-wrapper">
                            <a href="?page=schedule&view=block_detail&block=<?php echo urlencode($blockName); ?>&year=<?php echo urlencode($yearLabel); ?>" style="text-decoration: none; color: inherit;">
                            <div class="block-card" 
                                 data-block="<?php echo htmlspecialchars($blockName); ?>"
                                 data-year="<?php echo htmlspecialchars($yearLabel); ?>"
                                 data-block-id="<?php echo $blockId; ?>"
                                 style="background: white; border: 1px solid #e0e0e0; border-left: 5px solid <?php echo $blockColor; ?>; border-radius: 10px; padding: 20px; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                    <h4 style="margin: 0; font-size: 20px; font-weight: 700; color: <?php echo $blockColor; ?>;">
                                        <?php echo htmlspecialchars($blockName); ?>
                                    </h4>
                                    <span style="background: <?php echo $blockColor; ?>; color: white; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                        <?php echo $courseCount; ?> course<?php echo $courseCount !== 1 ? 's' : ''; ?>
                                    </span>
                                </div>
                                
                                <?php if (!empty($term)): ?>
                                <div style="margin-bottom: 8px;">
                                    <span style="display: inline-block; background: #f3e5f5; color: #7b1fa2; padding: 3px 10px; border-radius: 4px; font-size: 12px; font-weight: 500;">
                                        <?php echo htmlspecialchars($term); ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                                
                                <p style="margin: 8px 0 0 0; font-size: 13px; color: #666;">
                                    <strong>Instructors:</strong> <?php echo count($blockInstructors); ?>
                                </p>
                                
                                <div class="block-expand-hint" style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #eee; font-size: 12px; color: #888; display: flex; justify-content: space-between; align-items: center;">
                                    <span>Click to view courses, times & details</span>
                                    <span style="font-size: 16px;">→</span>
                                </div>
                            </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <!-- END PER BLOCK VIEW -->

        <!-- ========== PER SCHEDULE VIEW ========== -->
        <div id="scheduleView" style="margin-top: 20px; display: none;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="color: #333; font-size: 18px; margin: 0;">📋 All Schedules</h3>
                <?php if (!empty($schedules)): ?>
                <a href="?page=schedule&clear_all_schedules=confirm" class="btn-small btn-danger" 
                   onclick="return confirm('Are you sure you want to delete ALL schedules? This action cannot be undone!')">Clear All</a>
                <?php endif; ?>
            </div>

            <?php if (empty($schedules)): ?>
                <p style="text-align: center; color: #999;">No schedules available</p>
            <?php else: ?>
                <?php
                // Group schedules by year level
                $schedulesByYear = ['1st Year' => [], '2nd Year' => [], '3rd Year' => [], 'Other' => []];
                foreach ($schedules as $schedule) {
                    // Use saved yearLevel if available, otherwise infer from classCode
                    $year = $schedule['yearLevel'] ?? '';
                    if (empty($year)) {
                        $code = $schedule['classCode'] ?? '';
                        $year = $courseYearMap[$code] ?? 'Other';
                    }
                    $schedulesByYear[$year][] = $schedule;
                }
                // Remove empty groups and 'Other' if empty
                $schedulesByYear = array_filter($schedulesByYear);
                ?>

                <?php foreach ($schedulesByYear as $yearLabel => $yearSchedules): ?>
                <div style="margin-bottom: 28px;" data-year-section="<?php echo htmlspecialchars($yearLabel); ?>">
                    <h4 style="color: #1a73e8; font-size: 16px; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 2px solid #1a73e8;">
                        📚 <?php echo htmlspecialchars($yearLabel); ?> Schedules
                        <span style="font-size: 13px; color: #666; font-weight: normal; margin-left: 8px;">(<?php echo count($yearSchedules); ?> class<?php echo count($yearSchedules) !== 1 ? 'es' : ''; ?>)</span>
                    </h4>
                    <div class="schedule-list">
                        <?php foreach ($yearSchedules as $schedule): 
                            // Format times to 12-hour AM/PM format
                            $startFormatted = date('g:i A', strtotime($schedule['startTime']));
                            $endFormatted = date('g:i A', strtotime($schedule['endTime']));
                            $hasConflict = !empty($schedule['hasConflict']);
                            $conflictStyle = $hasConflict ? 'border: 2px solid #dc3545; background: #fff5f5;' : '';
                            
                            // Check if CIT/CC course (by class code prefix)
                            $classCode = $schedule['classCode'] ?? '';
                            $isCITCCByCode = (strpos(strtoupper($classCode), 'CIT') === 0 || strpos(strtoupper($classCode), 'CC') === 0);
                            
                            // Get days for filtering
                            $scheduleDays = $schedule['days'] ?? '';
                            if (is_array($scheduleDays)) $scheduleDays = implode('/', $scheduleDays);
                            $labDays = $schedule['labDays'] ?? '';
                            if (is_array($labDays)) $labDays = implode('/', $labDays);
                            $allDays = !empty($labDays) ? $scheduleDays . '/' . $labDays : $scheduleDays;
                        ?>
                            <article class="schedule-item<?php echo $hasConflict ? ' has-conflict' : ''; ?><?php echo $isCITCCByCode ? ' citcc-schedule' : ''; ?>" 
                                     data-year="<?php echo htmlspecialchars($yearLabel); ?>"
                                     data-code="<?php echo htmlspecialchars($classCode); ?>"
                                     data-block="<?php echo htmlspecialchars($schedule['block'] ?? ''); ?>"
                                     data-days="<?php echo htmlspecialchars($allDays); ?>"
                                     data-instructor="<?php echo htmlspecialchars($schedule['instructor'] ?? ''); ?>"
                                     data-lab-instructor="<?php echo htmlspecialchars($schedule['labInstructor'] ?? ''); ?>"
                                     style="<?php echo $conflictStyle; ?>">
                                <?php if ($hasConflict): ?>
                                <div class="conflict-warning" style="background: #dc3545; color: white; padding: 6px 10px; font-size: 12px; border-radius: 4px 4px 0 0; margin: -12px -12px 10px -12px;">
                                    ⚠️ CONFLICT: <?php echo htmlspecialchars($schedule['conflictWith'] ?? 'Room already booked'); ?>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($isCITCCByCode): ?>
                                <!-- CIT/CC Combined Schedule Card -->
                                <div class="schedule-header" style="border-bottom: 1px solid #eee; padding-bottom: 12px; margin-bottom: 12px;">
                                    <div>
                                        <h4 style="margin: 0 0 6px 0; font-size: 18px; font-weight: 600; color: #333;"><?php echo htmlspecialchars($schedule['className']); ?></h4>
                                        <span style="font-size: 14px; color: #666;"><strong>Course Code:</strong> <?php echo htmlspecialchars($schedule['classCode'] ?? 'N/A'); ?></span>
                                        <?php if (!empty($schedule['block'])): ?>
                                        <span style="display: inline-block; background: #e3f2fd; color: #1565c0; padding: 3px 10px; border-radius: 4px; font-size: 12px; font-weight: 500; margin-left: 10px;"><?php echo htmlspecialchars($schedule['block']); ?></span>
                                        <?php else: ?>
                                        <span style="display: inline-block; background: #ffebee; color: #c62828; padding: 3px 10px; border-radius: 4px; font-size: 12px; font-weight: 500; margin-left: 10px;">No Block</span>
                                        <?php endif; ?>
                                        <?php if (!empty($schedule['term'])): ?>
                                        <span style="display: inline-block; background: #f3e5f5; color: #7b1fa2; padding: 3px 10px; border-radius: 4px; font-size: 12px; font-weight: 500; margin-left: 5px;"><?php echo htmlspecialchars($schedule['term']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="schedule-actions">
                                        <button class="btn-small" onclick="event.stopPropagation(); editSchedule(<?php echo $schedule['id']; ?>)" aria-label="Edit schedule">Edit</button>
                                        <a href="?page=schedule&delete_schedule=<?php echo $schedule['id']; ?>" class="btn-small btn-danger" onclick="event.stopPropagation(); return confirm('Are you sure you want to delete this schedule?')" aria-label="Delete schedule">Delete</a>
                                    </div>
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                    <!-- Lecture Section -->
                                    <div style="background: #f0f7ff; padding: 16px; border-radius: 8px; border-left: 4px solid #1976d2;">
                                        <h5 style="margin: 0 0 14px 0; color: #1976d2; font-size: 15px; font-weight: 600;">📚 Lecture</h5>
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                            <div>
                                                <div style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Time</div>
                                                <div style="font-size: 14px; color: #333;"><?php echo $startFormatted . ' - ' . $endFormatted; ?></div>
                                            </div>
                                            <div>
                                                <div style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Days</div>
                                                <div style="font-size: 14px; color: #333;"><?php 
                                                    $daysDisplay = $schedule['days'] ?? 'Not set';
                                                    if (is_array($daysDisplay)) $daysDisplay = implode('/', $daysDisplay);
                                                    echo htmlspecialchars($daysDisplay);
                                                ?></div>
                                            </div>
                                            <div>
                                                <div style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Room</div>
                                                <div style="font-size: 14px; color: #333;"><?php echo htmlspecialchars($schedule['room'] ?? 'N/A'); ?></div>
                                            </div>
                                            <div>
                                                <div style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Instructor</div>
                                                <div style="font-size: 14px; color: #333;"><?php echo htmlspecialchars($schedule['instructor'] ?? 'N/A'); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Laboratory Section -->
                                    <?php 
                                    $hasLabData = !empty($schedule['labRoom']) || !empty($schedule['labDays']) || !empty($schedule['labStartTime']);
                                    if ($hasLabData): 
                                        $labStartFormatted = !empty($schedule['labStartTime']) ? date('g:i A', strtotime($schedule['labStartTime'])) : 'N/A';
                                        $labEndFormatted = !empty($schedule['labEndTime']) ? date('g:i A', strtotime($schedule['labEndTime'])) : 'N/A';
                                        $labDaysDisplay = $schedule['labDays'] ?? 'Not set';
                                        if (is_array($labDaysDisplay)) $labDaysDisplay = implode('/', $labDaysDisplay);
                                    ?>
                                    <div style="background: #fff8e1; padding: 16px; border-radius: 8px; border-left: 4px solid #f9a825;">
                                        <h5 style="margin: 0 0 14px 0; color: #f57f17; font-size: 15px; font-weight: 600;">🖥️ Laboratory</h5>
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                            <div>
                                                <div style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Time</div>
                                                <div style="font-size: 14px; color: #333;"><?php echo $labStartFormatted . ' - ' . $labEndFormatted; ?></div>
                                            </div>
                                            <div>
                                                <div style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Days</div>
                                                <div style="font-size: 14px; color: #333;"><?php echo htmlspecialchars($labDaysDisplay); ?></div>
                                            </div>
                                            <div>
                                                <div style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Room</div>
                                                <div style="font-size: 14px; color: #333;"><?php echo htmlspecialchars($schedule['labRoom'] ?? 'N/A'); ?></div>
                                            </div>
                                            <div>
                                                <div style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Instructor</div>
                                                <div style="font-size: 14px; color: #333;"><?php echo htmlspecialchars($schedule['labInstructor'] ?? 'N/A'); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php else: ?>
                                    <div style="background: #fff8e1; padding: 16px; border-radius: 8px; border-left: 4px solid #f9a825; opacity: 0.7;">
                                        <h5 style="margin: 0 0 14px 0; color: #f57f17; font-size: 15px; font-weight: 600;">🖥️ Laboratory</h5>
                                        <div style="font-size: 14px; color: #888; text-align: center; padding: 20px 0;">
                                            <em>Lab schedule not set</em><br>
                                            <small>Edit to add laboratory schedule</small>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php else: ?>
                                <!-- Regular Schedule Card -->
                                <div class="schedule-header" style="border-bottom: 1px solid #eee; padding-bottom: 12px; margin-bottom: 12px;">
                                    <div>
                                        <h4 style="margin: 0 0 6px 0; font-size: 18px; font-weight: 600; color: #333;"><?php echo htmlspecialchars($schedule['className']); ?></h4>
                                        <span style="font-size: 14px; color: #666;"><strong>Course Code:</strong> <?php echo htmlspecialchars($schedule['classCode'] ?? 'N/A'); ?></span>
                                        <?php if (!empty($schedule['block'])): ?>
                                        <span style="display: inline-block; background: #e8f5e9; color: #2e7d32; padding: 3px 10px; border-radius: 4px; font-size: 12px; font-weight: 500; margin-left: 10px;"><?php echo htmlspecialchars($schedule['block']); ?></span>
                                        <?php else: ?>
                                        <span style="display: inline-block; background: #ffebee; color: #c62828; padding: 3px 10px; border-radius: 4px; font-size: 12px; font-weight: 500; margin-left: 10px;">No Block</span>
                                        <?php endif; ?>
                                        <?php if (!empty($schedule['term'])): ?>
                                        <span style="display: inline-block; background: #f3e5f5; color: #7b1fa2; padding: 3px 10px; border-radius: 4px; font-size: 12px; font-weight: 500; margin-left: 5px;"><?php echo htmlspecialchars($schedule['term']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="schedule-actions">
                                        <button class="btn-small" onclick="event.stopPropagation(); editSchedule(<?php echo $schedule['id']; ?>)" aria-label="Edit schedule">Edit</button>
                                        <a href="?page=schedule&delete_schedule=<?php echo $schedule['id']; ?>" class="btn-small btn-danger" onclick="event.stopPropagation(); return confirm('Are you sure you want to delete this schedule?')" aria-label="Delete schedule">Delete</a>
                                    </div>
                                </div>
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
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px 24px; font-size: 15px; line-height: 1.8;">
                                    <p style="margin: 0;"><strong>Time:</strong> <?php echo $startFormatted . ' - ' . $endFormatted; ?></p>
                                    <p style="margin: 0;"><strong>Room:</strong> <?php echo htmlspecialchars($schedule['room']); ?></p>
                                    <?php if (!empty($roomBuildingInfo)): ?>
                                    <p style="margin: 0;"><strong>Location:</strong> <?php echo htmlspecialchars($roomBuildingInfo); ?></p>
                                    <?php endif; ?>
                                    <p style="margin: 0;"><strong>Instructor:</strong> <?php echo htmlspecialchars($schedule['instructor']); ?></p>
                                    <p style="margin: 0; grid-column: 1 / -1;"><strong>Days:</strong> <?php 
                                        $daysDisplay = $schedule['days'] ?? 'Not set';
                                        if (is_array($daysDisplay)) {
                                            $daysDisplay = implode('/', $daysDisplay);
                                        }
                                        echo htmlspecialchars($daysDisplay);
                                    ?></p>
                                </div>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <!-- END OVERVIEW: Schedule View -->

        <?php if ($currentView === 'block_detail' && !empty($viewBlock)): ?>
        <!-- ========== BLOCK DETAIL VIEW ========== -->
        <?php
        // Get schedules for this specific block
        $blockDetailSchedules = [];
        foreach ($schedules as $sched) {
            $schedYear = $sched['yearLevel'] ?? '';
            if (empty($schedYear)) {
                $code = $sched['classCode'] ?? '';
                $schedYear = $courseYearMap[$code] ?? 'Other';
            }
            $schedBlock = $sched['block'] ?? 'Unassigned';
            if ($schedBlock === $viewBlock && $schedYear === $viewYear) {
                $blockDetailSchedules[] = $sched;
            }
        }
        // Sort by time
        usort($blockDetailSchedules, function($a, $b) {
            return ($a['startTime'] ?? '00:00') <=> ($b['startTime'] ?? '00:00');
        });
        $blockCourseCount = count($blockDetailSchedules);
        $blockTerm = !empty($blockDetailSchedules) ? ($blockDetailSchedules[0]['term'] ?? '') : '';
        ?>
        
        <div style="margin-bottom: 20px;">
            <a href="?page=schedule&view=overview" class="btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none;">
                ← Back to Schedule Overview
            </a>
        </div>
        
        <div style="background: #2e7d32; color: white; padding: 20px 24px; border-radius: 8px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="margin: 0; font-size: 24px; color: white;"><?php echo htmlspecialchars($viewBlock); ?></h2>
                    <p style="margin: 8px 0 0 0; opacity: 0.9; font-size: 14px;">
                        <?php echo htmlspecialchars($viewYear); ?> • <?php echo $blockCourseCount; ?> course<?php echo $blockCourseCount !== 1 ? 's' : ''; ?>
                        <?php if (!empty($blockTerm)): ?> • <?php echo htmlspecialchars($blockTerm); ?><?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        
        <?php if (empty($blockDetailSchedules)): ?>
            <p style="text-align: center; color: #999; padding: 40px;">No schedules found for this block</p>
        <?php else: ?>
            <div class="schedule-list">
                <?php foreach ($blockDetailSchedules as $sched): 
                    $startFormatted = date('g:i A', strtotime($sched['startTime']));
                    $endFormatted = date('g:i A', strtotime($sched['endTime']));
                    $isCITCC = (strpos(strtoupper($sched['classCode'] ?? ''), 'CIT') === 0 || strpos(strtoupper($sched['classCode'] ?? ''), 'CC') === 0);
                    $daysDisplay = $sched['days'] ?? 'Not set';
                    if (is_array($daysDisplay)) $daysDisplay = implode('/', $daysDisplay);
                    
                    // Get room info with building/floor
                    $roomBuildingInfo = '';
                    $roomData = null;
                    if (!empty($sched['roomId'])) {
                        $roomData = getRoomById($sched['roomId']);
                    }
                    if (!$roomData && !empty($sched['room'])) {
                        $allRoomOptions = getRoomsAsOptions();
                        foreach ($allRoomOptions as $roomOpt) {
                            if ($roomOpt['label'] === $sched['room']) {
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
                <article class="schedule-item<?php echo $isCITCC ? ' citcc-schedule' : ''; ?>">
                    <!-- Header: Title + Actions -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px;">
                        <h4 style="margin: 0; font-size: 18px; font-weight: 600; color: #333;"><?php echo htmlspecialchars($sched['className'] ?? 'N/A'); ?></h4>
                        <div class="schedule-actions" style="display: flex; gap: 6px; flex-shrink: 0;">
                            <button class="btn-small" onclick="editSchedule(<?php echo $sched['id']; ?>)" aria-label="Edit schedule">Edit</button>
                            <a href="?page=schedule&delete_schedule=<?php echo $sched['id']; ?>&return_block=<?php echo urlencode($viewBlock); ?>&return_year=<?php echo urlencode($viewYear); ?>" class="btn-small btn-danger" onclick="return confirm('Are you sure you want to delete this schedule?')" aria-label="Delete schedule">Delete</a>
                        </div>
                    </div>
                    
                    <!-- Course Code + Block + Term -->
                    <div style="margin-bottom: 16px;">
                        <span style="font-size: 14px; color: #666;"><strong>Course Code:</strong> <?php echo htmlspecialchars($sched['classCode'] ?? 'N/A'); ?></span>
                        <?php if (!empty($sched['block'])): ?>
                        <span style="display: inline-block; background: #28a745; color: white; padding: 2px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; margin-left: 10px;"><?php echo htmlspecialchars($sched['block']); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($sched['term'])): ?>
                        <span style="display: inline-block; background: #dc3545; color: white; padding: 2px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; margin-left: 5px;"><?php echo htmlspecialchars($sched['term']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($isCITCC): ?>
                    <!-- CIT/CC Course: Lecture + Laboratory side by side -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <!-- Lecture Section -->
                        <div style="background: #e8f5e9; padding: 16px; border-radius: 8px; border-left: 4px solid #2e7d32;">
                            <h5 style="margin: 0 0 14px 0; color: #2e7d32; font-size: 15px; font-weight: 600;">📚 Lecture</h5>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <div>
                                    <div style="font-size: 11px; color: #666; font-weight: 600; text-transform: uppercase; margin-bottom: 3px;">Time</div>
                                    <div style="font-size: 13px; color: #333;"><?php echo $startFormatted . ' - ' . $endFormatted; ?></div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #666; font-weight: 600; text-transform: uppercase; margin-bottom: 3px;">Days</div>
                                    <div style="font-size: 13px; color: #333;"><?php echo htmlspecialchars($daysDisplay); ?></div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #666; font-weight: 600; text-transform: uppercase; margin-bottom: 3px;">Room</div>
                                    <div style="font-size: 13px; color: #333;"><?php echo htmlspecialchars($sched['room'] ?? 'N/A'); ?></div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #666; font-weight: 600; text-transform: uppercase; margin-bottom: 3px;">Instructor</div>
                                    <div style="font-size: 13px; color: #333;"><?php echo htmlspecialchars($sched['instructor'] ?? 'N/A'); ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Laboratory Section -->
                        <?php 
                        $hasLabData = !empty($sched['labRoom']) || !empty($sched['labDays']) || !empty($sched['labStartTime']);
                        if ($hasLabData): 
                            $labStartFormatted = !empty($sched['labStartTime']) ? date('g:i A', strtotime($sched['labStartTime'])) : 'N/A';
                            $labEndFormatted = !empty($sched['labEndTime']) ? date('g:i A', strtotime($sched['labEndTime'])) : 'N/A';
                            $labDaysDisplay = $sched['labDays'] ?? 'Not set';
                            if (is_array($labDaysDisplay)) $labDaysDisplay = implode('/', $labDaysDisplay);
                        ?>
                        <div style="background: #fff8e1; padding: 16px; border-radius: 8px; border-left: 4px solid #f9a825;">
                            <h5 style="margin: 0 0 14px 0; color: #f57f17; font-size: 15px; font-weight: 600;">🖥️ Laboratory</h5>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <div>
                                    <div style="font-size: 11px; color: #666; font-weight: 600; text-transform: uppercase; margin-bottom: 3px;">Time</div>
                                    <div style="font-size: 13px; color: #333;"><?php echo $labStartFormatted . ' - ' . $labEndFormatted; ?></div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #666; font-weight: 600; text-transform: uppercase; margin-bottom: 3px;">Days</div>
                                    <div style="font-size: 13px; color: #333;"><?php echo htmlspecialchars($labDaysDisplay); ?></div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #666; font-weight: 600; text-transform: uppercase; margin-bottom: 3px;">Room</div>
                                    <div style="font-size: 13px; color: #333;"><?php echo htmlspecialchars($sched['labRoom'] ?? 'N/A'); ?></div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #666; font-weight: 600; text-transform: uppercase; margin-bottom: 3px;">Instructor</div>
                                    <div style="font-size: 13px; color: #333;"><?php echo htmlspecialchars($sched['labInstructor'] ?? 'N/A'); ?></div>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div style="background: #fff8e1; padding: 16px; border-radius: 8px; border-left: 4px solid #f9a825; opacity: 0.7;">
                            <h5 style="margin: 0 0 14px 0; color: #f57f17; font-size: 15px; font-weight: 600;">🖥️ Laboratory</h5>
                            <div style="font-size: 13px; color: #888; text-align: center; padding: 20px 0;">
                                <em>Lab schedule not set</em><br>
                                <small>Edit to add laboratory schedule</small>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php else: ?>
                    <!-- Regular Course: Two column layout -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px 40px; font-size: 14px; line-height: 1.9;">
                        <p style="margin: 0;"><strong>Time:</strong> <?php echo $startFormatted . ' - ' . $endFormatted; ?></p>
                        <p style="margin: 0;"><strong>Room:</strong> <?php echo htmlspecialchars($sched['room'] ?? 'N/A'); ?></p>
                        <?php if (!empty($roomBuildingInfo)): ?>
                        <p style="margin: 0;"><strong>Location:</strong> <?php echo htmlspecialchars($roomBuildingInfo); ?></p>
                        <p style="margin: 0;"><strong>Instructor:</strong> <?php echo htmlspecialchars($sched['instructor'] ?? 'N/A'); ?></p>
                        <?php else: ?>
                        <p style="margin: 0;"><strong>Instructor:</strong> <?php echo htmlspecialchars($sched['instructor'] ?? 'N/A'); ?></p>
                        <p style="margin: 0;"></p>
                        <?php endif; ?>
                        <p style="margin: 0;"><strong>Days:</strong> <?php echo htmlspecialchars($daysDisplay); ?></p>
                    </div>
                    <?php endif; ?>
                </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <!-- END BLOCK DETAIL VIEW -->

        <?php endif; ?>
    </div>
</section>

<!-- Block Schedule Overview View (Hidden by default) -->
<section id="blockOverviewSection" class="tab-content" style="display: none;">
    <div class="card">
        <div style="margin-bottom: 20px;">
            <button onclick="backToScheduleOverview()" class="btn-secondary" style="display: inline-flex; align-items: center; gap: 8px;">
                ← Back to Schedule Overview
            </button>
        </div>
        <div style="background: #1c361d; color: white; padding: 20px 24px; border-radius: 8px; margin-bottom: 20px;">
            <h2 id="blockOverviewTitle" style="margin: 0; font-size: 22px; color: white;">Block Schedule</h2>
            <p id="blockOverviewSubtitle" style="margin: 8px 0 0 0; opacity: 0.9; font-size: 14px;">All classes for this block</p>
        </div>
        <div id="blockOverviewContent">
            <!-- Content will be populated by JavaScript -->
        </div>
    </div>
</section>

<script>
// Store all schedules data for modal
const allSchedulesData = <?php echo json_encode($schedules); ?>;

function showBlockSchedule(block, e) {
    if (e) e.stopPropagation(); // Prevent bubbling
    
    if (!block) {
        alert('This schedule has no block assigned. Edit the schedule to assign a block.');
        return;
    }
    
    // Filter schedules by block
    const blockSchedules = allSchedulesData.filter(s => s.block === block);
    
    if (blockSchedules.length === 0) {
        return;
    }
    
    // Sort by time
    blockSchedules.sort((a, b) => {
        const timeA = a.startTime || '00:00';
        const timeB = b.startTime || '00:00';
        return timeA.localeCompare(timeB);
    });
    
    // Update block overview title
    document.getElementById('blockOverviewTitle').textContent = `Block ${block} Schedule`;
    document.getElementById('blockOverviewSubtitle').textContent = `${blockSchedules.length} class${blockSchedules.length !== 1 ? 'es' : ''} in this block`;
    
    // Build content HTML
    let html = '<div style="display: flex; flex-direction: column; gap: 16px;">';
    
    blockSchedules.forEach((schedule, index) => {
        const startTime = schedule.startTime ? formatTime(schedule.startTime) : 'N/A';
        const endTime = schedule.endTime ? formatTime(schedule.endTime) : 'N/A';
        const days = Array.isArray(schedule.days) ? schedule.days.join('/') : (schedule.days || 'N/A');
        
        const isCITCC = schedule.classCode && (schedule.classCode.toUpperCase().startsWith('CIT') || schedule.classCode.toUpperCase().startsWith('CC'));
        const borderColor = isCITCC ? '#1976d2' : '#1c361d';
        
        html += `
        <div style="background: #f8f9fa; border-radius: 8px; border-left: 4px solid ${borderColor}; padding: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                <div>
                    <h4 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600; color: #333;">${escapeHtml(schedule.className || 'N/A')}</h4>
                    <span style="font-size: 13px; color: #666;"><strong>Course Code:</strong> ${escapeHtml(schedule.classCode || 'N/A')}</span>
                </div>
                <span style="background: #e8f5e9; color: #2e7d32; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;">#${index + 1}</span>
            </div>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px 20px; font-size: 14px;">
                <p style="margin: 0;"><strong>Time:</strong> ${startTime} - ${endTime}</p>
                <p style="margin: 0;"><strong>Days:</strong> ${escapeHtml(days)}</p>
                <p style="margin: 0;"><strong>Room:</strong> ${escapeHtml(schedule.room || 'N/A')}</p>
                <p style="margin: 0;"><strong>Instructor:</strong> ${escapeHtml(schedule.instructor || 'N/A')}</p>
            </div>`;
        
        // Add lab info if it's a CIT/CC course with lab data
        if (isCITCC && (schedule.labRoom || schedule.labDays || schedule.labStartTime)) {
            const labStartTime = schedule.labStartTime ? formatTime(schedule.labStartTime) : 'N/A';
            const labEndTime = schedule.labEndTime ? formatTime(schedule.labEndTime) : 'N/A';
            const labDays = Array.isArray(schedule.labDays) ? schedule.labDays.join('/') : (schedule.labDays || 'N/A');
            
            html += `
            <div style="margin-top: 12px; padding-top: 12px; border-top: 1px dashed #ddd;">
                <span style="font-size: 12px; font-weight: 600; color: #f57f17; text-transform: uppercase;">Laboratory</span>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px 20px; font-size: 14px; margin-top: 8px;">
                    <p style="margin: 0;"><strong>Time:</strong> ${labStartTime} - ${labEndTime}</p>
                    <p style="margin: 0;"><strong>Days:</strong> ${escapeHtml(labDays)}</p>
                    <p style="margin: 0;"><strong>Room:</strong> ${escapeHtml(schedule.labRoom || 'N/A')}</p>
                    <p style="margin: 0;"><strong>Instructor:</strong> ${escapeHtml(schedule.labInstructor || 'N/A')}</p>
                </div>
            </div>`;
        }
        
        html += '</div>';
    });
    
    html += '</div>';
    
    document.getElementById('blockOverviewContent').innerHTML = html;
    
    // Hide the main schedule section and show block overview
    document.getElementById('schedule').style.display = 'none';
    document.getElementById('blockOverviewSection').style.display = 'block';
    
    // Scroll to top
    window.scrollTo(0, 0);
}

function backToScheduleOverview() {
    // Hide block overview and show main schedule section
    document.getElementById('blockOverviewSection').style.display = 'none';
    document.getElementById('schedule').style.display = 'block';
}

function formatTime(timeStr) {
    if (!timeStr) return 'N/A';
    const [hours, minutes] = timeStr.split(':');
    const h = parseInt(hours);
    const ampm = h >= 12 ? 'PM' : 'AM';
    const hour12 = h % 12 || 12;
    return `${hour12}:${minutes} ${ampm}`;
}

// Handle browser back button for block overview
window.addEventListener('popstate', function(e) {
    if (document.getElementById('blockOverviewSection').style.display !== 'none') {
        backToScheduleOverview();
    }
});

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

// ===== SCHEDULE FILTERS =====
function applyFilters() {
    const yearFilter = document.getElementById('filterYear')?.value || 'all';
    const typeFilter = document.getElementById('filterType')?.value || 'all';
    const dayFilter = document.getElementById('filterDay')?.value || 'all';
    const blockFilter = document.getElementById('filterBlock')?.value || 'all';
    const instructorFilter = document.getElementById('filterInstructor')?.value || 'all';
    
    // Get all schedule items and year sections
    const scheduleItems = document.querySelectorAll('.schedule-item');
    const yearSections = document.querySelectorAll('[data-year-section]');
    
    scheduleItems.forEach(item => {
        let show = true;
        
        // Year filter
        if (yearFilter !== 'all') {
            const itemYear = item.getAttribute('data-year') || '';
            if (itemYear !== yearFilter) {
                show = false;
            }
        }
        
        // Block filter
        if (show && blockFilter !== 'all') {
            const itemBlock = item.getAttribute('data-block') || '';
            if (itemBlock !== blockFilter) {
                show = false;
            }
        }
        
        // Course type filter
        if (show && typeFilter !== 'all') {
            const code = item.getAttribute('data-code') || '';
            const upperCode = code.toUpperCase();
            
            if (typeFilter === 'citcc') {
                if (!upperCode.startsWith('CIT') && !upperCode.startsWith('CC')) {
                    show = false;
                }
            } else if (typeFilter === 'gened') {
                // Gen Ed courses (not CIT/CC and not PE/NSTP)
                const isPE = upperCode.startsWith('PE') || upperCode.startsWith('PATHFIT') || upperCode.startsWith('NSTP');
                const isCITCC = upperCode.startsWith('CIT') || upperCode.startsWith('CC');
                if (isPE || isCITCC) {
                    show = false;
                }
            } else if (typeFilter === 'pe') {
                const isPE = upperCode.startsWith('PE') || upperCode.startsWith('PATHFIT') || upperCode.startsWith('NSTP');
                if (!isPE) {
                    show = false;
                }
            }
        }
        
        // Day filter
        if (show && dayFilter !== 'all') {
            const itemDays = (item.getAttribute('data-days') || '').toLowerCase();
            if (!itemDays.includes(dayFilter)) {
                show = false;
            }
        }
        
        // Instructor filter
        if (show && instructorFilter !== 'all') {
            const itemInstructor = item.getAttribute('data-instructor') || '';
            const itemLabInstructor = item.getAttribute('data-lab-instructor') || '';
            if (itemInstructor !== instructorFilter && itemLabInstructor !== instructorFilter) {
                show = false;
            }
        }
        
        item.style.display = show ? '' : 'none';
    });
    
    // Update year section visibility based on visible children
    yearSections.forEach(section => {
        const sectionYear = section.getAttribute('data-year-section');
        const visibleItems = section.querySelectorAll('.schedule-item:not([style*="display: none"])');
        
        // If year filter is set and doesn't match this section, hide it
        if (yearFilter !== 'all' && sectionYear !== yearFilter) {
            section.style.display = 'none';
        } else if (visibleItems.length === 0) {
            section.style.display = 'none';
        } else {
            section.style.display = '';
            // Update count in section header
            const countSpan = section.querySelector('h4 span');
            if (countSpan) {
                countSpan.textContent = `(${visibleItems.length} class${visibleItems.length !== 1 ? 'es' : ''})`;
            }
        }
    });
}

function resetFilters() {
    document.getElementById('filterYear').value = 'all';
    document.getElementById('filterType').value = 'all';
    document.getElementById('filterDay').value = 'all';
    document.getElementById('filterBlock').value = 'all';
    document.getElementById('filterInstructor').value = 'all';
    applyFilters();
    applyBlockFilters();
}

// ===== VIEW MODE SWITCHING =====
let currentViewMode = 'block'; // Default to 'block' view

function setViewMode(mode) {
    currentViewMode = mode;
    
    const blockView = document.getElementById('blockView');
    const scheduleView = document.getElementById('scheduleView');
    const blockBtn = document.getElementById('viewModeBlock');
    const scheduleBtn = document.getElementById('viewModeSchedule');
    
    if (mode === 'block') {
        blockView.style.display = 'block';
        scheduleView.style.display = 'none';
        blockBtn.style.background = '#1c361d';
        blockBtn.style.color = '#fff';
        scheduleBtn.style.background = '#fff';
        scheduleBtn.style.color = '#1c361d';
        blockBtn.classList.add('active');
        scheduleBtn.classList.remove('active');
    } else {
        blockView.style.display = 'none';
        scheduleView.style.display = 'block';
        scheduleBtn.style.background = '#1c361d';
        scheduleBtn.style.color = '#fff';
        blockBtn.style.background = '#fff';
        blockBtn.style.color = '#1c361d';
        scheduleBtn.classList.add('active');
        blockBtn.classList.remove('active');
    }
    
    // Save preference to localStorage
    localStorage.setItem('scheduleViewMode', mode);
}

// Restore view mode preference on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedMode = localStorage.getItem('scheduleViewMode') || 'block';
    setViewMode(savedMode);
});

// ===== BLOCK DETAILS MODAL =====
function showBlockDetails(blockName) {
    // Filter schedules by block
    const blockSchedules = allSchedulesData.filter(s => {
        const schedBlock = s.block || 'Unassigned';
        return schedBlock === blockName || (blockName === 'Unassigned' && !s.block);
    });
    
    if (blockSchedules.length === 0) {
        return;
    }
    
    // Sort by time
    blockSchedules.sort((a, b) => {
        const timeA = a.startTime || '00:00';
        const timeB = b.startTime || '00:00';
        return timeA.localeCompare(timeB);
    });
    
    // Get block info
    const yearLevel = blockSchedules[0]?.yearLevel || '';
    const term = blockSchedules[0]?.term || '';
    
    // Create and show modal
    let modalHtml = `
    <div id="blockDetailsModal" class="block-modal-overlay" onclick="closeBlockModal(event)">
        <div class="block-modal-content" onclick="event.stopPropagation()">
            <div class="block-modal-header">
                <div>
                    <h2 style="margin: 0; font-size: 24px; color: white;">${escapeHtml(blockName)}</h2>
                    <p style="margin: 6px 0 0 0; opacity: 0.9; font-size: 14px;">
                        ${blockSchedules.length} course${blockSchedules.length !== 1 ? 's' : ''}
                        ${yearLevel ? ' • ' + escapeHtml(yearLevel) : ''}
                        ${term ? ' • ' + escapeHtml(term) : ''}
                    </p>
                </div>
                <button onclick="closeBlockModal()" class="block-modal-close">&times;</button>
            </div>
            <div class="block-modal-body">`;
    
    blockSchedules.forEach((schedule, index) => {
        const startTime = schedule.startTime ? formatTime(schedule.startTime) : 'N/A';
        const endTime = schedule.endTime ? formatTime(schedule.endTime) : 'N/A';
        const days = Array.isArray(schedule.days) ? schedule.days.join('/') : (schedule.days || 'N/A');
        
        const isCITCC = schedule.classCode && (schedule.classCode.toUpperCase().startsWith('CIT') || schedule.classCode.toUpperCase().startsWith('CC'));
        const cardColor = isCITCC ? '#1976d2' : '#1c361d';
        
        modalHtml += `
            <div class="block-course-card" style="border-left-color: ${cardColor};">
                <div class="block-course-header">
                    <div>
                        <h4 class="block-course-title">${escapeHtml(schedule.className || 'N/A')}</h4>
                        <span class="block-course-code">${escapeHtml(schedule.classCode || 'N/A')}</span>
                    </div>
                    <span class="block-course-number">#${index + 1}</span>
                </div>
                <div class="block-course-details">
                    <div class="block-course-detail">
                        <span class="block-detail-label">⏰ Time</span>
                        <span class="block-detail-value">${startTime} - ${endTime}</span>
                    </div>
                    <div class="block-course-detail">
                        <span class="block-detail-label">📅 Days</span>
                        <span class="block-detail-value">${escapeHtml(days)}</span>
                    </div>
                    <div class="block-course-detail">
                        <span class="block-detail-label">🏫 Room</span>
                        <span class="block-detail-value">${escapeHtml(schedule.room || 'N/A')}</span>
                    </div>
                    <div class="block-course-detail">
                        <span class="block-detail-label">👨‍🏫 Instructor</span>
                        <span class="block-detail-value">${escapeHtml(schedule.instructor || 'N/A')}</span>
                    </div>
                </div>`;
        
        // Add lab info if it's a CIT/CC course with lab data
        if (isCITCC && (schedule.labRoom || schedule.labDays || schedule.labStartTime)) {
            const labStartTime = schedule.labStartTime ? formatTime(schedule.labStartTime) : 'N/A';
            const labEndTime = schedule.labEndTime ? formatTime(schedule.labEndTime) : 'N/A';
            const labDays = Array.isArray(schedule.labDays) ? schedule.labDays.join('/') : (schedule.labDays || 'N/A');
            
            modalHtml += `
                <div class="block-lab-section">
                    <span class="block-lab-label">🖥️ Laboratory</span>
                    <div class="block-course-details" style="margin-top: 8px;">
                        <div class="block-course-detail">
                            <span class="block-detail-label">⏰ Time</span>
                            <span class="block-detail-value">${labStartTime} - ${labEndTime}</span>
                        </div>
                        <div class="block-course-detail">
                            <span class="block-detail-label">📅 Days</span>
                            <span class="block-detail-value">${escapeHtml(labDays)}</span>
                        </div>
                        <div class="block-course-detail">
                            <span class="block-detail-label">🏫 Room</span>
                            <span class="block-detail-value">${escapeHtml(schedule.labRoom || 'N/A')}</span>
                        </div>
                        <div class="block-course-detail">
                            <span class="block-detail-label">👨‍🏫 Instructor</span>
                            <span class="block-detail-value">${escapeHtml(schedule.labInstructor || 'N/A')}</span>
                        </div>
                    </div>
                </div>`;
        }
        
        modalHtml += '</div>';
    });
    
    modalHtml += `
            </div>
        </div>
    </div>`;
    
    // Insert modal into DOM
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Add escape key listener
    document.addEventListener('keydown', handleModalEscape);
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

function closeBlockModal(event) {
    if (event && event.target !== event.currentTarget) return;
    
    const modal = document.getElementById('blockDetailsModal');
    if (modal) {
        modal.remove();
    }
    
    document.removeEventListener('keydown', handleModalEscape);
    document.body.style.overflow = '';
}

function handleModalEscape(e) {
    if (e.key === 'Escape') {
        closeBlockModal();
    }
}

// ===== TOGGLE BLOCK EXPAND (Inline) =====
function toggleBlockExpand(blockId) {
    const expandedContent = document.getElementById(blockId);
    const blockCard = document.querySelector(`[data-block-id="${blockId}"]`);
    
    if (!expandedContent || !blockCard) return;
    
    const isExpanded = expandedContent.style.display !== 'none';
    const expandIcon = blockCard.querySelector('.block-expand-icon');
    
    if (isExpanded) {
        // Collapse
        expandedContent.style.display = 'none';
        blockCard.style.borderRadius = '10px';
        if (expandIcon) expandIcon.style.transform = 'rotate(0deg)';
    } else {
        // Expand
        expandedContent.style.display = 'block';
        blockCard.style.borderRadius = '10px 10px 0 0';
        if (expandIcon) expandIcon.style.transform = 'rotate(180deg)';
        
        // Scroll the expanded content into view
        setTimeout(() => {
            expandedContent.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 100);
    }
}

// ===== BLOCK VIEW FILTERS =====
function applyBlockFilters() {
    const yearFilter = document.getElementById('filterYear')?.value || 'all';
    const blockFilter = document.getElementById('filterBlock')?.value || 'all';
    
    // Filter block card wrappers
    const blockWrappers = document.querySelectorAll('.block-card-wrapper');
    const yearBlockSections = document.querySelectorAll('[data-year-block-section]');
    
    blockWrappers.forEach(wrapper => {
        const card = wrapper.querySelector('.block-card');
        if (!card) return;
        
        let show = true;
        
        // Year filter
        if (yearFilter !== 'all') {
            const cardYear = card.getAttribute('data-year') || '';
            if (cardYear !== yearFilter) {
                show = false;
            }
        }
        
        // Block filter
        if (blockFilter !== 'all') {
            const cardBlock = card.getAttribute('data-block') || '';
            if (cardBlock !== blockFilter) {
                show = false;
            }
        }
        
        wrapper.style.display = show ? '' : 'none';
    });
    
    // Update year section visibility
    yearBlockSections.forEach(section => {
        const sectionYear = section.getAttribute('data-year-block-section');
        const visibleWrappers = section.querySelectorAll('.block-card-wrapper:not([style*="display: none"])');
        
        if (yearFilter !== 'all' && sectionYear !== yearFilter) {
            section.style.display = 'none';
        } else if (visibleWrappers.length === 0) {
            section.style.display = 'none';
        } else {
            section.style.display = '';
            // Update count in section header
            const countSpan = section.querySelector('h4 span');
            if (countSpan) {
                countSpan.textContent = `(${visibleWrappers.length} block${visibleWrappers.length !== 1 ? 's' : ''})`;
            }
        }
    });
}

// Override applyFilters to also filter block view
const originalApplyFilters = applyFilters;
applyFilters = function() {
    originalApplyFilters();
    applyBlockFilters();
};

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
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

// ===== CLASS CODE AUTO-FILL =====
const curriculumCourses = <?php echo json_encode($curriculumCourses); ?>;
const coursesByYear = <?php echo json_encode($coursesByYear); ?>;
const coursesByYearAndSem = <?php echo json_encode($coursesByYearAndSem); ?>;

// Store initial form values for restoration after dynamic population
const initialFormData = {
    block: '<?php echo addslashes($formData['block'] ?? ''); ?>',
    classCode: '<?php echo addslashes($formData['classCode'] ?? ''); ?>'
};

// ===== YEAR LEVEL AND TERM FILTER FOR CLASS CODES =====
function filterClassCodesByYearAndTerm() {
    const yearLevelSelect = document.getElementById('yearLevelSelect');
    const termSelect = document.getElementById('termSelect');
    const classCodeSelect = document.getElementById('classCodeSelect');
    const blockSelect = document.getElementById('blockSelect');
    const classNameInput = document.getElementById('classNameInput');
    
    if (!yearLevelSelect || !classCodeSelect) return;
    
    const selectedYear = yearLevelSelect.value;
    const selectedTerm = termSelect ? termSelect.value : '';
    
    // Clear current options
    classCodeSelect.innerHTML = '';
    
    // Also update block select based on year level
    if (blockSelect) {
        blockSelect.innerHTML = '';
        if (!selectedYear) {
            blockSelect.innerHTML = '<option value="">-- Select Year Level First --</option>';
        } else {
            blockSelect.innerHTML = '<option value="">-- Select Block --</option>';
            // Get year number prefix (1, 2, 3)
            const yearNum = selectedYear.charAt(0);
            // Add blocks A-Z with year prefix
            const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
            letters.forEach(letter => {
                const blockValue = yearNum + letter;
                const option = document.createElement('option');
                option.value = blockValue;
                option.textContent = blockValue;
                // Restore previously selected block
                if (initialFormData.block === blockValue) {
                    option.selected = true;
                }
                blockSelect.appendChild(option);
            });
        }
    }
    
    if (!selectedYear) {
        classCodeSelect.innerHTML = '<option value="">-- Select Year Level First --</option>';
        classNameInput.value = '';
        return;
    }
    
    if (!selectedTerm) {
        classCodeSelect.innerHTML = '<option value="">-- Select Term First --</option>';
        classNameInput.value = '';
        return;
    }
    
    // Add default option
    classCodeSelect.innerHTML = '<option value="">-- Select Class Code --</option>';
    
    // Get courses for the selected year level AND term
    const yearCourses = coursesByYearAndSem[selectedYear] || {};
    const courses = yearCourses[selectedTerm] || {};
    
    // Add course options
    Object.entries(courses).forEach(([code, name]) => {
        const option = document.createElement('option');
        option.value = code;
        option.textContent = code;
        classCodeSelect.appendChild(option);
    });
    
    // Clear course name
    classNameInput.value = '';
    
    // Reset dual/regular schedule visibility
    const dualScheduleContainer = document.getElementById('dualScheduleContainer');
    const regularScheduleContainer = document.getElementById('regularScheduleContainer');
    if (dualScheduleContainer) dualScheduleContainer.style.display = 'none';
    if (regularScheduleContainer) regularScheduleContainer.style.display = 'block';
}

// Legacy function for backward compatibility
function filterClassCodesByYear() {
    filterClassCodesByYearAndTerm();
}

// ===== INSTRUCTOR SEARCHABLE DROPDOWN =====
const instructorsList = <?php echo json_encode($instructorsList); ?>;

function showInstructorDropdown(inputId, dropdownId, valueId) {
    const searchInput = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);
    const searchTerm = searchInput.value.toLowerCase();
    
    // Filter instructors based on search term
    const filteredInstructors = Object.entries(instructorsList).filter(([name, credentials]) => {
        return name.toLowerCase().includes(searchTerm) || credentials.toLowerCase().includes(searchTerm);
    });
    
    let html = '';
    
    if (filteredInstructors.length === 0) {
        html = '<div style="padding: 12px; color: #666; text-align: center;">No instructors found</div>';
    } else {
        filteredInstructors.forEach(([name, credentials]) => {
            html += `<div class="instructor-option" data-value="${escapeHtml(name)}" data-input="${inputId}" data-valueid="${valueId}" data-dropdown="${dropdownId}"
                style="padding: 10px 12px; cursor: pointer; border-bottom: 1px solid #eee; background: white;"
                onmouseover="this.style.background='#e8f4fd'" onmouseout="this.style.background='white'">
                <strong>${escapeHtml(name)}</strong><br>
                <small style="color: #666;">${escapeHtml(credentials)}</small>
            </div>`;
        });
    }
    
    dropdown.innerHTML = html;
    dropdown.style.display = 'block';
    
    // Add click handlers
    dropdown.querySelectorAll('.instructor-option').forEach(opt => {
        opt.addEventListener('click', function() {
            const inputEl = document.getElementById(this.dataset.input);
            const valueEl = document.getElementById(this.dataset.valueid);
            const dropdownEl = document.getElementById(this.dataset.dropdown);
            
            inputEl.value = this.dataset.value;
            valueEl.value = this.dataset.value;
            dropdownEl.style.display = 'none';
            inputEl.style.borderColor = '#28a745';
        });
    });
}

function validateInstructorInput(inputId, valueId) {
    const searchInput = document.getElementById(inputId);
    const valueInput = document.getElementById(valueId);
    const value = searchInput.value;
    
    // Check if the value exactly matches an instructor name
    if (instructorsList.hasOwnProperty(value)) {
        valueInput.value = value;
        searchInput.style.borderColor = '#28a745';
        searchInput.setCustomValidity('');
    } else if (value) {
        valueInput.value = '';
        searchInput.style.borderColor = '#dc3545';
        searchInput.setCustomValidity('Please select a valid instructor from the list');
    } else {
        valueInput.value = '';
        searchInput.style.borderColor = '#ddd';
        searchInput.setCustomValidity('Please select an instructor');
    }
}

function initInstructorDropdowns() {
    const configs = [
        { input: 'instructorSearchInput', dropdown: 'instructorDropdown', value: 'instructorValue' },
        { input: 'lecInstructorSearchInput', dropdown: 'lecInstructorDropdown', value: 'lecInstructorValue' },
        { input: 'labInstructorSearchInput', dropdown: 'labInstructorDropdown', value: 'labInstructorValue' }
    ];
    
    configs.forEach(config => {
        const input = document.getElementById(config.input);
        const dropdown = document.getElementById(config.dropdown);
        
        if (input && dropdown) {
            input.addEventListener('focus', () => showInstructorDropdown(config.input, config.dropdown, config.value));
            input.addEventListener('input', () => {
                showInstructorDropdown(config.input, config.dropdown, config.value);
                validateInstructorInput(config.input, config.value);
            });
            input.addEventListener('blur', () => {
                setTimeout(() => {
                    dropdown.style.display = 'none';
                    validateInstructorInput(config.input, config.value);
                }, 200);
            });
            
            // Validate on page load if value exists
            if (input.value) {
                validateInstructorInput(config.input, config.value);
            }
        }
    });
}

function autoFillClassName() {
    const classCodeSelect = document.getElementById('classCodeSelect');
    const classNameInput = document.getElementById('classNameInput');
    const dualScheduleContainer = document.getElementById('dualScheduleContainer');
    const regularScheduleContainer = document.getElementById('regularScheduleContainer');
    const regularInstructorRow = document.getElementById('regularInstructorRow');
    
    if (!classCodeSelect || !classNameInput) return;
    
    const selectedCode = classCodeSelect.value;
    
    if (selectedCode && curriculumCourses[selectedCode]) {
        classNameInput.value = curriculumCourses[selectedCode];
    } else {
        classNameInput.value = '';
    }
    
    // Show dual schedule sections for CIT and CC courses, regular section for others
    const upperCode = selectedCode.toUpperCase();
    const isCITCC = upperCode.startsWith('CIT') || upperCode.startsWith('CC');
    
    if (dualScheduleContainer && regularScheduleContainer) {
        if (isCITCC) {
            dualScheduleContainer.style.display = 'block';
            regularScheduleContainer.style.display = 'none';
            if (regularInstructorRow) regularInstructorRow.style.display = 'none';
            // Make dual fields required, regular fields not required
            setDualFieldsRequired(true);
            setRegularFieldsRequired(false);
        } else {
            dualScheduleContainer.style.display = 'none';
            regularScheduleContainer.style.display = 'block';
            if (regularInstructorRow) regularInstructorRow.style.display = '';
            // Make regular fields required, dual fields not required
            setDualFieldsRequired(false);
            setRegularFieldsRequired(true);
        }
    }
}

// Helper function to set required attribute on dual schedule fields
function setDualFieldsRequired(required) {
    const lecStartTime = document.getElementById('lecStartTimeSelect');
    const lecRoom = document.getElementById('lecRoomSearchInput');
    const labStartTime = document.getElementById('labStartTimeSelect');
    const labRoom = document.getElementById('labRoomSearchInput');
    
    if (lecStartTime) lecStartTime.required = required;
    if (lecRoom) lecRoom.required = required;
    if (labStartTime) labStartTime.required = required;
    if (labRoom) labRoom.required = required;
}

// Helper function to set required attribute on regular schedule fields
function setRegularFieldsRequired(required) {
    const startTime = document.getElementById('startTimeSelect');
    const room = document.getElementById('roomSearchInput');
    
    if (startTime) startTime.required = required;
    if (room) room.required = required;
}

// Check if current course is CIT/CC
function isCITCCCourse() {
    const classCodeSelect = document.getElementById('classCodeSelect');
    if (!classCodeSelect) return false;
    const upperCode = classCodeSelect.value.toUpperCase();
    return upperCode.startsWith('CIT') || upperCode.startsWith('CC');
}

// Auto-fill on page load if class code is already selected
document.addEventListener('DOMContentLoaded', function() {
    const yearLevelSelect = document.getElementById('yearLevelSelect');
    const classCodeSelect = document.getElementById('classCodeSelect');
    
    // Initialize year level filter and restore class code if yearLevel is set
    if (yearLevelSelect && yearLevelSelect.value) {
        filterClassCodesByYear();
        // Restore previously selected class code from PHP formData
        <?php if (!empty($formData['classCode'])): ?>
        const savedClassCode = "<?php echo htmlspecialchars($formData['classCode']); ?>";
        if (classCodeSelect) {
            classCodeSelect.value = savedClassCode;
            autoFillClassName();
        }
        <?php endif; ?>
    }
    
    if (classCodeSelect && classCodeSelect.value) {
        autoFillClassName();
    }
    // Also auto-fill end time if start time is already selected
    const startTimeSelect = document.getElementById('startTimeSelect');
    if (startTimeSelect && startTimeSelect.value) {
        autoFillEndTime();
    }
    
    // Initialize dual sections visibility based on existing class code
    if (classCodeSelect) {
        const selectedCode = classCodeSelect.value.toUpperCase();
        const isCITCC = selectedCode.startsWith('CIT') || selectedCode.startsWith('CC');
        const dualScheduleContainer = document.getElementById('dualScheduleContainer');
        const regularScheduleContainer = document.getElementById('regularScheduleContainer');
        
        if (dualScheduleContainer && regularScheduleContainer) {
            if (isCITCC) {
                dualScheduleContainer.style.display = 'block';
                regularScheduleContainer.style.display = 'none';
                setDualFieldsRequired(true);
                setRegularFieldsRequired(false);
            }
        }
    }
    
    // Initialize dual room availability checking
    initDualRoomInputs();
    
    // Initialize instructor searchable dropdowns
    initInstructorDropdowns();
});

// ===== TIME SLOT AUTO-FILL =====
const fixedTimeSlots = <?php echo json_encode($fixedTimeSlots); ?>;

// ===== DAY SELECTION (Regular Schedule) =====
function toggleDay(btn) {
    const day = btn.dataset.day;
    const input = document.getElementById('scheduleDaysInput');
    let selectedDays = input.value ? input.value.split('/') : [];
    
    if (btn.classList.contains('selected')) {
        // Deselect
        btn.classList.remove('selected');
        btn.style.borderColor = '#ddd';
        btn.style.background = '#fff';
        btn.style.color = '#333';
        selectedDays = selectedDays.filter(d => d.toLowerCase() !== day.toLowerCase());
    } else {
        // Select
        btn.classList.add('selected');
        btn.style.borderColor = '#28a745';
        btn.style.background = '#28a745';
        btn.style.color = '#fff';
        if (!selectedDays.includes(day)) {
            selectedDays.push(day);
        }
    }
    
    input.value = selectedDays.join('/');
    
    // Trigger room availability check
    checkRoomAvailability();
}

// ===== DUAL DAY SELECTION (Lecture/Lab) =====
function toggleDualDay(btn, type) {
    const day = btn.dataset.day;
    const inputId = type === 'lec' ? 'lecDaysInput' : 'labDaysInput';
    const input = document.getElementById(inputId);
    const selectedColor = type === 'lec' ? '#1976d2' : '#f9a825';
    
    let selectedDays = input.value ? input.value.split('/') : [];
    
    if (btn.classList.contains('selected')) {
        // Deselect
        btn.classList.remove('selected');
        btn.style.borderColor = '#ddd';
        btn.style.background = '#fff';
        btn.style.color = '#333';
        selectedDays = selectedDays.filter(d => d.toLowerCase() !== day.toLowerCase());
    } else {
        // Select
        btn.classList.add('selected');
        btn.style.borderColor = selectedColor;
        btn.style.background = selectedColor;
        btn.style.color = '#fff';
        if (!selectedDays.includes(day)) {
            selectedDays.push(day);
        }
    }
    
    input.value = selectedDays.join('/');
    
    // Trigger room availability check for this section
    checkDualRoomAvailability(type);
}

// ===== DUAL END TIME AUTO-FILL =====
function autoFillDualEndTime(type) {
    const startTimeSelect = document.getElementById(type + 'StartTimeSelect');
    const endTimeDisplay = document.getElementById(type + 'EndTimeDisplay');
    const endTimeValue = document.getElementById(type + 'EndTimeValue');
    
    if (!startTimeSelect || !endTimeDisplay || !endTimeValue) return;
    
    const selectedStart = startTimeSelect.value;
    
    if (selectedStart && fixedTimeSlots[selectedStart]) {
        const endTime24 = fixedTimeSlots[selectedStart];
        endTimeValue.value = endTime24;
        
        // Convert to 12-hour format for display
        const [hours, minutes] = endTime24.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const hour12 = hour % 12 || 12;
        endTimeDisplay.value = hour12 + ':' + minutes + ' ' + ampm;
    } else {
        endTimeValue.value = '';
        endTimeDisplay.value = '';
    }
    
    // Trigger room availability check for this section
    checkDualRoomAvailability(type);
}

// ===== DUAL ROOM AVAILABILITY =====
let dualRoomData = { lec: { allRooms: [], availableLabels: [] }, lab: { allRooms: [], availableLabels: [] } };

function checkDualRoomAvailability(type) {
    const daysInput = document.getElementById(type + 'DaysInput');
    const startTimeSelect = document.getElementById(type + 'StartTimeSelect');
    const endTimeValue = document.getElementById(type + 'EndTimeValue');
    const roomInput = document.getElementById(type + 'RoomSearchInput');
    const statusDiv = document.getElementById(type + 'RoomAvailabilityStatus');
    
    const days = daysInput?.value || '';
    const startTime = startTimeSelect?.value || '';
    const endTime = endTimeValue?.value || '';
    
    // Reset room input if days/time not complete
    if (!days || !startTime || !endTime) {
        roomInput.placeholder = '-- Select days & time first --';
        roomInput.disabled = true;
        dualRoomData[type].allRooms = [];
        dualRoomData[type].availableLabels = [];
        statusDiv.innerHTML = '<span style="color: #666;">📅 Please select days and time slot first</span>';
        return;
    }
    
    // Show loading state
    roomInput.placeholder = 'Loading rooms...';
    roomInput.disabled = true;
    statusDiv.innerHTML = '<span style="color: #666;">🔄 Checking room availability...</span>';
    
    // Build URL with class_type filter
    const classType = type === 'lab' ? 'laboratory' : 'lecture';
    let url = `?check_room_availability=1&days=${encodeURIComponent(days)}&start_time=${encodeURIComponent(startTime)}&end_time=${encodeURIComponent(endTime)}&class_type=${encodeURIComponent(classType)}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            let rooms = data.rooms || [];
            
            // Filter by class type
            if (type === 'lab') {
                rooms = rooms.filter(r => r.type === 'computer-lab');
            } else {
                rooms = rooms.filter(r => r.type !== 'computer-lab');
            }
            
            dualRoomData[type].allRooms = rooms;
            
            const availableRooms = rooms.filter(r => r.available);
            const unavailableRooms = rooms.filter(r => !r.available);
            dualRoomData[type].availableLabels = availableRooms.map(r => r.label);
            
            // Enable input
            roomInput.disabled = false;
            roomInput.placeholder = type === 'lab' ? 'Type to search computer labs...' : 'Type to search lecture rooms...';
            
            // Update status
            const typeLabel = type === 'lab' ? 'Laboratory' : 'Lecture';
            statusDiv.innerHTML = `<span style="color: #dc3545;">❌ ${unavailableRooms.length} room(s) occupied</span>` +
                ` <span style="color: #28a745; margin-left: 10px;">✅ ${availableRooms.length} ${typeLabel.toLowerCase()} room(s) available</span>`;
        })
        .catch(error => {
            console.error('Error checking room availability:', error);
            statusDiv.innerHTML = '<span style="color: #dc3545;">⚠️ Error checking availability</span>';
            roomInput.placeholder = '-- Error loading rooms --';
            roomInput.disabled = true;
        });
}

function showDualRoomDropdown(type) {
    const roomInput = document.getElementById(type + 'RoomSearchInput');
    const dropdown = document.getElementById(type + 'RoomDropdown');
    const searchTerm = roomInput.value.toLowerCase();
    const rooms = dualRoomData[type].allRooms;
    
    if (rooms.length === 0) {
        dropdown.style.display = 'none';
        return;
    }
    
    const unavailableRooms = rooms.filter(r => !r.available && r.label.toLowerCase().includes(searchTerm));
    const availableRooms = rooms.filter(r => r.available && r.label.toLowerCase().includes(searchTerm));
    
    let html = '';
    
    // Show unavailable rooms
    if (unavailableRooms.length > 0) {
        html += '<div style="padding: 6px 12px; background: #fdf0f0; color: #dc3545; font-weight: bold; font-size: 12px;">❌ Unavailable</div>';
        unavailableRooms.forEach(room => {
            const conflictText = room.conflict ? `Booked: ${room.conflict.classCode}` : 'Occupied';
            const locationInfo = room.building ? `${room.building} - ${room.floor}${getFloorSuffix(room.floor)} Floor` : '';
            html += `<div style="padding: 10px 12px; background: #f9f9f9; color: #999; border-bottom: 1px solid #eee; cursor: not-allowed;">
                <span>❌</span> <strong>${escapeHtml(room.label)}</strong><br>
                <small style="color: #666;">📍 ${escapeHtml(locationInfo)}</small><br>
                <small style="color: #dc3545;">🚫 ${escapeHtml(conflictText)}</small>
            </div>`;
        });
    }
    
    // Show available rooms
    if (availableRooms.length > 0) {
        html += '<div style="padding: 6px 12px; background: #f0f9f0; color: #28a745; font-weight: bold; font-size: 12px;">✅ Available</div>';
        availableRooms.forEach(room => {
            const locationInfo = room.building ? `${room.building} - ${room.floor}${getFloorSuffix(room.floor)} Floor` : '';
            html += `<div class="dual-room-option" data-value="${escapeHtml(room.label)}" data-type="${type}" style="padding: 10px 12px; cursor: pointer; border-bottom: 1px solid #eee; background: white;" onmouseover="this.style.background='#e8f4fd'" onmouseout="this.style.background='white'">
                <span style="color: #28a745;">✅</span> <strong>${escapeHtml(room.label)}</strong><br>
                <small style="color: #666;">📍 ${escapeHtml(locationInfo)}</small>
            </div>`;
        });
    }
    
    if (html === '') {
        const typeLabel = type === 'lab' ? 'computer lab' : 'lecture';
        html = `<div style="padding: 12px; color: #666; text-align: center;">No ${typeLabel} rooms found</div>`;
    }
    
    dropdown.innerHTML = html;
    dropdown.style.display = 'block';
    
    // Add click handlers
    dropdown.querySelectorAll('.dual-room-option').forEach(opt => {
        opt.addEventListener('click', function() {
            const roomType = this.dataset.type;
            document.getElementById(roomType + 'RoomSearchInput').value = this.dataset.value;
            document.getElementById(roomType + 'RoomDropdown').style.display = 'none';
            validateDualRoomInput(roomType);
        });
    });
}

function validateDualRoomInput(type) {
    const roomInput = document.getElementById(type + 'RoomSearchInput');
    const value = roomInput.value;
    
    if (dualRoomData[type].availableLabels.includes(value)) {
        roomInput.style.borderColor = '#28a745';
        roomInput.setCustomValidity('');
    } else if (value) {
        roomInput.style.borderColor = '#dc3545';
        roomInput.setCustomValidity('Please select a valid available room');
    } else {
        roomInput.style.borderColor = '#ddd';
        roomInput.setCustomValidity('Please select a room');
    }
}

function initDualRoomInputs() {
    ['lec', 'lab'].forEach(type => {
        const roomInput = document.getElementById(type + 'RoomSearchInput');
        const dropdown = document.getElementById(type + 'RoomDropdown');
        
        if (roomInput) {
            roomInput.addEventListener('focus', () => showDualRoomDropdown(type));
            roomInput.addEventListener('input', () => {
                showDualRoomDropdown(type);
                validateDualRoomInput(type);
            });
            roomInput.addEventListener('blur', () => {
                setTimeout(() => {
                    dropdown.style.display = 'none';
                    validateDualRoomInput(type);
                }, 200);
            });
        }
    });
}

function getSelectedDays() {
    const input = document.getElementById('scheduleDaysInput');
    return input.value ? input.value.split('/') : [];
}

function autoFillEndTime() {
    const startTimeSelect = document.getElementById('startTimeSelect');
    const endTimeDisplay = document.getElementById('endTimeDisplay');
    const endTimeValue = document.getElementById('endTimeValue');
    
    if (!startTimeSelect || !endTimeDisplay || !endTimeValue) return;
    
    const selectedStart = startTimeSelect.value;
    
    if (selectedStart && fixedTimeSlots[selectedStart]) {
        const endTime24 = fixedTimeSlots[selectedStart];
        endTimeValue.value = endTime24;
        
        // Convert to 12-hour format for display
        const [hours, minutes] = endTime24.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const hour12 = hour % 12 || 12;
        endTimeDisplay.value = hour12 + ':' + minutes + ' ' + ampm;
    } else {
        endTimeValue.value = '';
        endTimeDisplay.value = '';
    }
    
    // Trigger room availability check
    checkRoomAvailability();
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
    const scheduleDaysInput = document.getElementById('scheduleDaysInput');
    const startTimeSelect = document.getElementById('startTimeSelect');
    const endTimeValue = document.getElementById('endTimeValue');
    const roomInput = document.getElementById('roomSearchInput');
    const statusDiv = document.getElementById('roomAvailabilityStatus');
    const dropdown = document.getElementById('roomDropdown');
    
    const days = scheduleDaysInput?.value || '';
    const startTime = startTimeSelect?.value || '';
    const endTime = endTimeValue?.value || '';
    
    // Reset room input if days/time not complete
    if (!days || !startTime || !endTime) {
        roomInput.placeholder = '-- Select days & time first --';
        roomInput.disabled = true;
        allRooms = [];
        availableRoomLabels = [];
        statusDiv.innerHTML = '<span style="color: #666;">📅 Please select days and time slot to see available rooms</span>';
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
    
    // Build URL with parameters - using days instead of date
    let url = `?check_room_availability=1&days=${encodeURIComponent(days)}&start_time=${encodeURIComponent(startTime)}&end_time=${encodeURIComponent(endTime)}`;
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
            let statusHtml = `<span style="color: #dc3545;">❌ ${unavailableRooms.length} room(s) occupied</span>` +
                ` <span style="color: #28a745; margin-left: 10px;">✅ ${availableRooms.length} room(s) available</span>`;
            
            statusDiv.innerHTML = statusHtml;
            
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
    
    // Filter by search term
    const unavailableRooms = allRooms.filter(r => !r.available && r.label.toLowerCase().includes(searchTerm));
    const availableRooms = allRooms.filter(r => r.available && r.label.toLowerCase().includes(searchTerm));
    
    let html = '';
    
    // Show unavailable rooms (occupied)
    if (unavailableRooms.length > 0) {
        html += '<div style="padding: 6px 12px; background: #fdf0f0; color: #dc3545; font-weight: bold; font-size: 12px;">❌ Unavailable Rooms (occupied)</div>';
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
    
    // Show available rooms
    if (availableRooms.length > 0) {
        html += '<div style="padding: 6px 12px; background: #f0f9f0; color: #28a745; font-weight: bold; font-size: 12px;">✅ Available Rooms (click to select)</div>';
        availableRooms.forEach(room => {
            const locationInfo = room.building ? `${room.building} - ${room.floor}${getFloorSuffix(room.floor)} Floor` : '';
            const roomTypeLabel = room.type === 'computer-lab' ? ' 🖥️' : '';
            html += `<div class="room-option" data-value="${escapeHtml(room.label)}" style="padding: 10px 12px; cursor: pointer; border-bottom: 1px solid #eee; background: white;" onmouseover="this.style.background='#e8f4fd'" onmouseout="this.style.background='white'">
                <span style="color: #28a745;">✅</span> <strong>${escapeHtml(room.label)}</strong>${roomTypeLabel}<br>
                <small style="color: #666;">📍 ${escapeHtml(locationInfo)}</small>
            </div>`;
        });
    }
    
    if (html === '') {
        html = `<div style="padding: 12px; color: #666; text-align: center;">No rooms found matching your search</div>`;
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

// Validate date and time - reject past dates and past times
function validateDateTimeSchedule(showAlert = false) {
    const dateInput = document.querySelector('input[name="scheduleDate"]');
    const startTimeSelect = document.getElementById('startTimeSelect');
    const statusDiv = document.getElementById('roomAvailabilityStatus');
    
    if (!dateInput) return true;
    
    const selectedDate = dateInput.value;
    const selectedStartTime = startTimeSelect?.value || '';
    
    const today = new Date();
    const todayStr = today.toISOString().split('T')[0]; // YYYY-MM-DD format
    
    // Check if date is in the past
    if (selectedDate && selectedDate < todayStr) {
        const formattedDate = new Date(selectedDate).toLocaleDateString('en-GB'); // DD/MM/YYYY
        const formattedToday = today.toLocaleDateString('en-GB');
        const errorMsg = `Cannot schedule for a past date (${formattedDate}). Today is ${formattedToday}. Please select today or a future date.`;
        
        if (showAlert) {
            alert(errorMsg);
        }
        if (statusDiv) {
            statusDiv.innerHTML = `<span style="color: #dc3545;">⚠️ ${errorMsg}</span>`;
        }
        dateInput.style.borderColor = '#dc3545';
        return false;
    } else if (dateInput.value) {
        dateInput.style.borderColor = '#28a745';
    }
    
    // If today's date, check if start time has passed
    if (selectedDate === todayStr && selectedStartTime) {
        const now = new Date();
        const currentTimeStr = now.toTimeString().slice(0, 5); // HH:MM format
        
        if (selectedStartTime < currentTimeStr) {
            const startTimeFormatted = new Date('1970-01-01T' + selectedStartTime).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            const currentTimeFormatted = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            const errorMsg = `The start time ${startTimeFormatted} has already passed. It's currently ${currentTimeFormatted}. Please select a future time.`;
            
            if (showAlert) {
                alert(errorMsg);
            }
            if (statusDiv) {
                statusDiv.innerHTML = `<span style="color: #dc3545;">⚠️ ${errorMsg}</span>`;
            }
            startTimeSelect.style.borderColor = '#dc3545';
            return false;
        } else {
            startTimeSelect.style.borderColor = '#28a745';
        }
    } else if (startTimeSelect && startTimeSelect.value) {
        startTimeSelect.style.borderColor = '#28a745';
    }
    
    return true;
}

// Add event listeners to date/time inputs
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.querySelector('input[name="scheduleDate"]');
    const startTimeSelect = document.getElementById('startTimeSelect');
    const endTimeValue = document.getElementById('endTimeValue');
    const roomInput = document.getElementById('roomSearchInput');
    const dropdown = document.getElementById('roomDropdown');
    const form = document.getElementById('scheduleForm');
    
    // Set minimum date to today
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);
        
        // Validate on date change (no alert, just visual)
        dateInput.addEventListener('change', function() {
            if (validateDateTimeSchedule(false)) {
                checkRoomAvailability();
            }
        });
    }
    
    // Start time select already has onchange handler for autoFillEndTime
    // which calls checkRoomAvailability
    
    // Form submit validation (with alert)
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateDateTimeSchedule(true)) {
                e.preventDefault();
                return false;
            }
        });
    }
    
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
    if (dateInput?.value && startTimeSelect?.value && endTimeValue?.value) {
        checkRoomAvailability();
    }
});
</script>
