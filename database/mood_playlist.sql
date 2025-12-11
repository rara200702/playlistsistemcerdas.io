-- Mood-Based Playlist Generator Database
-- Import file ini ke phpMyAdmin untuk setup database

-- Buat database (jika belum ada)
CREATE DATABASE IF NOT EXISTS `mood_playlist` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mood_playlist`;

-- Hapus tabel jika sudah ada (untuk fresh install)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `playlist_song`;
DROP TABLE IF EXISTS `songs`;
DROP TABLE IF EXISTS `playlists`;
SET FOREIGN_KEY_CHECKS = 1;

-- Tabel playlists
CREATE TABLE `playlists` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `mood` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel songs (dengan kolom thumbnail)
CREATE TABLE `songs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `artist` varchar(255) NOT NULL,
  `spotify_link` varchar(255) DEFAULT NULL,
  `youtube_link` varchar(255) DEFAULT NULL,
  `thumbnail` varchar(500) DEFAULT NULL,
  `mood` varchar(255) NOT NULL,
  `playlist_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `songs_playlist_id_foreign` (`playlist_id`),
  CONSTRAINT `songs_playlist_id_foreign` FOREIGN KEY (`playlist_id`) REFERENCES `playlists` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel pivot playlist_song
CREATE TABLE `playlist_song` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `playlist_id` bigint(20) UNSIGNED NOT NULL,
  `song_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `playlist_song_playlist_id_foreign` (`playlist_id`),
  KEY `playlist_song_song_id_foreign` (`song_id`),
  CONSTRAINT `playlist_song_playlist_id_foreign` FOREIGN KEY (`playlist_id`) REFERENCES `playlists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `playlist_song_song_id_foreign` FOREIGN KEY (`song_id`) REFERENCES `songs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample playlists
INSERT INTO `playlists` (`name`, `description`, `mood`, `created_at`, `updated_at`) VALUES
('Energi Playlist', 'Lagu-lagu penuh energi untuk semangat', 'energi', NOW(), NOW()),
('Tenang Playlist', 'Lagu-lagu tenang untuk relaksasi', 'tenang', NOW(), NOW()),
('Galau Playlist', 'Lagu-lagu untuk suasana hati yang galau', 'galau', NOW(), NOW()),
('Bahagia Playlist', 'Lagu-lagu ceria dan bahagia', 'bahagia', NOW(), NOW()),
('Romantis Playlist', 'Lagu-lagu romantis untuk pasangan', 'romantis', NOW(), NOW()),
('Semangat Playlist', 'Lagu-lagu motivasi dan semangat', 'semangat', NOW(), NOW());

-- Insert sample songs dengan thumbnail (lebih banyak lagu)
INSERT INTO `songs` (`title`, `artist`, `mood`, `spotify_link`, `youtube_link`, `thumbnail`, `created_at`, `updated_at`) VALUES
-- Energi songs (10 lagu)
('Eye of the Tiger', 'Survivor', 'energi', 'https://open.spotify.com/track/2HHtWyy5CgaQbC7XSoOb0e', 'https://www.youtube.com/watch?v=btPJPFnesV4', 'https://img.youtube.com/vi/btPJPFnesV4/mqdefault.jpg', NOW(), NOW()),
('Stronger', 'Kanye West', 'energi', 'https://open.spotify.com/track/0j2T0R9dR9qdFgsEcd7rHm', 'https://www.youtube.com/watch?v=PsO6ZnUZI0g', 'https://img.youtube.com/vi/PsO6ZnUZI0g/mqdefault.jpg', NOW(), NOW()),
('Can\'t Hold Us', 'Macklemore & Ryan Lewis', 'energi', 'https://open.spotify.com/track/3AWb2QYWx6TkL4Yp6S8VKm', 'https://www.youtube.com/watch?v=2zNSgSzhBfM', 'https://img.youtube.com/vi/2zNSgSzhBfM/mqdefault.jpg', NOW(), NOW()),
('Titanium', 'David Guetta ft. Sia', 'energi', 'https://open.spotify.com/track/0TDLuuLlV54CkRRUOahJb4', 'https://www.youtube.com/watch?v=JRfuAukYTKg', 'https://img.youtube.com/vi/JRfuAukYTKg/mqdefault.jpg', NOW(), NOW()),
('Roar', 'Katy Perry', 'energi', 'https://open.spotify.com/track/6F5c58TMEs1byxUstkzVeM', 'https://www.youtube.com/watch?v=CevxZvSJLk8', 'https://img.youtube.com/vi/CevxZvSJLk8/mqdefault.jpg', NOW(), NOW()),
('Thunder', 'Imagine Dragons', 'energi', 'https://open.spotify.com/track/1zB4vmk8tFRmM9UULNzbLB', 'https://www.youtube.com/watch?v=fKopy74weus', 'https://img.youtube.com/vi/fKopy74weus/mqdefault.jpg', NOW(), NOW()),
('Believer', 'Imagine Dragons', 'energi', 'https://open.spotify.com/track/0pqnGHJpmpxKKif3TrnyHC', 'https://www.youtube.com/watch?v=7wtfhZwyrcc', 'https://img.youtube.com/vi/7wtfhZwyrcc/mqdefault.jpg', NOW(), NOW()),
('Uptown Funk', 'Bruno Mars', 'energi', 'https://open.spotify.com/track/32OlwWuMpZ6b0aN2RZOeMS', 'https://www.youtube.com/watch?v=OPf0YbXqDm0', 'https://img.youtube.com/vi/OPf0YbXqDm0/mqdefault.jpg', NOW(), NOW()),
('Shake It Off', 'Taylor Swift', 'energi', 'https://open.spotify.com/track/5xTtaWoae3wi06K5WfVUUH', 'https://www.youtube.com/watch?v=nfWlot6h_JM', 'https://img.youtube.com/vi/nfWlot6h_JM/mqdefault.jpg', NOW(), NOW()),
('Firework', 'Katy Perry', 'energi', 'https://open.spotify.com/track/4lCv7b86sLynZbXhfScfm2', 'https://www.youtube.com/watch?v=QGJuMBdaqIw', 'https://img.youtube.com/vi/QGJuMBdaqIw/mqdefault.jpg', NOW(), NOW()),

-- Tenang songs (10 lagu)
('Weightless', 'Marconi Union', 'tenang', 'https://open.spotify.com/track/2qijjWZ8xYqYndrg0s2Pz4', 'https://www.youtube.com/watch?v=UfcAVejslrU', 'https://img.youtube.com/vi/UfcAVejslrU/mqdefault.jpg', NOW(), NOW()),
('Strawberry Swing', 'Coldplay', 'tenang', 'https://open.spotify.com/track/2dphvmoT5jy3o4HD3kZ3ug', 'https://www.youtube.com/watch?v=8I9mz4n9u6A', 'https://img.youtube.com/vi/8I9mz4n9u6A/mqdefault.jpg', NOW(), NOW()),
('River Flows in You', 'Yiruma', 'tenang', 'https://open.spotify.com/track/7trI2IwLc2iE7yW0Myi5bz', 'https://www.youtube.com/watch?v=7maJOI3QMu0', 'https://img.youtube.com/vi/7maJOI3QMu0/mqdefault.jpg', NOW(), NOW()),
('Gymnopédie No.1', 'Erik Satie', 'tenang', 'https://open.spotify.com/track/6X0JXlUz8x4zJ4XlUz8x4z', 'https://www.youtube.com/watch?v=S-Xm7s9eGxU', 'https://img.youtube.com/vi/S-Xm7s9eGxU/mqdefault.jpg', NOW(), NOW()),
('Clair de Lune', 'Claude Debussy', 'tenang', 'https://open.spotify.com/track/1pKYYY0dkg23sTvsfbUn0N', 'https://www.youtube.com/watch?v=CvFH_6DNRCY', 'https://img.youtube.com/vi/CvFH_6DNRCY/mqdefault.jpg', NOW(), NOW()),
('Yellow', 'Coldplay', 'tenang', 'https://open.spotify.com/track/3AJwUDP919kvQ9QcozQPxg', 'https://www.youtube.com/watch?v=yKNxeF4KMsY', 'https://img.youtube.com/vi/yKNxeF4KMsY/mqdefault.jpg', NOW(), NOW()),
('Fix You', 'Coldplay', 'tenang', 'https://open.spotify.com/track/7LVHVU3tWfcxj5aiPFEW4Q', 'https://www.youtube.com/watch?v=k4V3Mo61fJM', 'https://img.youtube.com/vi/k4V3Mo61fJM/mqdefault.jpg', NOW(), NOW()),
('The Scientist', 'Coldplay', 'tenang', 'https://open.spotify.com/track/75JFxkI2RXiU7L9VXzMkle', 'https://www.youtube.com/watch?v=RB-RcX5DS5A', 'https://img.youtube.com/vi/RB-RcX5DS5A/mqdefault.jpg', NOW(), NOW()),
('Hallelujah', 'Jeff Buckley', 'tenang', 'https://open.spotify.com/track/2u5xM5s8YxY9Q5StF3qFdI', 'https://www.youtube.com/watch?v=y8AWFf7EAc4', 'https://img.youtube.com/vi/y8AWFf7EAc4/mqdefault.jpg', NOW(), NOW()),
('Mad World', 'Gary Jules', 'tenang', 'https://open.spotify.com/track/3JOVTQ5h8HGFnDdp4VT3MP', 'https://www.youtube.com/watch?v=4N3N1MlvVc4', 'https://img.youtube.com/vi/4N3N1MlvVc4/mqdefault.jpg', NOW(), NOW()),

-- Galau songs (10 lagu)
('Someone Like You', 'Adele', 'galau', 'https://open.spotify.com/track/1zwMYTA5nlNjZxYrvBB2pV', 'https://www.youtube.com/watch?v=hLQl3WQQoQ0', 'https://img.youtube.com/vi/hLQl3WQQoQ0/mqdefault.jpg', NOW(), NOW()),
('All Too Well', 'Taylor Swift', 'galau', 'https://open.spotify.com/track/3nsfB1vus2qaloUdcBZvDu', 'https://www.youtube.com/watch?v=tollGa3S0o8', 'https://img.youtube.com/vi/tollGa3S0o8/mqdefault.jpg', NOW(), NOW()),
('Say Something', 'A Great Big World', 'galau', 'https://open.spotify.com/track/6Vc5wAMmXdKIAM7WUoEb7N', 'https://www.youtube.com/watch?v=-2U0Ivkn2Ds', 'https://img.youtube.com/vi/-2U0Ivkn2Ds/mqdefault.jpg', NOW(), NOW()),
('Hurt', 'Johnny Cash', 'galau', 'https://open.spotify.com/track/6gPSUj4H5tB4s0fnLPr3qj', 'https://www.youtube.com/watch?v=vt1Pwfnh5pc', 'https://img.youtube.com/vi/vt1Pwfnh5pc/mqdefault.jpg', NOW(), NOW()),
('Hello', 'Adele', 'galau', 'https://open.spotify.com/track/4sPmO7WMQUAf45wMOtFwgs', 'https://www.youtube.com/watch?v=YQHsXMglC9A', 'https://img.youtube.com/vi/YQHsXMglC9A/mqdefault.jpg', NOW(), NOW()),
('When I Was Your Man', 'Bruno Mars', 'galau', 'https://open.spotify.com/track/0n7TUdM0hskQFi0rXxVZt6', 'https://www.youtube.com/watch?v=ekzHIouo8Q4', 'https://img.youtube.com/vi/ekzHIouo8Q4/mqdefault.jpg', NOW(), NOW()),
('Stay', 'Rihanna ft. Mikky Ekko', 'galau', 'https://open.spotify.com/track/0U2fqHREh7z7k7k1Z6n8qG', 'https://www.youtube.com/watch?v=JF8BRvqG2sE', 'https://img.youtube.com/vi/JF8BRvqG2sE/mqdefault.jpg', NOW(), NOW()),
('Skinny Love', 'Bon Iver', 'galau', 'https://open.spotify.com/track/3B3eOgLJSqPEA0RfboIQVM', 'https://www.youtube.com/watch?v=8j9zMok7hwI', 'https://img.youtube.com/vi/8j9zMok7hwI/mqdefault.jpg', NOW(), NOW()),
('The Night We Met', 'Lord Huron', 'galau', 'https://open.spotify.com/track/0QZ5yyl6B6utIWkxe8xIGY', 'https://www.youtube.com/watch?v=KtlgYxa6BMU', 'https://img.youtube.com/vi/KtlgYxa6BMU/mqdefault.jpg', NOW(), NOW()),
('Creep', 'Radiohead', 'galau', 'https://open.spotify.com/track/70LcF31zb1H0PyJoE1SCha', 'https://www.youtube.com/watch?v=XFkzRNyyGFg', 'https://img.youtube.com/vi/XFkzRNyyGFg/mqdefault.jpg', NOW(), NOW()),

-- Bahagia songs (10 lagu)
('Happy', 'Pharrell Williams', 'bahagia', 'https://open.spotify.com/track/60nZcImufyMA1MKQY3dc5e', 'https://www.youtube.com/watch?v=ZbZSe6N_BXs', 'https://img.youtube.com/vi/ZbZSe6N_BXs/mqdefault.jpg', NOW(), NOW()),
('Can\'t Stop the Feeling!', 'Justin Timberlake', 'bahagia', 'https://open.spotify.com/track/6JV2JOE0MG4S8UjWxw3mK5', 'https://www.youtube.com/watch?v=ru0K8uYEZWw', 'https://img.youtube.com/vi/ru0K8uYEZWw/mqdefault.jpg', NOW(), NOW()),
('Good Vibrations', 'The Beach Boys', 'bahagia', 'https://open.spotify.com/track/5tWKYa6Zz3qP8f7x6h5h5j', 'https://www.youtube.com/watch?v=Eab_beh07HU', 'https://img.youtube.com/vi/Eab_beh07HU/mqdefault.jpg', NOW(), NOW()),
('Walking on Sunshine', 'Katrina & The Waves', 'bahagia', 'https://open.spotify.com/track/05wIrZSwuaVWhcv5FfqeH0', 'https://www.youtube.com/watch?v=iPUmE-tne5U', 'https://img.youtube.com/vi/iPUmE-tne5U/mqdefault.jpg', NOW(), NOW()),
('Don\'t Worry Be Happy', 'Bobby McFerrin', 'bahagia', 'https://open.spotify.com/track/4hObp5bmIJ3PP3cKA9K9pM', 'https://www.youtube.com/watch?v=d-diB65scQU', 'https://img.youtube.com/vi/d-diB65scQU/mqdefault.jpg', NOW(), NOW()),
('I Gotta Feeling', 'Black Eyed Peas', 'bahagia', 'https://open.spotify.com/track/2H1047e0oMSj10dgp7p2VG', 'https://www.youtube.com/watch?v=uSD4vsh1zDA', 'https://img.youtube.com/vi/uSD4vsh1zDA/mqdefault.jpg', NOW(), NOW()),
('Best Day of My Life', 'American Authors', 'bahagia', 'https://open.spotify.com/track/5Hroj5K7vLpIG4FNCRIjbP', 'https://www.youtube.com/watch?v=Y66j_BUCBMY', 'https://img.youtube.com/vi/Y66j_BUCBMY/mqdefault.jpg', NOW(), NOW()),
('On Top of the World', 'Imagine Dragons', 'bahagia', 'https://open.spotify.com/track/213x4gs9nz8gXOg6j9iCq9', 'https://www.youtube.com/watch?v=w5tWYmTiOW0', 'https://img.youtube.com/vi/w5tWYmTiOW0/mqdefault.jpg', NOW(), NOW()),
('Good Life', 'OneRepublic', 'bahagia', 'https://open.spotify.com/track/1fLDO3v1V1xEEG0cQzRSsI', 'https://www.youtube.com/watch?v=j36n8Kn_lUY', 'https://img.youtube.com/vi/j36n8Kn_lUY/mqdefault.jpg', NOW(), NOW()),
('Celebration', 'Kool & The Gang', 'bahagia', 'https://open.spotify.com/track/3K7Q9PHUTMT1t7zyxWCXzT', 'https://www.youtube.com/watch?v=3GwjfUFyY6M', 'https://img.youtube.com/vi/3GwjfUFyY6M/mqdefault.jpg', NOW(), NOW()),

-- Romantis songs (10 lagu)
('Perfect', 'Ed Sheeran', 'romantis', 'https://open.spotify.com/track/0tgVpDi06FyKpA1z0VMD4v', 'https://www.youtube.com/watch?v=2Vv-BfVoq4g', 'https://img.youtube.com/vi/2Vv-BfVoq4g/mqdefault.jpg', NOW(), NOW()),
('All of Me', 'John Legend', 'romantis', 'https://open.spotify.com/track/3U4isOIWM3VvDubwSI3y7a', 'https://www.youtube.com/watch?v=450p7goxZqg', 'https://img.youtube.com/vi/450p7goxZqg/mqdefault.jpg', NOW(), NOW()),
('Thinking Out Loud', 'Ed Sheeran', 'romantis', 'https://open.spotify.com/track/1Slwb6dOYkBlWal1PGtnNg', 'https://www.youtube.com/watch?v=lp-EO5I60KA', 'https://img.youtube.com/vi/lp-EO5I60KA/mqdefault.jpg', NOW(), NOW()),
('At Last', 'Etta James', 'romantis', 'https://open.spotify.com/track/4Hhv2vrOTy89HFRcjU3QOx', 'https://www.youtube.com/watch?v=S-cbOl96RFM', 'https://img.youtube.com/vi/S-cbOl96RFM/mqdefault.jpg', NOW(), NOW()),
('Make You Feel My Love', 'Adele', 'romantis', 'https://open.spotify.com/track/5FgDwN7Yb1b9NOcu7i7vke', 'https://www.youtube.com/watch?v=0put0_a--QY', 'https://img.youtube.com/vi/0put0_a--QY/mqdefault.jpg', NOW(), NOW()),
('A Thousand Years', 'Christina Perri', 'romantis', 'https://open.spotify.com/track/6lanRgr6wXibZr8KgzXxBl', 'https://www.youtube.com/watch?v=rtOvBOTyX00', 'https://img.youtube.com/vi/rtOvBOTyX00/mqdefault.jpg', NOW(), NOW()),
('Just the Way You Are', 'Bruno Mars', 'romantis', 'https://open.spotify.com/track/7BqBn9nzAq8spo5e7cZ0dJ', 'https://www.youtube.com/watch?v=LjhCEhWiKXk', 'https://img.youtube.com/vi/LjhCEhWiKXk/mqdefault.jpg', NOW(), NOW()),
('Marry You', 'Bruno Mars', 'romantis', 'https://open.spotify.com/track/22PMfvdz35fFKYnJyMnLo2', 'https://www.youtube.com/watch?v=ebXbLfLACGM', 'https://img.youtube.com/vi/ebXbLfLACGM/mqdefault.jpg', NOW(), NOW()),
('I Will Always Love You', 'Whitney Houston', 'romantis', 'https://open.spotify.com/track/4eHbdreAnSOrDDsFfc4Fpm', 'https://www.youtube.com/watch?v=3JWTaaS7LdU', 'https://img.youtube.com/vi/3JWTaaS7LdU/mqdefault.jpg', NOW(), NOW()),
('Can\'t Help Falling in Love', 'Elvis Presley', 'romantis', 'https://open.spotify.com/track/44AyOl4qVkzS48vBs6NXin', 'https://www.youtube.com/watch?v=vGJTaP6anOU', 'https://img.youtube.com/vi/vGJTaP6anOU/mqdefault.jpg', NOW(), NOW()),

-- Semangat songs (10 lagu)
('Eye of the Tiger', 'Survivor', 'semangat', 'https://open.spotify.com/track/2HHtWyy5CgaQbC7XSoOb0e', 'https://www.youtube.com/watch?v=btPJPFnesV4', 'https://img.youtube.com/vi/btPJPFnesV4/mqdefault.jpg', NOW(), NOW()),
('We Are The Champions', 'Queen', 'semangat', 'https://open.spotify.com/track/7ccI9cStQbQdystvc6TvxD', 'https://www.youtube.com/watch?v=04854XqcfCY', 'https://img.youtube.com/vi/04854XqcfCY/mqdefault.jpg', NOW(), NOW()),
('Don\'t Stop Believin\'', 'Journey', 'semangat', 'https://open.spotify.com/track/4bHsxqR3GMrXTxEPLuK5ue', 'https://www.youtube.com/watch?v=1k8craCGpgs', 'https://img.youtube.com/vi/1k8craCGpgs/mqdefault.jpg', NOW(), NOW()),
('Lose Yourself', 'Eminem', 'semangat', 'https://open.spotify.com/track/5Z01UMMf7V1o0MzF86s6WJ', 'https://www.youtube.com/watch?v=_Yhyp-_hX2s', 'https://img.youtube.com/vi/_Yhyp-_hX2s/mqdefault.jpg', NOW(), NOW()),
('Hall of Fame', 'The Script ft. will.i.am', 'semangat', 'https://open.spotify.com/track/1p1F0YQfwiYzl2tqWxSsXd', 'https://www.youtube.com/watch?v=mk48x8zu7eQ', 'https://img.youtube.com/vi/mk48x8zu7eQ/mqdefault.jpg', NOW(), NOW()),
('Fight Song', 'Rachel Platten', 'semangat', 'https://open.spotify.com/track/37f4ITSlgPX81ad2EvmV4r', 'https://www.youtube.com/watch?v=xo1VInw-SKc', 'https://img.youtube.com/vi/xo1VInw-SKc/mqdefault.jpg', NOW(), NOW()),
('Stronger', 'Kelly Clarkson', 'semangat', 'https://open.spotify.com/track/0nFbK7qy2SflXv3k3Si7Ya', 'https://www.youtube.com/watch?v=Xn676-fLq7I', 'https://img.youtube.com/vi/Xn676-fLq7I/mqdefault.jpg', NOW(), NOW()),
('I Will Survive', 'Gloria Gaynor', 'semangat', 'https://open.spotify.com/track/7af28QgzuoCOYqGlRKIewq', 'https://www.youtube.com/watch?v=ARt9HV9vV6E', 'https://img.youtube.com/vi/ARt9HV9vV6E/mqdefault.jpg', NOW(), NOW()),
('Survivor', 'Destiny\'s Child', 'semangat', 'https://open.spotify.com/track/2Mpj1Ul5OFPyyP4wB62Rvi', 'https://www.youtube.com/watch?v=Wmc8bQoL-J0', 'https://img.youtube.com/vi/Wmc8bQoL-J0/mqdefault.jpg', NOW(), NOW()),
('Titanium', 'David Guetta ft. Sia', 'semangat', 'https://open.spotify.com/track/0TDLuuLlV54CkRRUOahJb4', 'https://www.youtube.com/watch?v=JRfuAukYTKg', 'https://img.youtube.com/vi/JRfuAukYTKg/mqdefault.jpg', NOW(), NOW());

-- Link songs to playlists berdasarkan mood
INSERT INTO `playlist_song` (`playlist_id`, `song_id`, `created_at`, `updated_at`)
SELECT p.id, s.id, NOW(), NOW()
FROM `playlists` p
INNER JOIN `songs` s ON p.mood = s.mood;
