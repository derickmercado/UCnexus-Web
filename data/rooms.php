<?php
/**
 * Room Data for UC Nexus
 * Contains all room information organized by building
 * Total: 196 rooms
 */

// Standard time slots for scheduling
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

// All rooms data - Total: 196 rooms
$ROOMS = [
    // ===============================================
    // LABORATORY ROOMS
    // ===============================================
    
    // =============== MAIN BUILDING (M) - LABS ===============
    ['id' => 'room-M303', 'name' => 'Computer Laboratory', 'description' => 'Computer Laboratory', 'capacity' => 40, 'location' => 'Main Building, 3rd Floor', 'building' => 'M', 'floor' => 3, 'type' => 'computer-lab'],
    ['id' => 'room-M304', 'name' => 'Computer Laboratory', 'description' => 'Computer Laboratory', 'capacity' => 40, 'location' => 'Main Building, 3rd Floor', 'building' => 'M', 'floor' => 3, 'type' => 'computer-lab'],
    ['id' => 'room-M305', 'name' => 'Computer Laboratory', 'description' => 'Computer Laboratory', 'capacity' => 40, 'location' => 'Main Building, 3rd Floor', 'building' => 'M', 'floor' => 3, 'type' => 'computer-lab'],
    ['id' => 'room-M306', 'name' => 'Computer Laboratory', 'description' => 'Computer Laboratory', 'capacity' => 40, 'location' => 'Main Building, 3rd Floor', 'building' => 'M', 'floor' => 3, 'type' => 'computer-lab'],
    ['id' => 'room-M307', 'name' => 'Computer Laboratory', 'description' => 'Computer Laboratory', 'capacity' => 43, 'location' => 'Main Building, 3rd Floor', 'building' => 'M', 'floor' => 3, 'type' => 'computer-lab'],

    // =============== SCIENCE BUILDING (S) - BASEMENT LABS ===============
    ['id' => 'room-S010', 'name' => 'Hydro/Fluid Mech Lab', 'description' => 'Hydro/Fluid Mech Lab', 'capacity' => 45, 'location' => 'Science Building, Basement', 'building' => 'S', 'floor' => 0, 'type' => 'hydro/fluid-mech-lab'],
    ['id' => 'room-S012', 'name' => 'Matti/Soil Test Lab', 'description' => 'Matti/Soil Test Lab', 'capacity' => 40, 'location' => 'Science Building, Basement', 'building' => 'S', 'floor' => 0, 'type' => 'matti/soil-test-lab'],
    ['id' => 'room-S016', 'name' => 'Physics Lab', 'description' => 'Physics Lab', 'capacity' => 50, 'location' => 'Science Building, Basement', 'building' => 'S', 'floor' => 0, 'type' => 'physics-lab'],
    ['id' => 'room-S019', 'name' => 'GS & JHS Lab', 'description' => 'GS & JHS Lab', 'capacity' => 45, 'location' => 'Science Building, Basement', 'building' => 'S', 'floor' => 0, 'type' => 'gs/jhs-lab'],
    ['id' => 'room-S020', 'name' => 'HE Lab', 'description' => 'HE Lab', 'capacity' => 45, 'location' => 'Science Building, Basement', 'building' => 'S', 'floor' => 0, 'type' => 'he-lab'],

    // =============== SCIENCE BUILDING (S) - 1ST FLOOR LABS ===============
    ['id' => 'room-S106', 'name' => 'GSJHS Computer Laboratory', 'description' => 'GSJHS Computer Laboratory', 'capacity' => 45, 'location' => 'Science Building, 1st Floor', 'building' => 'S', 'floor' => 1, 'type' => 'computer-lab'],
    ['id' => 'room-S107', 'name' => 'Chemistry Laboratory', 'description' => 'Chemistry Laboratory', 'capacity' => 40, 'location' => 'Science Building, 1st Floor', 'building' => 'S', 'floor' => 1, 'type' => 'chemistry-lab'],
    ['id' => 'room-S111', 'name' => 'Chemistry Laboratory', 'description' => 'Chemistry Laboratory', 'capacity' => 40, 'location' => 'Science Building, 1st Floor', 'building' => 'S', 'floor' => 1, 'type' => 'chemistry-lab'],
    ['id' => 'room-S113', 'name' => 'TLE/THE/TVL-HE Demo Room', 'description' => 'TLE/THE/TVL-HE Demo Room', 'capacity' => 50, 'location' => 'Science Building, 1st Floor', 'building' => 'S', 'floor' => 1, 'type' => 'demo-room'],
    ['id' => 'room-S116', 'name' => 'Chemistry Laboratory', 'description' => 'Chemistry Laboratory', 'capacity' => 40, 'location' => 'Science Building, 1st Floor', 'building' => 'S', 'floor' => 1, 'type' => 'chemistry-lab'],
    ['id' => 'room-S117', 'name' => 'Chemistry Laboratory', 'description' => 'Chemistry Laboratory', 'capacity' => 40, 'location' => 'Science Building, 1st Floor', 'building' => 'S', 'floor' => 1, 'type' => 'chemistry-lab'],
    ['id' => 'room-S120', 'name' => 'SHS Physics Laboratory', 'description' => 'SHS Physics Laboratory', 'capacity' => 40, 'location' => 'Science Building, 1st Floor', 'building' => 'S', 'floor' => 1, 'type' => 'physics-lab'],
    ['id' => 'room-S121', 'name' => 'Con Med Lab', 'description' => 'Con Med Lab', 'capacity' => 40, 'location' => 'Science Building, 1st Floor', 'building' => 'S', 'floor' => 1, 'type' => 'con-med-lab'],

    // =============== SCIENCE BUILDING (S) - 2ND FLOOR LABS ===============
    ['id' => 'room-S213', 'name' => 'CEA Computer Laboratory', 'description' => 'CEA Computer Laboratory', 'capacity' => 30, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'cea-computer-lab'],
    ['id' => 'room-S218', 'name' => 'Engr Computer Laboratory', 'description' => 'Engineering Computer Laboratory', 'capacity' => 45, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'engineering-computer-lab'],
    ['id' => 'room-S227', 'name' => 'Electronic and Digital Lab', 'description' => 'Electronic and Digital Lab', 'capacity' => 40, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'electronic/digital-lab'],
    ['id' => 'room-S229', 'name' => 'Biology Laboratory', 'description' => 'Biology Laboratory', 'capacity' => 30, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'biology-lab'],
    ['id' => 'room-S232', 'name' => 'Biology Laboratory', 'description' => 'Biology Laboratory', 'capacity' => 40, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'biology-lab'],
    ['id' => 'room-S240', 'name' => 'Biology Laboratory', 'description' => 'Biology Laboratory', 'capacity' => 40, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'biology-lab'],

    // =============== SCIENCE BUILDING (S) - 3RD FLOOR LABS ===============
    ['id' => 'room-S311', 'name' => 'CISCO Laboratory', 'description' => 'CISCO Laboratory', 'capacity' => 9, 'location' => 'Science Building, 3rd Floor', 'building' => 'S', 'floor' => 3, 'type' => 'cisco-lab'],
    ['id' => 'room-S312', 'name' => 'Computer Laboratory', 'description' => 'Computer Laboratory', 'capacity' => 43, 'location' => 'Science Building, 3rd Floor', 'building' => 'S', 'floor' => 3, 'type' => 'computer-lab'],

    // =============== SCIENCE BUILDING (S) - 4TH FLOOR DRAFTING ===============
    ['id' => 'room-S413', 'name' => 'Drafting Room', 'description' => 'Drafting Room', 'capacity' => 28, 'location' => 'Science Building, 4th Floor', 'building' => 'S', 'floor' => 4, 'type' => 'drafting-room'],
    ['id' => 'room-S414', 'name' => 'Drafting Room', 'description' => 'Drafting Room', 'capacity' => 24, 'location' => 'Science Building, 4th Floor', 'building' => 'S', 'floor' => 4, 'type' => 'drafting-room'],
    ['id' => 'room-S415', 'name' => 'Thesis/Acoustic/Drafting Room', 'description' => 'Thesis/Acoustic/Drafting Room', 'capacity' => 29, 'location' => 'Science Building, 4th Floor', 'building' => 'S', 'floor' => 4, 'type' => 'thesis-room'],

    // =============== SCIENCE BUILDING (S) - 5TH FLOOR DRAFTING ===============
    ['id' => 'room-S502', 'name' => 'Drafting Room', 'description' => 'Drafting Room', 'capacity' => 30, 'location' => 'Science Building, 5th Floor', 'building' => 'S', 'floor' => 5, 'type' => 'drafting-room'],
    ['id' => 'room-S503', 'name' => 'Drafting Room', 'description' => 'Drafting Room', 'capacity' => 24, 'location' => 'Science Building, 5th Floor', 'building' => 'S', 'floor' => 5, 'type' => 'drafting-room'],
    ['id' => 'room-S504', 'name' => 'Drafting Room', 'description' => 'Drafting Room', 'capacity' => 32, 'location' => 'Science Building, 5th Floor', 'building' => 'S', 'floor' => 5, 'type' => 'drafting-room'],

    // =============== SCIENCE BUILDING (S) - 6TH FLOOR DRAFTING ===============
    ['id' => 'room-S602', 'name' => 'Drafting Room', 'description' => 'Drafting Room', 'capacity' => 50, 'location' => 'Science Building, 6th Floor', 'building' => 'S', 'floor' => 6, 'type' => 'drafting-room'],
    ['id' => 'room-S603', 'name' => 'Drafting Room', 'description' => 'Drafting Room', 'capacity' => 20, 'location' => 'Science Building, 6th Floor', 'building' => 'S', 'floor' => 6, 'type' => 'drafting-room'],
    ['id' => 'room-S604', 'name' => 'Drafting Room', 'description' => 'Drafting Room', 'capacity' => 25, 'location' => 'Science Building, 6th Floor', 'building' => 'S', 'floor' => 6, 'type' => 'drafting-room'],
    ['id' => 'room-S605', 'name' => 'Drafting Room', 'description' => 'Drafting Room', 'capacity' => 30, 'location' => 'Science Building, 6th Floor', 'building' => 'S', 'floor' => 6, 'type' => 'drafting-room'],
    ['id' => 'room-S606', 'name' => 'Drafting Room', 'description' => 'Drafting Room', 'capacity' => 28, 'location' => 'Science Building, 6th Floor', 'building' => 'S', 'floor' => 6, 'type' => 'drafting-room'],

    // =============== EDS BUILDING (N) - LABS ===============
    ['id' => 'room-2001', 'name' => 'Nutrition Lab/Lecture', 'description' => 'Nutrition Lab/Lecture', 'capacity' => 52, 'location' => 'EDS Building, 2nd Floor', 'building' => 'N', 'floor' => 2, 'type' => 'nutrition-lab'],
    ['id' => 'room-2003', 'name' => 'Maternity/Lecture', 'description' => 'Maternity/Lecture', 'capacity' => 50, 'location' => 'EDS Building, 2nd Floor', 'building' => 'N', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-2004', 'name' => 'Nursing Lab', 'description' => 'Nursing Lab', 'capacity' => 60, 'location' => 'EDS Building, 2nd Floor', 'building' => 'N', 'floor' => 2, 'type' => 'nursing-lab'],
    ['id' => 'room-3001', 'name' => 'E-learning', 'description' => 'E-learning', 'capacity' => 40, 'location' => 'EDS Building, 3rd Floor', 'building' => 'N', 'floor' => 3, 'type' => 'computer-lab'],
    ['id' => 'room-3002', 'name' => 'E-learning', 'description' => 'E-learning', 'capacity' => 40, 'location' => 'EDS Building, 3rd Floor', 'building' => 'N', 'floor' => 3, 'type' => 'computer-lab'],
    ['id' => 'room-8005', 'name' => 'CON Health Center', 'description' => 'CON Health Center', 'capacity' => 25, 'location' => 'EDS Building, 8th Floor', 'building' => 'N', 'floor' => 8, 'type' => 'seminar-room'],

    // =============== BRS BUILDING (U) - LABS ===============
    ['id' => 'room-U101', 'name' => 'Drafting Room', 'description' => 'Drafting Room', 'capacity' => 28, 'location' => 'BRS Building, 1st Floor', 'building' => 'U', 'floor' => 1, 'type' => 'drafting-room'],
    ['id' => 'room-U103', 'name' => 'Masscom Laboratory', 'description' => 'Masscom Laboratory', 'capacity' => 25, 'location' => 'BRS Building, 1st Floor', 'building' => 'U', 'floor' => 1, 'type' => 'masscom-lab'],
    ['id' => 'room-U104', 'name' => 'Psychology Laboratory', 'description' => 'Psychology Laboratory', 'capacity' => 20, 'location' => 'BRS Building, 1st Floor', 'building' => 'U', 'floor' => 1, 'type' => 'psychology-lab'],
    ['id' => 'room-U201', 'name' => 'Computer Laboratory', 'description' => 'Computer Laboratory', 'capacity' => 45, 'location' => 'BRS Building, 2nd Floor', 'building' => 'U', 'floor' => 2, 'type' => 'computer-lab'],
    ['id' => 'room-U301', 'name' => 'FA Computer Lab', 'description' => 'FA Computer Lab', 'capacity' => 40, 'location' => 'BRS Building, 3rd Floor', 'building' => 'U', 'floor' => 3, 'type' => 'computer-lab'],
    ['id' => 'room-U302', 'name' => 'Computer Laboratory', 'description' => 'Computer Laboratory', 'capacity' => 40, 'location' => 'BRS Building, 3rd Floor', 'building' => 'U', 'floor' => 3, 'type' => 'computer-lab'],
    ['id' => 'room-U303', 'name' => 'Computer Laboratory', 'description' => 'Computer Laboratory', 'capacity' => 40, 'location' => 'BRS Building, 3rd Floor', 'building' => 'U', 'floor' => 3, 'type' => 'computer-lab'],
    ['id' => 'room-U304', 'name' => 'Computer Laboratory', 'description' => 'Computer Laboratory', 'capacity' => 40, 'location' => 'BRS Building, 2nd Floor', 'building' => 'U', 'floor' => 2, 'type' => 'computer-lab'],
    ['id' => 'room-U601', 'name' => 'Mootcourt', 'description' => 'Mootcourt', 'capacity' => 41, 'location' => 'BRS Building, 6th Floor', 'building' => 'U', 'floor' => 6, 'type' => 'classroom'],

    // =============== CHTM BUILDING (F) - LABS ===============
    ['id' => 'room-F406', 'name' => 'Tribu Cafeteria', 'description' => 'Tribu Cafeteria', 'capacity' => 25, 'location' => 'CHTM Building, 4th Floor', 'building' => 'F', 'floor' => 4, 'type' => 'cafeteria'],
    ['id' => 'room-F601', 'name' => 'Hotel', 'description' => 'Hotel', 'capacity' => 2, 'location' => 'CHTM Building, 6th Floor', 'building' => 'F', 'floor' => 6, 'type' => 'hotel-room'],
    ['id' => 'room-F602', 'name' => 'Hotel', 'description' => 'Hotel', 'capacity' => 2, 'location' => 'CHTM Building, 6th Floor', 'building' => 'F', 'floor' => 6, 'type' => 'hotel-room'],
    ['id' => 'room-F603', 'name' => 'Hotel', 'description' => 'Hotel', 'capacity' => 2, 'location' => 'CHTM Building, 6th Floor', 'building' => 'F', 'floor' => 6, 'type' => 'hotel-room'],
    ['id' => 'room-F604', 'name' => 'Hotel', 'description' => 'Hotel', 'capacity' => 2, 'location' => 'CHTM Building, 6th Floor', 'building' => 'F', 'floor' => 6, 'type' => 'hotel-room'],
    ['id' => 'room-F605', 'name' => 'Hotel', 'description' => 'Hotel', 'capacity' => 1, 'location' => 'CHTM Building, 6th Floor', 'building' => 'F', 'floor' => 6, 'type' => 'hotel-room'],
    ['id' => 'room-F606', 'name' => 'Hotel', 'description' => 'Hotel', 'capacity' => 2, 'location' => 'CHTM Building, 6th Floor', 'building' => 'F', 'floor' => 6, 'type' => 'hotel-room'],
    ['id' => 'room-F607', 'name' => 'Hotel (Masters Bed Room)', 'description' => 'Hotel (Masters Bed Room)', 'capacity' => 2, 'location' => 'CHTM Building, 6th Floor', 'building' => 'F', 'floor' => 6, 'type' => 'hotel-room'],
    ['id' => 'room-F608', 'name' => 'Hotel (Masters Bed Room)', 'description' => 'Hotel (Masters Bed Room)', 'capacity' => 1, 'location' => 'CHTM Building, 6th Floor', 'building' => 'F', 'floor' => 6, 'type' => 'hotel-room'],
    ['id' => 'room-F704', 'name' => 'Lec-Demo Culinary Studio', 'description' => 'Lec-Demo Culinary Studio', 'capacity' => 40, 'location' => 'CHTM Building, 7th Floor', 'building' => 'F', 'floor' => 7, 'type' => 'culinary-lab'],
    ['id' => 'room-F801', 'name' => 'Lec/Lab', 'description' => 'Lec/Lab', 'capacity' => 44, 'location' => 'CHTM Building, 8th Floor', 'building' => 'F', 'floor' => 8, 'type' => 'classroom'],
    ['id' => 'room-F805', 'name' => 'Lec/Lab', 'description' => 'LEC/LAB', 'capacity' => 44, 'location' => 'CHTM Building, 8th Floor', 'building' => 'F', 'floor' => 8, 'type' => 'classroom'],
    ['id' => 'room-F901', 'name' => 'Kitchen/Baking Lab', 'description' => 'Kitchen/Baking Lab', 'capacity' => 30, 'location' => 'CHTM Building, 9th Floor', 'building' => 'F', 'floor' => 9, 'type' => 'culinary-lab'],
    ['id' => 'room-F902', 'name' => 'Baking Lab', 'description' => 'Baking Lab', 'capacity' => 30, 'location' => 'CHTM Building, 9th Floor', 'building' => 'F', 'floor' => 9, 'type' => 'culinary-lab'],
    ['id' => 'room-F1001B', 'name' => 'Canao Restaurant & Creativity Hall', 'description' => 'Canao Restaurant & Creativity Hall', 'capacity' => 100, 'location' => 'CHTM Building, 10th Floor', 'building' => 'F', 'floor' => 10, 'type' => 'classroom'],
    ['id' => 'room-F1001A', 'name' => 'Canao Restaurant & Creativity Hall (Low Ceiling)', 'description' => 'Canao Restaurant & Creativity Hall (Low Ceiling)', 'capacity' => 30, 'location' => 'CHTM Building, 10th Floor', 'building' => 'F', 'floor' => 10, 'type' => 'classroom'],

    // =============== PE BUILDING (G) - SPECIAL ROOMS ===============
    ['id' => 'room-G311-312', 'name' => 'Dancing Hall', 'description' => 'Dancing Hall', 'capacity' => 100, 'location' => 'PE Building, 3rd Floor', 'building' => 'G', 'floor' => 3, 'type' => 'dancing-hall'],
    ['id' => 'room-G306', 'name' => 'Firing Range', 'description' => 'Firing Range', 'capacity' => 20, 'location' => 'PE Building, 3rd Floor', 'building' => 'G', 'floor' => 3, 'type' => 'firing-range'],

    // ===============================================
    // LECTURE ROOMS / CLASSROOMS
    // ===============================================

    // =============== MAIN BUILDING (M) - CLASSROOMS ===============
    ['id' => 'room-M201', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Main Building, 2nd Floor', 'building' => 'M', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-M204', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 42, 'location' => 'Main Building, 2nd Floor', 'building' => 'M', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-M205', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 48, 'location' => 'Main Building, 2nd Floor', 'building' => 'M', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-M206', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 40, 'location' => 'Main Building, 2nd Floor', 'building' => 'M', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-M207', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 48, 'location' => 'Main Building, 2nd Floor', 'building' => 'M', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-M208', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 48, 'location' => 'Main Building, 2nd Floor', 'building' => 'M', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-M210', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'Main Building, 2nd Floor', 'building' => 'M', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-M301', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Main Building, 3rd Floor', 'building' => 'M', 'floor' => 3, 'type' => 'classroom'],

    // =============== SCIENCE BUILDING (S) - BASEMENT CLASSROOMS ===============
    ['id' => 'room-S006', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, Basement', 'building' => 'S', 'floor' => 0, 'type' => 'classroom'],
    ['id' => 'room-S007', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, Basement', 'building' => 'S', 'floor' => 0, 'type' => 'classroom'],
    ['id' => 'room-S008', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, Basement', 'building' => 'S', 'floor' => 0, 'type' => 'classroom'],
    ['id' => 'room-S009', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, Basement', 'building' => 'S', 'floor' => 0, 'type' => 'classroom'],
    ['id' => 'room-S011', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, Basement', 'building' => 'S', 'floor' => 0, 'type' => 'classroom'],
    ['id' => 'room-S013', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, Basement', 'building' => 'S', 'floor' => 0, 'type' => 'classroom'],
    ['id' => 'room-S014', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'Science Building, Basement', 'building' => 'S', 'floor' => 0, 'type' => 'classroom'],
    ['id' => 'room-S015', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, Basement', 'building' => 'S', 'floor' => 0, 'type' => 'classroom'],

    // =============== SCIENCE BUILDING (S) - 1ST FLOOR CLASSROOMS ===============
    ['id' => 'room-S109', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, 1st Floor', 'building' => 'S', 'floor' => 1, 'type' => 'classroom'],
    ['id' => 'room-S110', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'Science Building, 1st Floor', 'building' => 'S', 'floor' => 1, 'type' => 'classroom'],
    ['id' => 'room-S112', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, 1st Floor', 'building' => 'S', 'floor' => 1, 'type' => 'classroom'],
    ['id' => 'room-S114', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, 1st Floor', 'building' => 'S', 'floor' => 1, 'type' => 'classroom'],
    ['id' => 'room-S122', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, 1st Floor', 'building' => 'S', 'floor' => 1, 'type' => 'classroom'],

    // =============== SCIENCE BUILDING (S) - 2ND FLOOR CLASSROOMS ===============
    ['id' => 'room-S220', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-S221', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-S223', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-S224b', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-S225', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-S226', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-S228', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-S230', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-S231', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-S233', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-S234', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 30, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-S235', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-S236', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-S237', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-S242', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-S243', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 48, 'location' => 'Science Building, 2nd Floor', 'building' => 'S', 'floor' => 2, 'type' => 'classroom'],

    // =============== SCIENCE BUILDING (S) - 3RD FLOOR CLASSROOMS ===============
    ['id' => 'room-S319', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, 3rd Floor', 'building' => 'S', 'floor' => 3, 'type' => 'classroom'],
    ['id' => 'room-S320', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, 3rd Floor', 'building' => 'S', 'floor' => 3, 'type' => 'classroom'],
    ['id' => 'room-S321', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 44, 'location' => 'Science Building, 3rd Floor', 'building' => 'S', 'floor' => 3, 'type' => 'classroom'],
    ['id' => 'room-S322', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, 3rd Floor', 'building' => 'S', 'floor' => 3, 'type' => 'classroom'],

    // =============== SCIENCE BUILDING (S) - 4TH FLOOR CLASSROOMS ===============
    ['id' => 'room-S407', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, 4th Floor', 'building' => 'S', 'floor' => 4, 'type' => 'classroom'],
    ['id' => 'room-S408', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'Science Building, 4th Floor', 'building' => 'S', 'floor' => 4, 'type' => 'classroom'],
    ['id' => 'room-S409', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'Science Building, 4th Floor', 'building' => 'S', 'floor' => 4, 'type' => 'classroom'],

    // =============== SCIENCE BUILDING (S) - 6TH FLOOR CLASSROOMS ===============
    ['id' => 'room-S601', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 30, 'location' => 'Science Building, 6th Floor', 'building' => 'S', 'floor' => 6, 'type' => 'classroom'],

    // =============== EDS BUILDING (N) - 3RD FLOOR CLASSROOMS ===============
    ['id' => 'room-3004', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'EDS Building, 3rd Floor', 'building' => 'N', 'floor' => 3, 'type' => 'classroom'],
    ['id' => 'room-3005', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 40, 'location' => 'EDS Building, 3rd Floor', 'building' => 'N', 'floor' => 3, 'type' => 'classroom'],
    ['id' => 'room-3007', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'EDS Building, 3rd Floor', 'building' => 'N', 'floor' => 3, 'type' => 'classroom'],

    // =============== EDS BUILDING (N) - 4TH FLOOR CLASSROOMS ===============
    ['id' => 'room-4003', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 4th Floor', 'building' => 'N', 'floor' => 4, 'type' => 'classroom'],
    ['id' => 'room-4004', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 4th Floor', 'building' => 'N', 'floor' => 4, 'type' => 'classroom'],
    ['id' => 'room-4007', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 4th Floor', 'building' => 'N', 'floor' => 4, 'type' => 'classroom'],

    // =============== EDS BUILDING (N) - 5TH FLOOR CLASSROOMS ===============
    ['id' => 'room-5003', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 5th Floor', 'building' => 'N', 'floor' => 5, 'type' => 'classroom'],
    ['id' => 'room-5004', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 5th Floor', 'building' => 'N', 'floor' => 5, 'type' => 'classroom'],
    ['id' => 'room-5005', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 5th Floor', 'building' => 'N', 'floor' => 5, 'type' => 'classroom'],

    // =============== EDS BUILDING (N) - 6TH FLOOR CLASSROOMS ===============
    ['id' => 'room-6001', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 6th Floor', 'building' => 'N', 'floor' => 6, 'type' => 'classroom'],
    ['id' => 'room-6002', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 6th Floor', 'building' => 'N', 'floor' => 6, 'type' => 'classroom'],
    ['id' => 'room-6003', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 6th Floor', 'building' => 'N', 'floor' => 6, 'type' => 'classroom'],
    ['id' => 'room-6004', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 6th Floor', 'building' => 'N', 'floor' => 6, 'type' => 'classroom'],
    ['id' => 'room-6005', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 6th Floor', 'building' => 'N', 'floor' => 6, 'type' => 'classroom'],
    ['id' => 'room-6006', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 6th Floor', 'building' => 'N', 'floor' => 6, 'type' => 'classroom'],
    ['id' => 'room-6007', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 6th Floor', 'building' => 'N', 'floor' => 6, 'type' => 'classroom'],

    // =============== EDS BUILDING (N) - 7TH FLOOR CLASSROOMS ===============
    ['id' => 'room-7001', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 7th Floor', 'building' => 'N', 'floor' => 7, 'type' => 'classroom'],
    ['id' => 'room-7002', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 7th Floor', 'building' => 'N', 'floor' => 7, 'type' => 'classroom'],
    ['id' => 'room-7003', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 7th Floor', 'building' => 'N', 'floor' => 7, 'type' => 'classroom'],
    ['id' => 'room-7004', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 7th Floor', 'building' => 'N', 'floor' => 7, 'type' => 'classroom'],
    ['id' => 'room-7005', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 7th Floor', 'building' => 'N', 'floor' => 7, 'type' => 'classroom'],
    ['id' => 'room-7006', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 7th Floor', 'building' => 'N', 'floor' => 7, 'type' => 'classroom'],
    ['id' => 'room-7007', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 7th Floor', 'building' => 'N', 'floor' => 7, 'type' => 'classroom'],

    // =============== EDS BUILDING (N) - 8TH FLOOR CLASSROOMS ===============
    ['id' => 'room-8001', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 8th Floor', 'building' => 'N', 'floor' => 8, 'type' => 'classroom'],
    ['id' => 'room-8002', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 8th Floor', 'building' => 'N', 'floor' => 8, 'type' => 'classroom'],
    ['id' => 'room-8003', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 8th Floor', 'building' => 'N', 'floor' => 8, 'type' => 'classroom'],
    ['id' => 'room-8004', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 8th Floor', 'building' => 'N', 'floor' => 8, 'type' => 'classroom'],
    ['id' => 'room-8006', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 8th Floor', 'building' => 'N', 'floor' => 8, 'type' => 'classroom'],
    ['id' => 'room-8007', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'EDS Building, 8th Floor', 'building' => 'N', 'floor' => 8, 'type' => 'classroom'],

    // =============== EDS BUILDING (N) - 9TH FLOOR CLASSROOMS ===============
    ['id' => 'room-9001', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'EDS Building, 9th Floor', 'building' => 'N', 'floor' => 9, 'type' => 'classroom'],

    // =============== PE BUILDING (G) - CLASSROOMS ===============
    ['id' => 'room-G201', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'PE Building, 2nd Floor', 'building' => 'G', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-G202', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'PE Building, 2nd Floor', 'building' => 'G', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-G203', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'PE Building, 2nd Floor', 'building' => 'G', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-G204', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 30, 'location' => 'PE Building, 2nd Floor', 'building' => 'G', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-G403', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'PE Building, 4th Floor', 'building' => 'G', 'floor' => 4, 'type' => 'classroom'],
    ['id' => 'room-G502', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 40, 'location' => 'PE Building, 5th Floor', 'building' => 'G', 'floor' => 5, 'type' => 'classroom'],
    ['id' => 'room-G503', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'PE Building, 5th Floor', 'building' => 'G', 'floor' => 5, 'type' => 'classroom'],
    ['id' => 'room-G504', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'PE Building, 5th Floor', 'building' => 'G', 'floor' => 5, 'type' => 'classroom'],

    // =============== BRS BUILDING (U) - CLASSROOMS ===============
    ['id' => 'room-U102', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 30, 'location' => 'BRS Building, 1st Floor', 'building' => 'U', 'floor' => 1, 'type' => 'classroom'],
    ['id' => 'room-U202', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'BRS Building, 2nd Floor', 'building' => 'U', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-U203', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'BRS Building, 2nd Floor', 'building' => 'U', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-U204', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 40, 'location' => 'BRS Building, 2nd Floor', 'building' => 'U', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-U205', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 40, 'location' => 'BRS Building, 2nd Floor', 'building' => 'U', 'floor' => 2, 'type' => 'classroom'],
    ['id' => 'room-U401', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'BRS Building, 4th Floor', 'building' => 'U', 'floor' => 4, 'type' => 'lecture-hall'],
    ['id' => 'room-U402', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'BRS Building, 4th Floor', 'building' => 'U', 'floor' => 4, 'type' => 'lecture-hall'],
    ['id' => 'room-U403', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'BRS Building, 4th Floor', 'building' => 'U', 'floor' => 4, 'type' => 'lecture-hall'],
    ['id' => 'room-U404', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 40, 'location' => 'BRS Building, 4th Floor', 'building' => 'U', 'floor' => 4, 'type' => 'classroom'],
    ['id' => 'room-U405', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 40, 'location' => 'BRS Building, 4th Floor', 'building' => 'U', 'floor' => 4, 'type' => 'classroom'],
    ['id' => 'room-U701', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'BRS Building, 7th Floor', 'building' => 'U', 'floor' => 7, 'type' => 'lecture-hall'],
    ['id' => 'room-U702', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'BRS Building, 7th Floor', 'building' => 'U', 'floor' => 7, 'type' => 'lecture-hall'],
    ['id' => 'room-U703', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'BRS Building, 7th Floor', 'building' => 'U', 'floor' => 7, 'type' => 'lecture-hall'],
    ['id' => 'room-U704', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 40, 'location' => 'BRS Building, 7th Floor', 'building' => 'U', 'floor' => 7, 'type' => 'classroom'],
    ['id' => 'room-U705', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'BRS Building, 7th Floor', 'building' => 'U', 'floor' => 7, 'type' => 'lecture-hall'],
    ['id' => 'room-U706', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'BRS Building, 7th Floor', 'building' => 'U', 'floor' => 7, 'type' => 'lecture-hall'],
    ['id' => 'room-U801', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'BRS Building, 8th Floor', 'building' => 'U', 'floor' => 8, 'type' => 'lecture-hall'],
    ['id' => 'room-U802', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'BRS Building, 8th Floor', 'building' => 'U', 'floor' => 8, 'type' => 'lecture-hall'],
    ['id' => 'room-U803', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'BRS Building, 8th Floor', 'building' => 'U', 'floor' => 8, 'type' => 'lecture-hall'],
    ['id' => 'room-U804', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 40, 'location' => 'BRS Building, 8th Floor', 'building' => 'U', 'floor' => 8, 'type' => 'classroom'],
    ['id' => 'room-U805', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'BRS Building, 8th Floor', 'building' => 'U', 'floor' => 8, 'type' => 'lecture-hall'],
    ['id' => 'room-U806', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'BRS Building, 8th Floor', 'building' => 'U', 'floor' => 8, 'type' => 'lecture-hall'],
    ['id' => 'room-U901', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'BRS Building, 9th Floor', 'building' => 'U', 'floor' => 9, 'type' => 'lecture-hall'],
    ['id' => 'room-U902', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'BRS Building, 9th Floor', 'building' => 'U', 'floor' => 9, 'type' => 'lecture-hall'],
    ['id' => 'room-U903', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'BRS Building, 9th Floor', 'building' => 'U', 'floor' => 9, 'type' => 'lecture-hall'],
    ['id' => 'room-U904', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'BRS Building, 9th Floor', 'building' => 'U', 'floor' => 9, 'type' => 'lecture-hall'],
    ['id' => 'room-U906', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'BRS Building, 9th Floor', 'building' => 'U', 'floor' => 9, 'type' => 'lecture-hall'],
    ['id' => 'room-U907', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 40, 'location' => 'BRS Building, 9th Floor', 'building' => 'U', 'floor' => 9, 'type' => 'classroom'],
    ['id' => 'room-U1001', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 40, 'location' => 'BRS Building, 10th Floor', 'building' => 'U', 'floor' => 10, 'type' => 'classroom'],
    ['id' => 'room-U1002', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 40, 'location' => 'BRS Building, 10th Floor', 'building' => 'U', 'floor' => 10, 'type' => 'classroom'],
    ['id' => 'room-U1003', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 40, 'location' => 'BRS Building, 10th Floor', 'building' => 'U', 'floor' => 10, 'type' => 'classroom'],
    ['id' => 'room-U1004', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 40, 'location' => 'BRS Building, 10th Floor', 'building' => 'U', 'floor' => 10, 'type' => 'classroom'],
    ['id' => 'room-U1006', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 40, 'location' => 'BRS Building, 10th Floor', 'building' => 'U', 'floor' => 10, 'type' => 'classroom'],

    // =============== CHTM BUILDING (F) - CLASSROOMS ===============
    ['id' => 'room-F400', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'CHTM Building, 4th Floor', 'building' => 'F', 'floor' => 4, 'type' => 'classroom'],
    ['id' => 'room-F405', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'CHTM Building, 4th Floor', 'building' => 'F', 'floor' => 4, 'type' => 'classroom'],
    ['id' => 'room-F501', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'CHTM Building, 5th Floor', 'building' => 'F', 'floor' => 5, 'type' => 'classroom'],
    ['id' => 'room-F502', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'CHTM Building, 5th Floor', 'building' => 'F', 'floor' => 5, 'type' => 'classroom'],
    ['id' => 'room-F503', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 45, 'location' => 'CHTM Building, 5th Floor', 'building' => 'F', 'floor' => 5, 'type' => 'classroom'],
    ['id' => 'room-F504', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 50, 'location' => 'CHTM Building, 5th Floor', 'building' => 'F', 'floor' => 5, 'type' => 'classroom'],
    ['id' => 'room-F506', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 40, 'location' => 'CHTM Building, 5th Floor', 'building' => 'F', 'floor' => 5, 'type' => 'classroom'],
    ['id' => 'room-F701', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 40, 'location' => 'CHTM Building, 7th Floor', 'building' => 'F', 'floor' => 7, 'type' => 'classroom'],
    ['id' => 'room-F702', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 40, 'location' => 'CHTM Building, 7th Floor', 'building' => 'F', 'floor' => 7, 'type' => 'classroom'],
    ['id' => 'room-F703', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 40, 'location' => 'CHTM Building, 7th Floor', 'building' => 'F', 'floor' => 7, 'type' => 'classroom'],
    ['id' => 'room-F706', 'name' => 'Classroom/Lecture Hall', 'description' => 'Lecture Room', 'capacity' => 40, 'location' => 'CHTM Building, 7th Floor', 'building' => 'F', 'floor' => 7, 'type' => 'classroom'],
];

// =============== HELPER FUNCTIONS ===============

/**
 * Get all rooms
 */
function getAllRooms() {
    global $ROOMS;
    return $ROOMS;
}

/**
 * Get total room count
 */
function getTotalRooms() {
    global $ROOMS;
    return count($ROOMS);
}

/**
 * Get room by ID
 */
function getRoomById($roomId) {
    global $ROOMS;
    foreach ($ROOMS as $room) {
        if ($room['id'] === $roomId) {
            return $room;
        }
    }
    return null;
}

/**
 * Get rooms by building
 */
function getRoomsByBuilding($buildingId) {
    global $ROOMS;
    return array_values(array_filter($ROOMS, function($room) use ($buildingId) {
        return $room['building'] === $buildingId;
    }));
}

/**
 * Get rooms by floor
 */
function getRoomsByFloor($buildingId, $floor) {
    global $ROOMS;
    return array_values(array_filter($ROOMS, function($room) use ($buildingId, $floor) {
        return $room['building'] === $buildingId && $room['floor'] === $floor;
    }));
}

/**
 * Get rooms by type
 */
function getRoomsByType($type) {
    global $ROOMS;
    return array_values(array_filter($ROOMS, function($room) use ($type) {
        return $room['type'] === $type;
    }));
}

/**
 * Get rooms with minimum capacity
 */
function getRoomsWithCapacity($minCapacity) {
    global $ROOMS;
    return array_values(array_filter($ROOMS, function($room) use ($minCapacity) {
        return $room['capacity'] >= $minCapacity;
    }));
}

/**
 * Search rooms by name or description
 */
function searchRooms($query) {
    global $ROOMS;
    $query = strtolower($query);
    return array_values(array_filter($ROOMS, function($room) use ($query) {
        return strpos(strtolower($room['name']), $query) !== false ||
               strpos(strtolower($room['description'] ?? ''), $query) !== false ||
               strpos(strtolower($room['location']), $query) !== false ||
               strpos(strtolower($room['id']), $query) !== false;
    }));
}

/**
 * Get room statistics
 */
function getRoomStatistics() {
    global $ROOMS;
    
    $stats = [
        'total' => count($ROOMS),
        'byBuilding' => [],
        'byType' => [],
        'totalCapacity' => 0,
        'avgCapacity' => 0
    ];
    
    foreach ($ROOMS as $room) {
        // Count by building
        $building = $room['building'];
        if (!isset($stats['byBuilding'][$building])) {
            $stats['byBuilding'][$building] = 0;
        }
        $stats['byBuilding'][$building]++;
        
        // Count by type
        $type = $room['type'];
        if (!isset($stats['byType'][$type])) {
            $stats['byType'][$type] = 0;
        }
        $stats['byType'][$type]++;
        
        // Total capacity
        $stats['totalCapacity'] += $room['capacity'];
    }
    
    $stats['avgCapacity'] = $stats['total'] > 0 ? round($stats['totalCapacity'] / $stats['total']) : 0;
    
    return $stats;
}

/**
 * Get standard time slots
 */
function getTimeSlots() {
    global $STANDARD_TIME_SLOTS;
    return $STANDARD_TIME_SLOTS;
}

/**
 * Get school days
 */
function getSchoolDays() {
    global $SCHOOL_DAYS;
    return $SCHOOL_DAYS;
}

/**
 * Get room types
 */
function getRoomTypes() {
    global $ROOM_TYPES;
    return $ROOM_TYPES;
}

/**
 * Get rooms as dropdown options (for select elements)
 */
function getRoomsAsOptions() {
    global $ROOMS;
    $options = [];
    foreach ($ROOMS as $room) {
        $roomNumber = str_replace('room-', '', $room['id']);
        $options[] = [
            'value' => $room['id'],
            'label' => $roomNumber . ' - ' . $room['name'] . ' (' . $room['capacity'] . ' seats)'
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
 * Get unique room types from data
 */
function getUniqueRoomTypes() {
    global $ROOMS;
    $types = [];
    foreach ($ROOMS as $room) {
        if (!isset($types[$room['type']])) {
            $types[$room['type']] = ucwords(str_replace(['-', '/'], [' ', '/'], $room['type']));
        }
    }
    return $types;
}
