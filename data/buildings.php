<?php
/**
 * Building Data for UC Nexus
 * Contains building information and configurations
 */

// Building information
$BUILDINGS = [
    'U' => [
        'id' => 'U',
        'name' => 'BRS Building (U Building)',
        'fullName' => 'BRS Building',
        'totalRooms' => 42,
        'floors' => 9,
        'color' => '#3B82F6' // Blue
    ],
    'M' => [
        'id' => 'M',
        'name' => 'Main Building (M Building)',
        'fullName' => 'Main Building',
        'totalRooms' => 13,
        'floors' => 2,
        'color' => '#10B981' // Green
    ],
    'S' => [
        'id' => 'S',
        'name' => 'Science Building (S Building)',
        'fullName' => 'Science Building',
        'totalRooms' => 69,
        'floors' => 7,
        'color' => '#8B5CF6' // Purple
    ],
    'N' => [
        'id' => 'N',
        'name' => 'EDS Building (N Building)',
        'fullName' => 'EDS Building',
        'totalRooms' => 35,
        'floors' => 5,
        'color' => '#F59E0B' // Amber
    ],
    'F' => [
        'id' => 'F',
        'name' => 'CHTM Building (F Building)',
        'fullName' => 'CHTM Building',
        'totalRooms' => 27,
        'floors' => 7,
        'color' => '#EC4899' // Pink
    ],
    'G' => [
        'id' => 'G',
        'name' => 'PE Building (G Building)',
        'fullName' => 'PE Building',
        'totalRooms' => 10,
        'floors' => 4,
        'color' => '#EF4444' // Red
    ]
];

/**
 * Get all buildings
 */
function getAllBuildings() {
    global $BUILDINGS;
    return $BUILDINGS;
}

/**
 * Get a specific building by ID
 */
function getBuildingById($buildingId) {
    global $BUILDINGS;
    return $BUILDINGS[$buildingId] ?? null;
}

/**
 * Get total number of buildings
 */
function getTotalBuildings() {
    global $BUILDINGS;
    return count($BUILDINGS);
}

/**
 * Get total rooms across all buildings
 */
function getTotalRoomsAllBuildings() {
    global $BUILDINGS;
    $total = 0;
    foreach ($BUILDINGS as $building) {
        $total += $building['totalRooms'];
    }
    return $total;
}
