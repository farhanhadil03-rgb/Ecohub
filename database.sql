CREATE DATABASE IF NOT EXISTS ecohub CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE ecohub;

-- =========================
-- USERS
-- =========================
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin','User','Guest') NOT NULL DEFAULT 'User'
) ENGINE=InnoDB;

INSERT INTO users (full_name, username, email, password, role)
VALUES (
    'Admin Utama',
    'admin',
    'admin@gmail.com',
    '$2y$10$NFzTW/HM7O0bxb6bSLRx8eMUrUgWkmBx6ZtgxYAEUZF31qhrebgxu',
    'Admin'
);

-- =========================
-- PROFILES ✅ DIPERBAIKI
-- =========================
CREATE TABLE profiles (
    profile_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE,
    display_name VARCHAR(120),
    bio TEXT,
    points INT DEFAULT 0,
    recycled_count INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

INSERT INTO profiles (user_id, display_name, bio, points, recycled_count) VALUES
(1, 'Andi P.', 'Penggiat daur ulang plastik', 120, 32);

-- =========================
-- LISTINGS (BARTER) ✅
-- =========================
CREATE TABLE listings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  title VARCHAR(200),
  description TEXT,
  item_condition ENUM('new','good','used') DEFAULT 'used',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

INSERT INTO listings (user_id, title, description, item_condition) VALUES
(1, 'Botol Kaca Bekas (20 pcs)', 'Botol kaca bersih, cocok untuk DIY', 'good');

-- =========================
-- CHALLENGES
-- =========================
CREATE TABLE challenges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200),
    description TEXT,
    points_reward INT DEFAULT 10,
    start_date DATE,
    end_date DATE
);

INSERT INTO challenges (title, description, points_reward, start_date, end_date) VALUES
('Bawa Tumbler Sepanjang Minggu', 'Bawa tumbler setiap hari selama seminggu', 50, '2025-11-01', '2025-11-07');

-- =========================
-- CHALLENGE PARTICIPANTS
-- =========================
CREATE TABLE challenge_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    challenge_id INT,
    user_id INT,
    status ENUM('joined','completed') DEFAULT 'joined',
    proof TEXT,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (challenge_id) REFERENCES challenges(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- =========================
-- ARTICLES
-- =========================
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    content TEXT,
    author VARCHAR(120),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO articles (title, content, author) VALUES
('Mengapa Penting Menyetorkan Barang Bekas Secara Berkala?',
 'Banyak pengguna EcoHub menunda penyetoran barang bekas padahal sudah terdaftar. Artikel ini menjelaskan dampaknya terhadap lingkungan serta bagaimana fitur notifikasi membantu pengguna tetap konsisten dalam menerapkan gaya hidup ramah lingkungan.',
 'Tim EcoHub'),

('Eco-Challenge: Cara Sederhana Mengurangi Sampah Plastik',
 'Eco-challenge mingguan EcoHub dirancang untuk memotivasi pengguna melakukan aksi kecil namun berdampak besar, seperti membawa tas belanja sendiri. Dengan dukungan notifikasi, pengguna tidak mudah lupa dan lebih disiplin menjalankan tantangan.',
 'Tim Sustainability'),

('Mengenal Manfaat Pemilahan Sampah dari Rumah',
 'Pemilahan sampah adalah langkah awal menuju ekosistem daur ulang yang lebih efektif. Artikel ini memberikan panduan sederhana serta bagaimana EcoHub memberikan pengingat agar kebiasaan ini tetap berlanjut.',
 'Green Educator'),

('Tips Membaca Artikel Edukasi Lingkungan Secara Konsisten',
 'EcoHub menyediakan artikel edukasi terbaru setiap minggu. Dengan bantuan fitur notifikasi, pengguna dapat terus mengikuti update ilmu dan praktik hijau tanpa perlu mengeceknya secara manual.',
 'EcoWriter Team'),

('Apa yang Terjadi Jika Kita Tidak Menjaga Konsistensi Aksi Ramah Lingkungan?',
 'Kurangnya konsistensi menyebabkan upaya lingkungan berjalan lambat. EcoHub menghadirkan pengingat untuk memastikan setiap pengguna tetap aktif, baik dalam menyetorkan barang bekas, menyelesaikan eco-challenge, maupun membaca artikel edukatif.',
 'Earth Awareness Group');

-- =========================
-- NOTIFICATIONS ✅
-- =========================
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    message TEXT,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- =========================
-- POINTS LOG ✅
-- =========================
CREATE TABLE points_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    change_amount INT,
    reason VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- =========================
-- RECYCLING LOCATIONS ✅ (MAP)
-- =========================
CREATE TABLE recycling_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    latitude DECIMAL(10,7) NOT NULL,
    longitude DECIMAL(10,7) NOT NULL
);

INSERT INTO recycling_locations (name, address, latitude, longitude) VALUES
('DropBox Plastik Jakarta', 'Jl. Merdeka No.10, Jakarta', -6.200000, 106.816666),
('Bank Sampah Bandung', 'Jl. Melati No.12, Bandung', -6.914744, 107.609810),
('Recycle Center Surabaya', 'Jl. Pemuda No.20, Surabaya', -7.257472, 112.752090);
-- =========================
-- ECO CHALLENGES ✅ (untuk challenges.php kamu)
-- =========================
CREATE TABLE eco_challenges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    points INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO eco_challenges (title, description, points) VALUES
('Kurangi Plastik Sekali Pakai', 'Bawa tumbler sendiri.', 10),
('Hemat Listrik', 'Matikan perangkat elektronik.', 5);
CREATE TABLE tutorials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    video_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO tutorials (title, description, image)
VALUES
('Cara Daur Ulang Botol Plastik', 'Langkah mudah mendaur ulang botol plastik di rumah.', 'plastik.jpg'),
('Recycling Kertas', 'Tutorial pengolahan kertas bekas menjadi kerajinan.', 'kertas.jpg');

CREATE TABLE barter_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);
INSERT INTO barter_items (user_id, item_name, description, image)
VALUES
(1, 'Botol Kaca Bekas', 'Botol kaca cocok untuk kerajinan.', 'kaca.jpg');



CREATE TABLE challenge_completions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    challenge_id INT NOT NULL,
    completed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (user_id, challenge_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (challenge_id) REFERENCES eco_challenges(id) ON DELETE CASCADE
);

CREATE TABLE user_challenges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    challenge_id INT NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (challenge_id) REFERENCES eco_challenges(id)
);

CREATE TABLE levels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    min_points INT
);

INSERT INTO levels (name, min_points) VALUES
('Eco Starter', 0),
('Eco Warrior', 100),
('Eco Hero', 250),
('Eco Guardian', 500);

CREATE TABLE rewards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    description TEXT,
    points_required INT,
    stock INT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO rewards (name, description, points_required, stock) VALUES
('Tumbler Eco', 'Tumbler stainless ramah lingkungan', 300, 20),
('Sedotan Stainless', 'Sedotan reusable stainless steel', 150, 50),
('Totebag Eco', 'Tas kain ramah lingkungan', 250, 30);

CREATE TABLE reward_claims (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    reward_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (reward_id) REFERENCES rewards(id) ON DELETE CASCADE
);

ALTER TABLE users
ADD COLUMN level_id INT DEFAULT 1,
ADD FOREIGN KEY (level_id) REFERENCES levels(id);

ALTER TABLE users
ADD COLUMN points INT DEFAULT 0;

ALTER TABLE users CHANGE password password_hash VARCHAR(255);

ALTER TABLE notifications 
ADD COLUMN from_user_id INT AFTER user_id,
ADD COLUMN item_id INT AFTER from_user_id;

ALTER TABLE notifications
ADD FOREIGN KEY (from_user_id) REFERENCES users(user_id) ON DELETE CASCADE;

ALTER TABLE notifications
ADD FOREIGN KEY (item_id) REFERENCES barter_items(id) ON DELETE CASCADE;

ALTER TABLE notifications
ADD COLUMN type ENUM('request', 'response') DEFAULT 'request';

ALTER TABLE notifications 
ADD COLUMN status ENUM('pending','accepted','rejected') DEFAULT 'pending';

ALTER TABLE reward_claims
DROP COLUMN status;
