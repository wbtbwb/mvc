CREATE TABLE IF NOT EXISTS user(
    id INT AUTO_INCREMENT,
    username VARCHAR(16) NOT NULL,
    password_hash VARCHAR(32) NOT NULL,
    salt VARCHAR(8) NOT NULL,
    role VARCHAR(16),
    PRIMARY KEY(id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Defoult table for User entity';

--delimiter

INSERT INTO user(id, username, password_hash, salt, role) VALUES(
    NULL,
    'admin',
    '3eee33d3cadd7a6ba59414f09dd48362',
    'saltsalt',
    'admin'
);
