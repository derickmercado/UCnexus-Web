<?php
/**
 * Building Data for UC Nexus
 * Database-connected version
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Get all buildings from database
 */
function getAllBuildings() {
    if (!isDatabaseSetup()) {
        return [];
    }
    
    $buildings = dbFetchAll("
        SELECT b.*, 
               COUNT(r.id) as room_count
        FROM buildings b
        LEFT JOIN rooms r ON b.id = r.building_id AND r.is_active = 1
        GROUP BY b.id
        ORDER BY b.name
    ");
    
    $result = [];
    foreach ($buildings as $building) {
        $result[$building['id']] = [
            'id' => $building['id'],
            'name' => $building['full_name'] . ' (' . $building['id'] . ' Building)',
            'fullName' => $building['full_name'],
            'totalRooms' => (int)$building['room_count'],
            'floors' => (int)$building['floors'],
            'color' => $building['color']
        ];
    }
    
    return $result;
}

/**
 * Get a specific building by ID
 */
function getBuildingById($buildingId) {
    if (!isDatabaseSetup()) {
        return null;
    }
    
    $building = dbFetchOne("
        SELECT b.*, 
               COUNT(r.id) as room_count
        FROM buildings b
        LEFT JOIN rooms r ON b.id = r.building_id AND r.is_active = 1
        WHERE b.id = ?
        GROUP BY b.id
    ", [$buildingId]);
    
    if ($building) {
        return [
            'id' => $building['id'],
            'name' => $building['full_name'] . ' (' . $building['id'] . ' Building)',
            'fullName' => $building['full_name'],
            'totalRooms' => (int)$building['room_count'],
            'floors' => (int)$building['floors'],
            'color' => $building['color']
        ];
    }
    
    return null;
}

/**
 * Get total number of buildings
 */
function getTotalBuildings() {
    if (!isDatabaseSetup()) {
        return 0;
    }
    
    $result = dbFetchOne("SELECT COUNT(*) as count FROM buildings");
    return (int)$result['count'];
}

/**
 * Get total rooms across all buildings
 */
function getTotalRoomsAllBuildings() {
    if (!isDatabaseSetup()) {
        return 0;
    }
    
    $result = dbFetchOne("SELECT COUNT(*) as count FROM rooms WHERE is_active = 1");
    return (int)$result['count'];
}

/**
 * Add a new building
 */
function addBuilding($data) {
    if (!isDatabaseSetup()) {
        return false;
    }
    
    $sql = "INSERT INTO buildings (id, name, full_name, floors, color) VALUES (?, ?, ?, ?, ?)";
    dbExecute($sql, [
        $data['id'],
        $data['name'],
        $data['fullName'],
        $data['floors'],
        $data['color'] ?? '#3498db'
    ]);
    
    return true;
}

/**
 * Update building
 */
function updateBuilding($buildingId, $data) {
    if (!isDatabaseSetup()) {
        return false;
    }
    
    $sql = "UPDATE buildings SET name = ?, full_name = ?, floors = ?, color = ? WHERE id = ?";
    dbExecute($sql, [
        $data['name'],
        $data['fullName'],
        $data['floors'],
        $data['color'] ?? '#3498db',
        $buildingId
    ]);
    
    return true;
}

/**
 * Delete building
 */
function deleteBuilding($buildingId) {
    if (!isDatabaseSetup()) {
        return false;
    }
    
    // First, disable all rooms in this building
    dbExecute("UPDATE rooms SET is_active = 0 WHERE building_id = ?", [$buildingId]);
    
    // Then delete the building
    dbExecute("DELETE FROM buildings WHERE id = ?", [$buildingId]);
    
    return true;
}
