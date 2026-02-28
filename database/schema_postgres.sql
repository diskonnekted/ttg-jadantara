-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    role TEXT DEFAULT 'admin'
);

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    icon_class TEXT
);

-- Tutorials Table
CREATE TABLE IF NOT EXISTS tutorials (
    id SERIAL PRIMARY KEY,
    title TEXT NOT NULL,
    slug TEXT UNIQUE NOT NULL,
    description TEXT,
    difficulty TEXT,
    duration INTEGER,
    category_id INTEGER REFERENCES categories(id),
    author_id INTEGER REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    view_count INTEGER DEFAULT 0,
    image_path TEXT,
    video_url TEXT,
    pdf_path TEXT
);

-- Steps Table
CREATE TABLE IF NOT EXISTS steps (
    id SERIAL PRIMARY KEY,
    tutorial_id INTEGER NOT NULL REFERENCES tutorials(id) ON DELETE CASCADE,
    step_number INTEGER NOT NULL,
    title TEXT,
    content TEXT,
    image_path TEXT
);

-- Materials Table
CREATE TABLE IF NOT EXISTS materials (
    id SERIAL PRIMARY KEY,
    tutorial_id INTEGER NOT NULL REFERENCES tutorials(id) ON DELETE CASCADE,
    name TEXT NOT NULL,
    quantity TEXT,
    local_source TEXT
);
