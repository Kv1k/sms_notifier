CREATE TABLE IF NOT EXISTS  messenger_messages (
    id SERIAL PRIMARY KEY,
    body TEXT NOT NULL,
    headers TEXT NOT NULL,
    queue_name VARCHAR(255) NOT NULL,
    status VARCHAR(32) DEFAULT 'queued' NOT NULL,
    available_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    attempted INT DEFAULT 0 NOT NULL,
    failures INT DEFAULT 0 NOT NULL
);
