CREATE TABLE IF NOT EXISTS bookmarks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    url VARCHAR(2048) NOT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS bookmark_tags (
    bookmark_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (bookmark_id, tag_id),
    FOREIGN KEY (bookmark_id) REFERENCES bookmarks(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- Sample data to verify your setup works (safe to run multiple times)
INSERT IGNORE INTO bookmarks (id, title, url, notes) VALUES
    (1, 'PHP Manual', 'https://www.php.net/manual/en/', 'Official PHP documentation'),
    (2, 'MDN Web Docs', 'https://developer.mozilla.org/', 'HTML, CSS, and web platform reference'),
    (3, 'Stack Overflow', 'https://stackoverflow.com/', NULL);

INSERT IGNORE INTO tags (id, name) VALUES (1, 'php'), (2, 'reference'), (3, 'community');

INSERT IGNORE INTO bookmark_tags (bookmark_id, tag_id) VALUES
    (1, 1),
    (1, 2),
    (2, 2),
    (3, 3);
