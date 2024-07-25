DROP TABLE IF EXISTS user_alias;

CREATE TABLE user_alias (
    id SERIAL PRIMARY KEY,
    auth_id INTEGER NOT NULL REFERENCES auth(id),
    alias VARCHAR(255) NOT NULL,
    KEY `idx_auth_id` (`auth_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;