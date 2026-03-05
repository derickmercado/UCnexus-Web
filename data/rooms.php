<?php
/**
 * Room Data for UC Nexus
 * Database-connected version
 */

require_once __DIR__ . '/../config/database.php';

// Fallback static data (used if database is not available)
$STANDARD_TIME_SLOTS = [
    ['startTime' => '07:30', 'endTime' => '08:50'],
    ['startTime' => '08:50', 'endTime' => '10:10'],
    ['startTime' => '10:10', 'endTime' => '11:30'],
    ['startTime' => '11:30', 'endTime' => '12:50'],
    ['startTime' => '12:50', 'endTime' => '14:10'],
    ['startTime' => '14:10', 'endTime' => '15:30'],
    ['startTime' => '15:30', 'endTime' => '16:50'],
    ['startTime' => '16:50', 'endTime' => '18:10'],
    ['startTime' => '18:10', 'endTime' => '19:30'],
    ['startTime' => '19:30', 'endTime' => '20:50'],
];

$SCHOOL_DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

// Room types for categorization
$ROOM_TYPES = [
    'computer-lab' => 'Computer Laboratory',
    'classroom' => 'Classroom/Lecture Hall',
    'lecture-hall' => 'Lecture Hall',
    'chemistry-lab' => 'Chemistry Laboratory',
    'physics-lab' => 'Physics Laboratory',
    'biology-lab' => 'Biology Laboratory',
    'engineering-lab' => 'Engineering Laboratory',
    'drafting-room' => 'Drafting Room',
    'nursing-lab' => 'Nursing Laboratory',
    'culinary-lab' => 'Culinary Laboratory',
    'demo-room' => 'Demo Room',
    'conference' => 'Conference Room',
    'office' => 'Office',
    'gym' => 'Gymnasium',
    'other' => 'Other'
];

// =============== HELPER FUNCTIONS ===============

/**
 * Get all rooms from database
 */
function getAllRooms() {
    if (!isDatabaseSetup()) {
        return [];
    }
    
    $rooms = dbFetchAll("SELECT * FROM rooms WHERE is_active = 1 ORDER BY building_id, floor, id");
    
    // Convert to expected format
    return array_map(function($room) {
        return [
            'id' => $room['id'],
            'name' => $room['name'],
            'description' => $room['description'],
            'capacity' => (int)$room['capacity'],
            'location' => $room['location'],
            'building' => $room['building_id'],
            'floor' => (int)$room['floor'],
            'type' => $room['type']
        ];
    }, $rooms);
}

/**
 * Get total room count
 */
function getTotalRooms() {
    if (!isDatabaseSetup()) {
        return 0;
    }
    
    $result = dbFetchOne("SELECT COUNT(*) as count FROM rooms WHERE is_active = 1");
    return (int)$result['count'];
}

/**
 * Get room by ID
 */
function getRoomById($roomId) {
    if (!isDatabaseSetup()) {
        return null;
    }
    
    $room = dbFetchOne("SELECT * FROM rooms WHERE id = ?", [$roomId]);
    
    if ($room) {
        return [
            'id' => $room['id'],
            'name' => $room['name'],
            'description' => $room['description'],
            'capacity' => (int)$room['capacity'],
            'location' => $room['location'],
            'building' => $room['building_id'],
            'floor' => (int)$room['floor'],
            'type' => $room['type']
        ];
    }
    
    return null;
}

/**
 * Get rooms by building
 */
function getRoomsByBuilding($buildingId) {
    if (!isDatabaseSetup()) {
        return [];
    }
    
    $rooms = dbFetchAll(
        "SELECT * FROM rooms WHERE building_id = ? AND is_active = 1 ORDER BY floor, id",
        [$buildingId]
    );
    
    return array_map(function($room) {
        return [
            'id' => $room['id'],
            'name' => $room['name'],
            'description' => $room['description'],
            'capacity' => (int)$room['capacity'],
            'location' => $room['location'],
            'building' => $room['building_id'],
            'floor' => (int)$room['floor'],
            'type' => $room['type']
        ];
    }, $rooms);
}

/**
 * Get rooms by floor
 */
function getRoomsByFloor($buildingId, $floor) {
    if (!isDatabaseSetup()) {
        return [];
    }
    
    $rooms = dbFetchAll(
        "SELECT * FROM rooms WHERE building_id = ? AND floor = ? AND is_active = 1 ORDER BY id",
        [$buildingId, $floor]
    );
    
    return array_map(function($room) {
        return [
            'id' => $room['id'],
            'name' => $room['name'],
            'description' => $room['description'],
            'capacity' => (int)$room['capacity'],
            'location' => $room['location'],
            'building' => $room['building_id'],
            'floor' => (int)$room['floor'],
            'type' => $room['type']
        ];
    }, $rooms);
}

/**
 * Get rooms by type
 */
function getRoomsByType($type) {
    if (!isDatabaseSetup()) {
        return [];
    }
    
    $rooms = dbFetchAll(
        "SELECT * FROM rooms WHERE type = ? AND is_active = 1 ORDER BY building_id, floor, id",
        [$type]
    );
    
    return array_map(function($room) {
        return [
            'id' => $room['id'],
            'name' => $room['name'],
            'description' => $room['description'],
            'capacity' => (int)$room['capacity'],
            'location' => $room['location'],
            'building' => $room['building_id'],
            'floor' => (int)$room['floor'],
            'type' => $room['type']
        ];
    }, $rooms);
}

/**
 * Get rooms with minimum capacity
 */
function getRoomsWithCapacity($minCapacity) {
    if (!isDatabaseSetup()) {
        return [];
    }
    
    $rooms = dbFetchAll(
        "SELECT * FROM rooms WHERE capacity >= ? AND is_active = 1 ORDER BY capacity DESC",
        [$minCapacity]
    );
    
    return array_map(function($room) {
        return [
            'id' => $room['id'],
            'name' => $room['name'],
            'description' => $room['description'],
            'capacity' => (int)$room['capacity'],
            'location' => $room['location'],
            'building' => $room['building_id'],
            'floor' => (int)$room['floor'],
            'type' => $room['type']
        ];
    }, $rooms);
}

/**
 * Search rooms by name or description
 */
function searchRooms($query) {
    if (!isDatabaseSetup()) {
        return [];
    }
    
    $searchTerm = '%' . $query . '%';
    $rooms = dbFetchAll(
        "SELECT * FROM rooms WHERE is_active = 1 AND (
            name LIKE ? OR 
            description LIKE ? OR 
            location LIKE ? OR 
            id LIKE ?
        ) ORDER BY building_id, floor, id",
        [$searchTerm, $searchTerm, $searchTerm, $searchTerm]
    );
    
    return array_map(function($room) {
        return [
            'id' => $room['id'],
            'name' => $room['name'],
            'description' => $room['description'],
            'capacity' => (int)$room['capacity'],
            'location' => $room['location'],
            'building' => $room['building_id'],
            'floor' => (int)$room['floor'],
            'type' => $room['type']
        ];
    }, $rooms);
}

/**
 * Get room statistics from database
 */
function getRoomStatistics() {
    if (!isDatabaseSetup()) {
        return [
            'total' => 0,
            'byBuilding' => [],
            'byType' => [],
            'totalCapacity' => 0,
            'avgCapacity' => 0
        ];
    }
    
    $stats = [
        'total' => 0,
        'byBuilding' => [],
        'byType' => [],
        'totalCapacity' => 0,
        'avgCapacity' => 0
    ];
    
    // Get totals
    $totals = dbFetchOne("SELECT COUNT(*) as total, SUM(capacity) as totalCap, AVG(capacity) as avgCap FROM rooms WHERE is_active = 1");
    $stats['total'] = (int)$totals['total'];
    $stats['totalCapacity'] = (int)$totals['totalCap'];
    $stats['avgCapacity'] = round($totals['avgCap']);
    
    // Get by building
    $byBuilding = dbFetchAll("SELECT building_id, COUNT(*) as count FROM rooms WHERE is_active = 1 GROUP BY building_id");
    foreach ($byBuilding as $row) {
        $stats['byBuilding'][$row['building_id']] = (int)$row['count'];
    }
    
    // Get by type
    $byType = dbFetchAll("SELECT type, COUNT(*) as count FROM rooms WHERE is_active = 1 GROUP BY type");
    foreach ($byType as $row) {
        $stats['byType'][$row['type']] = (int)$row['count'];
    }
    
    return $stats;
}

/**
 * Get standard time slots
 */
function getTimeSlots() {
    if (!isDatabaseSetup()) {
        global $STANDARD_TIME_SLOTS;
        return $STANDARD_TIME_SLOTS;
    }
    
    $slots = dbFetchAll("SELECT start_time, end_time FROM time_slots ORDER BY slot_order");
    
    return array_map(function($slot) {
        return [
            'startTime' => substr($slot['start_time'], 0, 5),
            'endTime' => substr($slot['end_time'], 0, 5)
        ];
    }, $slots);
}

/**
 * Get school days
 */
function getSchoolDays() {
    global $SCHOOL_DAYS;
    return $SCHOOL_DAYS;
}

/**
 * Get room types from database
 */
function getRoomTypes() {
    if (!isDatabaseSetup()) {
        global $ROOM_TYPES;
        return $ROOM_TYPES;
    }
    
    $types = dbFetchAll("SELECT id, name FROM room_types ORDER BY name");
    $result = [];
    
    foreach ($types as $type) {
        $result[$type['id']] = $type['name'];
    }
    
    return $result;
}

/**
 * Get rooms as dropdown options (for select elements)
 */
function getRoomsAsOptions() {
    if (!isDatabaseSetup()) {
        return [];
    }
    
    $rooms = dbFetchAll("
        SELECT r.id, r.name, r.capacity, r.floor, r.building_id, r.type,
               b.name as building_name, b.full_name as building_full_name
        FROM rooms r
        LEFT JOIN buildings b ON r.building_id = b.id
        WHERE r.is_active = 1 
        ORDER BY r.building_id, r.floor, r.id
    ");
    $options = [];
    
    foreach ($rooms as $room) {
        $roomNumber = str_replace('room-', '', $room['id']);
        $options[] = [
            'value' => $room['id'],
            'label' => $roomNumber . ' - ' . $room['name'] . ' (' . $room['capacity'] . ' seats)',
            'building' => $room['building_name'] ?? $room['building_id'] ?? 'Unknown',
            'buildingFull' => $room['building_full_name'] ?? '',
            'floor' => (int)$room['floor'],
            'type' => $room['type'] ?? 'classroom'
        ];
    }
    
    return $options;
}

/**
 * Get room display name (ID + Name)
 */
function getRoomDisplayName($roomId) {
    $room = getRoomById($roomId);
    if ($room) {
        return str_replace('room-', '', $room['id']) . ' - ' . $room['name'];
    }
    return $roomId;
}

/**
 * Get unique room types from database
 */
function getUniqueRoomTypes() {
    return getRoomTypes();
}

/**
 * Add a new room to database
 */
function addRoom($data) {
    if (!isDatabaseSetup()) {
        return false;
    }
    
    $sql = "INSERT INTO rooms (id, name, description, capacity, location, building_id, floor, type) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    dbExecute($sql, [
        $data['id'],
        $data['name'],
        $data['description'] ?? $data['name'],
        $data['capacity'],
        $data['location'],
        $data['building'],
        $data['floor'],
        $data['type']
    ]);
    
    return true;
}

/**
 * Update room in database
 */
function updateRoom($roomId, $data) {
    if (!isDatabaseSetup()) {
        return false;
    }
    
    $sql = "UPDATE rooms SET name = ?, description = ?, capacity = ?, location = ?, building_id = ?, floor = ?, type = ? WHERE id = ?";
    
    dbExecute($sql, [
        $data['name'],
        $data['description'] ?? $data['name'],
        $data['capacity'],
        $data['location'],
        $data['building'],
        $data['floor'],
        $data['type'],
        $roomId
    ]);
    
    return true;
}

/**
 * Delete room (soft delete)
 */
function deleteRoom($roomId) {
    if (!isDatabaseSetup()) {
        return false;
    }
    
    dbExecute("UPDATE rooms SET is_active = 0 WHERE id = ?", [$roomId]);
    return true;
}
