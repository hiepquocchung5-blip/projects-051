-- Make google_id nullable for native accounts
ALTER TABLE users MODIFY google_id VARCHAR(255) NULL;

-- Add password and provider tracking
ALTER TABLE users ADD COLUMN password_hash VARCHAR(255) NULL AFTER email;
ALTER TABLE users ADD COLUMN auth_provider ENUM('google', 'native') NOT NULL DEFAULT 'google' AFTER password_hash;