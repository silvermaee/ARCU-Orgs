-- Create clubs table
CREATE TABLE IF NOT EXISTS clubs (
    club_id INT PRIMARY KEY AUTO_INCREMENT,
    club_name VARCHAR(255) NOT NULL,
    description TEXT,
    meeting_schedule VARCHAR(255),
    location VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample clubs
INSERT INTO clubs (club_name, description, meeting_schedule, location) VALUES
('Visual Arts Club', 'A club dedicated to exploring and creating visual arts including painting, drawing, and digital art.', 'Every Monday, 3:00 PM - 5:00 PM', 'Room 101, Arts Building'),
('Performing Arts Society', 'Join us for theater, dance, and musical performances. Open to all skill levels!', 'Every Wednesday, 4:00 PM - 6:00 PM', 'Auditorium'),
('Music Ensemble', 'For musicians of all levels. We practice various genres and perform regularly.', 'Every Tuesday and Thursday, 3:30 PM - 5:30 PM', 'Music Room'),
('Dance Crew', 'Learn different dance styles and perform at campus events.', 'Every Friday, 2:00 PM - 4:00 PM', 'Dance Studio'),
('Literary Circle', 'A space for writers and poetry enthusiasts to share and discuss their work.', 'Every Thursday, 3:00 PM - 5:00 PM', 'Library Conference Room'),
('Cultural Exchange Club', 'Celebrate and learn about different cultures through various activities and events.', 'Every Wednesday, 2:00 PM - 4:00 PM', 'Student Center'); 