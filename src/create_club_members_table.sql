CREATE TABLE IF NOT EXISTS club_members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    club_id INT NOT NULL,
    student_name VARCHAR(255) NOT NULL,
    student_id VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    interests TEXT,
    why_join TEXT,
    join_date DATETIME NOT NULL,
    status ENUM('pending', 'active') DEFAULT 'pending',
    FOREIGN KEY (club_id) REFERENCES clubs(club_id) ON DELETE CASCADE
); 