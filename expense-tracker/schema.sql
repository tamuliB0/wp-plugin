CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- You will design and create additional tables (expenses, categories,
-- etc.) as you work through the milestones. Add your CREATE TABLE
-- statements to this file so your schema stays in one place.
