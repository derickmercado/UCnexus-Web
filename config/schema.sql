-- UC Nexus Database Schema
-- Database: ucnexus_db (Local XAMPP)

-- ===============================================
-- BUILDINGS TABLE
-- ===============================================
CREATE TABLE IF NOT EXISTS `buildings` (
    `id` VARCHAR(10) PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `full_name` VARCHAR(255) NOT NULL,
    `floors` INT NOT NULL DEFAULT 1,
    `color` VARCHAR(20) DEFAULT '#3498db',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ===============================================
-- ROOM TYPES TABLE
-- ===============================================
CREATE TABLE IF NOT EXISTS `room_types` (
    `id` VARCHAR(50) PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ===============================================
-- ROOMS TABLE
-- ===============================================
CREATE TABLE IF NOT EXISTS `rooms` (
    `id` VARCHAR(50) PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `capacity` INT NOT NULL DEFAULT 30,
    `location` VARCHAR(255),
    `building_id` VARCHAR(10),
    `floor` INT NOT NULL DEFAULT 1,
    `type` VARCHAR(50),
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`building_id`) REFERENCES `buildings`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`type`) REFERENCES `room_types`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ===============================================
-- DEPARTMENTS TABLE
-- ===============================================
CREATE TABLE IF NOT EXISTS `departments` (
    `id` VARCHAR(20) PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ===============================================
-- SCHEDULES TABLE
-- ===============================================
CREATE TABLE IF NOT EXISTS `schedules` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `class_code` VARCHAR(50) NOT NULL,
    `class_name` VARCHAR(255) NOT NULL,
    `room_id` VARCHAR(50),
    `room_display` VARCHAR(255),
    `instructor` VARCHAR(255),
    `department_id` VARCHAR(20),
    `class_size` INT DEFAULT 0,
    `schedule_date` DATE,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `days` VARCHAR(100),
    `semester` VARCHAR(50),
    `school_year` VARCHAR(20),
    `is_active` TINYINT(1) DEFAULT 1,
    `has_conflict` TINYINT(1) DEFAULT 0,
    `conflict_with` VARCHAR(255) DEFAULT NULL,
    -- CIT/CC Dual Schedule Fields
    `is_citcc` TINYINT(1) DEFAULT 0,
    `lec_room` VARCHAR(255),
    `lec_instructor` VARCHAR(255),
    `lec_days` VARCHAR(100),
    `lec_start_time` TIME,
    `lec_end_time` TIME,
    `lab_room` VARCHAR(255),
    `lab_instructor` VARCHAR(255),
    `lab_days` VARCHAR(100),
    `lab_start_time` TIME,
    `lab_end_time` TIME,
    -- Year Level, Term, Block Fields
    `year_level` VARCHAR(20),
    `term` VARCHAR(20),
    `block` VARCHAR(10),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ===============================================
-- TIME SLOTS TABLE
-- ===============================================
CREATE TABLE IF NOT EXISTS `time_slots` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `slot_order` INT DEFAULT 0
) ENGINE=InnoDB;

-- ===============================================
-- INSERT DEFAULT DATA
-- ===============================================

-- Insert Buildings
INSERT INTO `buildings` (`id`, `name`, `full_name`, `floors`, `color`) VALUES
('M', 'Main', 'Main Building', 3, '#3498db'),
('S', 'Science', 'Science Building', 6, '#2ecc71'),
('N', 'EDS', 'EDS Building', 9, '#9b59b6'),
('G', 'PE', 'PE Building', 5, '#e74c3c'),
('U', 'BRS', 'BRS Building', 10, '#f39c12'),
('F', 'CHTM', 'CHTM Building', 10, '#1abc9c')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Insert Room Types
INSERT INTO `room_types` (`id`, `name`) VALUES
('computer-lab', 'Computer Laboratory'),
('classroom', 'Classroom/Lecture Hall'),
('lecture-hall', 'Lecture Hall'),
('chemistry-lab', 'Chemistry Laboratory'),
('physics-lab', 'Physics Laboratory'),
('biology-lab', 'Biology Laboratory'),
('engineering-lab', 'Engineering Laboratory'),
('drafting-room', 'Drafting Room'),
('nursing-lab', 'Nursing Laboratory'),
('culinary-lab', 'Culinary Laboratory'),
('demo-room', 'Demo Room'),
('conference', 'Conference Room'),
('office', 'Office'),
('gym', 'Gymnasium'),
('hydro/fluid-mech-lab', 'Hydro/Fluid Mech Lab'),
('matti/soil-test-lab', 'Matti/Soil Test Lab'),
('gs/jhs-lab', 'GS & JHS Lab'),
('he-lab', 'HE Lab'),
('con-med-lab', 'Con Med Lab'),
('cea-computer-lab', 'CEA Computer Laboratory'),
('engineering-computer-lab', 'Engineering Computer Laboratory'),
('electronic/digital-lab', 'Electronic and Digital Lab'),
('cisco-lab', 'CISCO Laboratory'),
('thesis-room', 'Thesis/Acoustic Room'),
('nutrition-lab', 'Nutrition Lab'),
('masscom-lab', 'Masscom Laboratory'),
('psychology-lab', 'Psychology Laboratory'),
('cafeteria', 'Cafeteria'),
('hotel-room', 'Hotel Room'),
('dancing-hall', 'Dancing Hall'),
('firing-range', 'Firing Range'),
('seminar-room', 'Seminar Room'),
('other', 'Other')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Insert Departments
INSERT INTO `departments` (`id`, `name`) VALUES
('CCS', 'College of Computer Studies'),
('CEA', 'College of Engineering and Architecture'),
('CAS', 'College of Arts and Sciences'),
('CHTM', 'College of Hospitality and Tourism Management'),
('CED', 'College of Education'),
('CBA', 'College of Business Administration'),
('CON', 'College of Nursing'),
('PE', 'Physical Education'),
('SHS', 'Senior High School'),
('JHS', 'Junior High School')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Insert Standard Time Slots
INSERT INTO `time_slots` (`start_time`, `end_time`, `slot_order`) VALUES
('07:30:00', '08:50:00', 1),
('08:50:00', '10:10:00', 2),
('10:10:00', '11:30:00', 3),
('11:30:00', '12:50:00', 4),
('12:50:00', '14:10:00', 5),
('14:10:00', '15:30:00', 6),
('15:30:00', '16:50:00', 7),
('16:50:00', '18:10:00', 8),
('18:10:00', '19:30:00', 9),
('19:30:00', '20:50:00', 10)
ON DUPLICATE KEY UPDATE `slot_order` = VALUES(`slot_order`);

-- Insert All Rooms (196 rooms)
INSERT INTO `rooms` (`id`, `name`, `description`, `capacity`, `location`, `building_id`, `floor`, `type`) VALUES
-- MAIN BUILDING (M) - LABS
('room-M303', 'Computer Laboratory', 'Computer Laboratory', 40, 'Main Building, 3rd Floor', 'M', 3, 'computer-lab'),
('room-M304', 'Computer Laboratory', 'Computer Laboratory', 40, 'Main Building, 3rd Floor', 'M', 3, 'computer-lab'),
('room-M305', 'Computer Laboratory', 'Computer Laboratory', 40, 'Main Building, 3rd Floor', 'M', 3, 'computer-lab'),
('room-M306', 'Computer Laboratory', 'Computer Laboratory', 40, 'Main Building, 3rd Floor', 'M', 3, 'computer-lab'),
('room-M307', 'Computer Laboratory', 'Computer Laboratory', 43, 'Main Building, 3rd Floor', 'M', 3, 'computer-lab'),
-- SCIENCE BUILDING (S) - BASEMENT LABS
('room-S010', 'Hydro/Fluid Mech Lab', 'Hydro/Fluid Mech Lab', 45, 'Science Building, Basement', 'S', 0, 'hydro/fluid-mech-lab'),
('room-S012', 'Matti/Soil Test Lab', 'Matti/Soil Test Lab', 40, 'Science Building, Basement', 'S', 0, 'matti/soil-test-lab'),
('room-S016', 'Physics Lab', 'Physics Lab', 50, 'Science Building, Basement', 'S', 0, 'physics-lab'),
('room-S019', 'GS & JHS Lab', 'GS & JHS Lab', 45, 'Science Building, Basement', 'S', 0, 'gs/jhs-lab'),
('room-S020', 'HE Lab', 'HE Lab', 45, 'Science Building, Basement', 'S', 0, 'he-lab'),
-- SCIENCE BUILDING (S) - 1ST FLOOR LABS
('room-S106', 'GSJHS Computer Laboratory', 'GSJHS Computer Laboratory', 45, 'Science Building, 1st Floor', 'S', 1, 'computer-lab'),
('room-S107', 'Chemistry Laboratory', 'Chemistry Laboratory', 40, 'Science Building, 1st Floor', 'S', 1, 'chemistry-lab'),
('room-S111', 'Chemistry Laboratory', 'Chemistry Laboratory', 40, 'Science Building, 1st Floor', 'S', 1, 'chemistry-lab'),
('room-S113', 'TLE/THE/TVL-HE Demo Room', 'TLE/THE/TVL-HE Demo Room', 50, 'Science Building, 1st Floor', 'S', 1, 'demo-room'),
('room-S116', 'Chemistry Laboratory', 'Chemistry Laboratory', 40, 'Science Building, 1st Floor', 'S', 1, 'chemistry-lab'),
('room-S117', 'Chemistry Laboratory', 'Chemistry Laboratory', 40, 'Science Building, 1st Floor', 'S', 1, 'chemistry-lab'),
('room-S120', 'SHS Physics Laboratory', 'SHS Physics Laboratory', 40, 'Science Building, 1st Floor', 'S', 1, 'physics-lab'),
('room-S121', 'Con Med Lab', 'Con Med Lab', 40, 'Science Building, 1st Floor', 'S', 1, 'con-med-lab'),
-- SCIENCE BUILDING (S) - 2ND FLOOR LABS
('room-S213', 'CEA Computer Laboratory', 'CEA Computer Laboratory', 30, 'Science Building, 2nd Floor', 'S', 2, 'cea-computer-lab'),
('room-S218', 'Engr Computer Laboratory', 'Engineering Computer Laboratory', 45, 'Science Building, 2nd Floor', 'S', 2, 'engineering-computer-lab'),
('room-S227', 'Electronic and Digital Lab', 'Electronic and Digital Lab', 40, 'Science Building, 2nd Floor', 'S', 2, 'electronic/digital-lab'),
('room-S229', 'Biology Laboratory', 'Biology Laboratory', 30, 'Science Building, 2nd Floor', 'S', 2, 'biology-lab'),
('room-S232', 'Biology Laboratory', 'Biology Laboratory', 40, 'Science Building, 2nd Floor', 'S', 2, 'biology-lab'),
('room-S240', 'Biology Laboratory', 'Biology Laboratory', 40, 'Science Building, 2nd Floor', 'S', 2, 'biology-lab'),
-- SCIENCE BUILDING (S) - 3RD FLOOR LABS
('room-S311', 'CISCO Laboratory', 'CISCO Laboratory', 9, 'Science Building, 3rd Floor', 'S', 3, 'cisco-lab'),
('room-S312', 'Computer Laboratory', 'Computer Laboratory', 43, 'Science Building, 3rd Floor', 'S', 3, 'computer-lab'),
-- SCIENCE BUILDING (S) - 4TH FLOOR DRAFTING
('room-S413', 'Drafting Room', 'Drafting Room', 28, 'Science Building, 4th Floor', 'S', 4, 'drafting-room'),
('room-S414', 'Drafting Room', 'Drafting Room', 24, 'Science Building, 4th Floor', 'S', 4, 'drafting-room'),
('room-S415', 'Thesis/Acoustic/Drafting Room', 'Thesis/Acoustic/Drafting Room', 29, 'Science Building, 4th Floor', 'S', 4, 'thesis-room'),
-- SCIENCE BUILDING (S) - 5TH FLOOR DRAFTING
('room-S502', 'Drafting Room', 'Drafting Room', 30, 'Science Building, 5th Floor', 'S', 5, 'drafting-room'),
('room-S503', 'Drafting Room', 'Drafting Room', 24, 'Science Building, 5th Floor', 'S', 5, 'drafting-room'),
('room-S504', 'Drafting Room', 'Drafting Room', 32, 'Science Building, 5th Floor', 'S', 5, 'drafting-room'),
-- SCIENCE BUILDING (S) - 6TH FLOOR DRAFTING
('room-S602', 'Drafting Room', 'Drafting Room', 50, 'Science Building, 6th Floor', 'S', 6, 'drafting-room'),
('room-S603', 'Drafting Room', 'Drafting Room', 20, 'Science Building, 6th Floor', 'S', 6, 'drafting-room'),
('room-S604', 'Drafting Room', 'Drafting Room', 25, 'Science Building, 6th Floor', 'S', 6, 'drafting-room'),
('room-S605', 'Drafting Room', 'Drafting Room', 30, 'Science Building, 6th Floor', 'S', 6, 'drafting-room'),
('room-S606', 'Drafting Room', 'Drafting Room', 28, 'Science Building, 6th Floor', 'S', 6, 'drafting-room'),
-- EDS BUILDING (N) - LABS
('room-2001', 'Nutrition Lab/Lecture', 'Nutrition Lab/Lecture', 52, 'EDS Building, 2nd Floor', 'N', 2, 'nutrition-lab'),
('room-2003', 'Maternity/Lecture', 'Maternity/Lecture', 50, 'EDS Building, 2nd Floor', 'N', 2, 'classroom'),
('room-2004', 'Nursing Lab', 'Nursing Lab', 60, 'EDS Building, 2nd Floor', 'N', 2, 'nursing-lab'),
('room-3001', 'E-learning', 'E-learning', 40, 'EDS Building, 3rd Floor', 'N', 3, 'computer-lab'),
('room-3002', 'E-learning', 'E-learning', 40, 'EDS Building, 3rd Floor', 'N', 3, 'computer-lab'),
('room-8005', 'CON Health Center', 'CON Health Center', 25, 'EDS Building, 8th Floor', 'N', 8, 'seminar-room'),
-- BRS BUILDING (U) - LABS
('room-U101', 'Drafting Room', 'Drafting Room', 28, 'BRS Building, 1st Floor', 'U', 1, 'drafting-room'),
('room-U103', 'Masscom Laboratory', 'Masscom Laboratory', 25, 'BRS Building, 1st Floor', 'U', 1, 'masscom-lab'),
('room-U104', 'Psychology Laboratory', 'Psychology Laboratory', 20, 'BRS Building, 1st Floor', 'U', 1, 'psychology-lab'),
('room-U201', 'Computer Laboratory', 'Computer Laboratory', 45, 'BRS Building, 2nd Floor', 'U', 2, 'computer-lab'),
('room-U301', 'FA Computer Lab', 'FA Computer Lab', 40, 'BRS Building, 3rd Floor', 'U', 3, 'computer-lab'),
('room-U302', 'Computer Laboratory', 'Computer Laboratory', 40, 'BRS Building, 3rd Floor', 'U', 3, 'computer-lab'),
('room-U303', 'Computer Laboratory', 'Computer Laboratory', 40, 'BRS Building, 3rd Floor', 'U', 3, 'computer-lab'),
('room-U304', 'Computer Laboratory', 'Computer Laboratory', 40, 'BRS Building, 2nd Floor', 'U', 2, 'computer-lab'),
('room-U601', 'Mootcourt', 'Mootcourt', 41, 'BRS Building, 6th Floor', 'U', 6, 'classroom'),
-- CHTM BUILDING (F) - LABS
('room-F406', 'Tribu Cafeteria', 'Tribu Cafeteria', 25, 'CHTM Building, 4th Floor', 'F', 4, 'cafeteria'),
('room-F601', 'Hotel', 'Hotel', 2, 'CHTM Building, 6th Floor', 'F', 6, 'hotel-room'),
('room-F602', 'Hotel', 'Hotel', 2, 'CHTM Building, 6th Floor', 'F', 6, 'hotel-room'),
('room-F603', 'Hotel', 'Hotel', 2, 'CHTM Building, 6th Floor', 'F', 6, 'hotel-room'),
('room-F604', 'Hotel', 'Hotel', 2, 'CHTM Building, 6th Floor', 'F', 6, 'hotel-room'),
('room-F605', 'Hotel', 'Hotel', 1, 'CHTM Building, 6th Floor', 'F', 6, 'hotel-room'),
('room-F606', 'Hotel', 'Hotel', 2, 'CHTM Building, 6th Floor', 'F', 6, 'hotel-room'),
('room-F607', 'Hotel (Masters Bed Room)', 'Hotel (Masters Bed Room)', 2, 'CHTM Building, 6th Floor', 'F', 6, 'hotel-room'),
('room-F608', 'Hotel (Masters Bed Room)', 'Hotel (Masters Bed Room)', 1, 'CHTM Building, 6th Floor', 'F', 6, 'hotel-room'),
('room-F704', 'Lec-Demo Culinary Studio', 'Lec-Demo Culinary Studio', 40, 'CHTM Building, 7th Floor', 'F', 7, 'culinary-lab'),
('room-F801', 'Lec/Lab', 'Lec/Lab', 44, 'CHTM Building, 8th Floor', 'F', 8, 'classroom'),
('room-F805', 'Lec/Lab', 'LEC/LAB', 44, 'CHTM Building, 8th Floor', 'F', 8, 'classroom'),
('room-F901', 'Kitchen/Baking Lab', 'Kitchen/Baking Lab', 30, 'CHTM Building, 9th Floor', 'F', 9, 'culinary-lab'),
('room-F902', 'Baking Lab', 'Baking Lab', 30, 'CHTM Building, 9th Floor', 'F', 9, 'culinary-lab'),
('room-F1001B', 'Canao Restaurant & Creativity Hall', 'Canao Restaurant & Creativity Hall', 100, 'CHTM Building, 10th Floor', 'F', 10, 'classroom'),
('room-F1001A', 'Canao Restaurant & Creativity Hall (Low Ceiling)', 'Canao Restaurant & Creativity Hall (Low Ceiling)', 30, 'CHTM Building, 10th Floor', 'F', 10, 'classroom'),
-- PE BUILDING (G) - SPECIAL ROOMS
('room-G311-312', 'Dancing Hall', 'Dancing Hall', 100, 'PE Building, 3rd Floor', 'G', 3, 'dancing-hall'),
('room-G306', 'Firing Range', 'Firing Range', 20, 'PE Building, 3rd Floor', 'G', 3, 'firing-range'),
-- MAIN BUILDING (M) - CLASSROOMS
('room-M201', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Main Building, 2nd Floor', 'M', 2, 'classroom'),
('room-M204', 'Classroom/Lecture Hall', 'Lecture Room', 42, 'Main Building, 2nd Floor', 'M', 2, 'classroom'),
('room-M205', 'Classroom/Lecture Hall', 'Lecture Room', 48, 'Main Building, 2nd Floor', 'M', 2, 'classroom'),
('room-M206', 'Classroom/Lecture Hall', 'Lecture Room', 40, 'Main Building, 2nd Floor', 'M', 2, 'classroom'),
('room-M207', 'Classroom/Lecture Hall', 'Lecture Room', 48, 'Main Building, 2nd Floor', 'M', 2, 'classroom'),
('room-M208', 'Classroom/Lecture Hall', 'Lecture Room', 48, 'Main Building, 2nd Floor', 'M', 2, 'classroom'),
('room-M210', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'Main Building, 2nd Floor', 'M', 2, 'classroom'),
('room-M301', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Main Building, 3rd Floor', 'M', 3, 'classroom'),
-- SCIENCE BUILDING (S) - BASEMENT CLASSROOMS
('room-S006', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, Basement', 'S', 0, 'classroom'),
('room-S007', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, Basement', 'S', 0, 'classroom'),
('room-S008', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, Basement', 'S', 0, 'classroom'),
('room-S009', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, Basement', 'S', 0, 'classroom'),
('room-S011', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, Basement', 'S', 0, 'classroom'),
('room-S013', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, Basement', 'S', 0, 'classroom'),
('room-S014', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'Science Building, Basement', 'S', 0, 'classroom'),
('room-S015', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, Basement', 'S', 0, 'classroom'),
-- SCIENCE BUILDING (S) - 1ST FLOOR CLASSROOMS
('room-S109', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, 1st Floor', 'S', 1, 'classroom'),
('room-S110', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'Science Building, 1st Floor', 'S', 1, 'classroom'),
('room-S112', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, 1st Floor', 'S', 1, 'classroom'),
('room-S114', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, 1st Floor', 'S', 1, 'classroom'),
('room-S122', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, 1st Floor', 'S', 1, 'classroom'),
-- SCIENCE BUILDING (S) - 2ND FLOOR CLASSROOMS
('room-S220', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, 2nd Floor', 'S', 2, 'classroom'),
('room-S221', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, 2nd Floor', 'S', 2, 'classroom'),
('room-S223', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, 2nd Floor', 'S', 2, 'classroom'),
('room-S224b', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, 2nd Floor', 'S', 2, 'classroom'),
('room-S225', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, 2nd Floor', 'S', 2, 'classroom'),
('room-S226', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, 2nd Floor', 'S', 2, 'classroom'),
('room-S228', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'Science Building, 2nd Floor', 'S', 2, 'classroom'),
('room-S230', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, 2nd Floor', 'S', 2, 'classroom'),
('room-S231', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'Science Building, 2nd Floor', 'S', 2, 'classroom'),
('room-S233', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, 2nd Floor', 'S', 2, 'classroom'),
('room-S234', 'Classroom/Lecture Hall', 'Lecture Room', 30, 'Science Building, 2nd Floor', 'S', 2, 'classroom'),
('room-S235', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, 2nd Floor', 'S', 2, 'classroom'),
('room-S236', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, 2nd Floor', 'S', 2, 'classroom'),
('room-S237', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, 2nd Floor', 'S', 2, 'classroom'),
('room-S242', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, 2nd Floor', 'S', 2, 'classroom'),
('room-S243', 'Classroom/Lecture Hall', 'Lecture Room', 48, 'Science Building, 2nd Floor', 'S', 2, 'classroom'),
-- SCIENCE BUILDING (S) - 3RD FLOOR CLASSROOMS
('room-S319', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, 3rd Floor', 'S', 3, 'classroom'),
('room-S320', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, 3rd Floor', 'S', 3, 'classroom'),
('room-S321', 'Classroom/Lecture Hall', 'Lecture Room', 44, 'Science Building, 3rd Floor', 'S', 3, 'classroom'),
('room-S322', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, 3rd Floor', 'S', 3, 'classroom'),
-- SCIENCE BUILDING (S) - 4TH FLOOR CLASSROOMS
('room-S407', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, 4th Floor', 'S', 4, 'classroom'),
('room-S408', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'Science Building, 4th Floor', 'S', 4, 'classroom'),
('room-S409', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'Science Building, 4th Floor', 'S', 4, 'classroom'),
-- SCIENCE BUILDING (S) - 6TH FLOOR CLASSROOMS
('room-S601', 'Classroom/Lecture Hall', 'Lecture Room', 30, 'Science Building, 6th Floor', 'S', 6, 'classroom'),
-- EDS BUILDING (N) - CLASSROOMS
('room-3004', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'EDS Building, 3rd Floor', 'N', 3, 'classroom'),
('room-3005', 'Classroom/Lecture Hall', 'Lecture Room', 40, 'EDS Building, 3rd Floor', 'N', 3, 'classroom'),
('room-3007', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'EDS Building, 3rd Floor', 'N', 3, 'classroom'),
('room-4003', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 4th Floor', 'N', 4, 'classroom'),
('room-4004', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 4th Floor', 'N', 4, 'classroom'),
('room-4007', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 4th Floor', 'N', 4, 'classroom'),
('room-5003', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 5th Floor', 'N', 5, 'classroom'),
('room-5004', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 5th Floor', 'N', 5, 'classroom'),
('room-5005', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 5th Floor', 'N', 5, 'classroom'),
('room-6001', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 6th Floor', 'N', 6, 'classroom'),
('room-6002', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 6th Floor', 'N', 6, 'classroom'),
('room-6003', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 6th Floor', 'N', 6, 'classroom'),
('room-6004', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 6th Floor', 'N', 6, 'classroom'),
('room-6005', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 6th Floor', 'N', 6, 'classroom'),
('room-6006', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 6th Floor', 'N', 6, 'classroom'),
('room-6007', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 6th Floor', 'N', 6, 'classroom'),
('room-7001', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 7th Floor', 'N', 7, 'classroom'),
('room-7002', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 7th Floor', 'N', 7, 'classroom'),
('room-7003', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 7th Floor', 'N', 7, 'classroom'),
('room-7004', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 7th Floor', 'N', 7, 'classroom'),
('room-7005', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 7th Floor', 'N', 7, 'classroom'),
('room-7006', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 7th Floor', 'N', 7, 'classroom'),
('room-7007', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 7th Floor', 'N', 7, 'classroom'),
('room-8001', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 8th Floor', 'N', 8, 'classroom'),
('room-8002', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 8th Floor', 'N', 8, 'classroom'),
('room-8003', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 8th Floor', 'N', 8, 'classroom'),
('room-8004', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 8th Floor', 'N', 8, 'classroom'),
('room-8006', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 8th Floor', 'N', 8, 'classroom'),
('room-8007', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'EDS Building, 8th Floor', 'N', 8, 'classroom'),
('room-9001', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'EDS Building, 9th Floor', 'N', 9, 'classroom'),
-- PE BUILDING (G) - CLASSROOMS
('room-G201', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'PE Building, 2nd Floor', 'G', 2, 'classroom'),
('room-G202', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'PE Building, 2nd Floor', 'G', 2, 'classroom'),
('room-G203', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'PE Building, 2nd Floor', 'G', 2, 'classroom'),
('room-G204', 'Classroom/Lecture Hall', 'Lecture Room', 30, 'PE Building, 2nd Floor', 'G', 2, 'classroom'),
('room-G403', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'PE Building, 4th Floor', 'G', 4, 'classroom'),
('room-G502', 'Classroom/Lecture Hall', 'Lecture Room', 40, 'PE Building, 5th Floor', 'G', 5, 'classroom'),
('room-G503', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'PE Building, 5th Floor', 'G', 5, 'classroom'),
('room-G504', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'PE Building, 5th Floor', 'G', 5, 'classroom'),
-- BRS BUILDING (U) - CLASSROOMS
('room-U102', 'Classroom/Lecture Hall', 'Lecture Room', 30, 'BRS Building, 1st Floor', 'U', 1, 'classroom'),
('room-U202', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'BRS Building, 2nd Floor', 'U', 2, 'classroom'),
('room-U203', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'BRS Building, 2nd Floor', 'U', 2, 'classroom'),
('room-U204', 'Classroom/Lecture Hall', 'Lecture Room', 40, 'BRS Building, 2nd Floor', 'U', 2, 'classroom'),
('room-U205', 'Classroom/Lecture Hall', 'Lecture Room', 40, 'BRS Building, 2nd Floor', 'U', 2, 'classroom'),
('room-U401', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'BRS Building, 4th Floor', 'U', 4, 'lecture-hall'),
('room-U402', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'BRS Building, 4th Floor', 'U', 4, 'lecture-hall'),
('room-U403', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'BRS Building, 4th Floor', 'U', 4, 'lecture-hall'),
('room-U404', 'Classroom/Lecture Hall', 'Lecture Room', 40, 'BRS Building, 4th Floor', 'U', 4, 'classroom'),
('room-U405', 'Classroom/Lecture Hall', 'Lecture Room', 40, 'BRS Building, 4th Floor', 'U', 4, 'classroom'),
('room-U701', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'BRS Building, 7th Floor', 'U', 7, 'lecture-hall'),
('room-U702', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'BRS Building, 7th Floor', 'U', 7, 'lecture-hall'),
('room-U703', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'BRS Building, 7th Floor', 'U', 7, 'lecture-hall'),
('room-U704', 'Classroom/Lecture Hall', 'Lecture Room', 40, 'BRS Building, 7th Floor', 'U', 7, 'classroom'),
('room-U705', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'BRS Building, 7th Floor', 'U', 7, 'lecture-hall'),
('room-U706', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'BRS Building, 7th Floor', 'U', 7, 'lecture-hall'),
('room-U801', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'BRS Building, 8th Floor', 'U', 8, 'lecture-hall'),
('room-U802', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'BRS Building, 8th Floor', 'U', 8, 'lecture-hall'),
('room-U803', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'BRS Building, 8th Floor', 'U', 8, 'lecture-hall'),
('room-U804', 'Classroom/Lecture Hall', 'Lecture Room', 40, 'BRS Building, 8th Floor', 'U', 8, 'classroom'),
('room-U805', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'BRS Building, 8th Floor', 'U', 8, 'lecture-hall'),
('room-U806', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'BRS Building, 8th Floor', 'U', 8, 'lecture-hall'),
('room-U901', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'BRS Building, 9th Floor', 'U', 9, 'lecture-hall'),
('room-U902', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'BRS Building, 9th Floor', 'U', 9, 'lecture-hall'),
('room-U903', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'BRS Building, 9th Floor', 'U', 9, 'lecture-hall'),
('room-U904', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'BRS Building, 9th Floor', 'U', 9, 'lecture-hall'),
('room-U906', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'BRS Building, 9th Floor', 'U', 9, 'lecture-hall'),
('room-U907', 'Classroom/Lecture Hall', 'Lecture Room', 40, 'BRS Building, 9th Floor', 'U', 9, 'classroom'),
('room-U1001', 'Classroom/Lecture Hall', 'Lecture Room', 40, 'BRS Building, 10th Floor', 'U', 10, 'classroom'),
('room-U1002', 'Classroom/Lecture Hall', 'Lecture Room', 40, 'BRS Building, 10th Floor', 'U', 10, 'classroom'),
('room-U1003', 'Classroom/Lecture Hall', 'Lecture Room', 40, 'BRS Building, 10th Floor', 'U', 10, 'classroom'),
('room-U1004', 'Classroom/Lecture Hall', 'Lecture Room', 40, 'BRS Building, 10th Floor', 'U', 10, 'classroom'),
('room-U1006', 'Classroom/Lecture Hall', 'Lecture Room', 40, 'BRS Building, 10th Floor', 'U', 10, 'classroom'),
-- CHTM BUILDING (F) - CLASSROOMS
('room-F400', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'CHTM Building, 4th Floor', 'F', 4, 'classroom'),
('room-F405', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'CHTM Building, 4th Floor', 'F', 4, 'classroom'),
('room-F501', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'CHTM Building, 5th Floor', 'F', 5, 'classroom'),
('room-F502', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'CHTM Building, 5th Floor', 'F', 5, 'classroom'),
('room-F503', 'Classroom/Lecture Hall', 'Lecture Room', 45, 'CHTM Building, 5th Floor', 'F', 5, 'classroom'),
('room-F504', 'Classroom/Lecture Hall', 'Lecture Room', 50, 'CHTM Building, 5th Floor', 'F', 5, 'classroom'),
('room-F506', 'Classroom/Lecture Hall', 'Lecture Room', 40, 'CHTM Building, 5th Floor', 'F', 5, 'classroom'),
('room-F701', 'Classroom/Lecture Hall', 'Lecture Room', 40, 'CHTM Building, 7th Floor', 'F', 7, 'classroom'),
('room-F702', 'Classroom/Lecture Hall', 'Lecture Room', 40, 'CHTM Building, 7th Floor', 'F', 7, 'classroom'),
('room-F703', 'Classroom/Lecture Hall', 'Lecture Room', 40, 'CHTM Building, 7th Floor', 'F', 7, 'classroom'),
('room-F706', 'Classroom/Lecture Hall', 'Lecture Room', 40, 'CHTM Building, 7th Floor', 'F', 7, 'classroom')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Indexes for better performance (commented out for initial setup - can be added manually later)
-- CREATE INDEX `idx_rooms_building` ON `rooms` (`building_id`);
-- CREATE INDEX `idx_rooms_type` ON `rooms` (`type`);
-- CREATE INDEX `idx_schedules_room` ON `schedules` (`room_id`);
-- CREATE INDEX `idx_schedules_date` ON `schedules` (`schedule_date`);
-- CREATE INDEX `idx_schedules_dept` ON `schedules` (`department_id`);
