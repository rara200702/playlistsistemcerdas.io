-- Update Database: Tambah kolom thumbnail dan update lagu yang sudah ada
-- Jalankan file ini jika database sudah ada dan ingin menambah kolom thumbnail

USE `mood_playlist`;

-- Tambah kolom thumbnail jika belum ada
ALTER TABLE `songs` 
ADD COLUMN IF NOT EXISTS `thumbnail` varchar(500) DEFAULT NULL AFTER `youtube_link`;

-- Update thumbnail untuk lagu yang sudah ada berdasarkan YouTube link
UPDATE `songs` 
SET `thumbnail` = CONCAT('https://img.youtube.com/vi/', 
    SUBSTRING_INDEX(SUBSTRING_INDEX(`youtube_link`, 'v=', -1), '&', 1), 
    '/mqdefault.jpg')
WHERE `youtube_link` IS NOT NULL 
AND `youtube_link` LIKE '%youtube.com%' 
AND (`thumbnail` IS NULL OR `thumbnail` = '');

-- Update thumbnail untuk lagu dengan format youtu.be
UPDATE `songs` 
SET `thumbnail` = CONCAT('https://img.youtube.com/vi/', 
    SUBSTRING_INDEX(`youtube_link`, '/', -1), 
    '/mqdefault.jpg')
WHERE `youtube_link` IS NOT NULL 
AND `youtube_link` LIKE '%youtu.be%' 
AND (`thumbnail` IS NULL OR `thumbnail` = '');

