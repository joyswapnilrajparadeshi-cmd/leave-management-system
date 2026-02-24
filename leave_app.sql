-- 1. Create Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    total_leaves INT DEFAULT 30,
    used_leaves INT DEFAULT 0,
    otp VARCHAR(10),
    otp_expiry DATETIME,
    reset_token VARCHAR(10),
    token_expiry DATETIME,
    department VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

-- 2. Create Leave Types table
CREATE TABLE IF NOT EXISTS leave_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(60) NOT NULL UNIQUE,
    code VARCHAR(20) NOT NULL UNIQUE,
    annual_quota INT NOT NULL,
    requires_proof TINYINT(1) DEFAULT 0,
    description VARCHAR(255) DEFAULT NULL,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Seed initial leave types
INSERT IGNORE INTO leave_types (name, code, annual_quota, requires_proof, description) VALUES
('Casual Leave', 'CL', 12, 0, 'Personal reasons / short breaks'),
('Sick Leave', 'SL', 10, 1, 'Illness; medical proof may be required'),
('Earned Leave', 'EL', 18, 0, 'Planned vacations'),
('Maternity Leave', 'ML', 180, 1, 'As per organization policy'),
('Paternity Leave', 'PL', 15, 1, 'As per organization policy'),
('Academic Leave', 'AL', 10, 1, 'Academic related events, workshops, conferences'),
('On Duty', 'OD', 20, 0, 'Official duty/representing college');

-- 4. Create Leave Requests table
CREATE TABLE IF NOT EXISTS leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    leave_type_id INT NULL,
    reason TEXT,
    from_date DATE,
    to_date DATE,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    proof_file VARCHAR(255),
    rejection_reason TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 5. Optional per-user overrides (if some users get different quotas)
CREATE TABLE IF NOT EXISTS user_leave_overrides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    leave_type_id INT NOT NULL,
    annual_quota INT NOT NULL,
    UNIQUE KEY uniq_user_type (user_id, leave_type_id),
    CONSTRAINT fk_ulo_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_ulo_type FOREIGN KEY (leave_type_id) REFERENCES leave_types(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. (InfinityFree does NOT allow event scheduler, so skipping this section)

-- 7. Set leave_type_id to CL for existing leave_requests without type
UPDATE leave_requests 
SET leave_type_id = (SELECT id FROM leave_types WHERE code='CL' LIMIT 1)
WHERE leave_type_id IS NULL;
