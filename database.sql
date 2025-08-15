-- Lightweight DB for CI3 Peta Kelurahan (2 samples)

SET NAMES utf8mb4;

SET FOREIGN_KEY_CHECKS = 0;


CREATE TABLE IF NOT EXISTS `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','viewer') NOT NULL DEFAULT 'viewer',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- seed users (admin/admin123, viewer/view123)

INSERT INTO `users` (`username`,`password_hash`,`role`) VALUES
('admin','$2y$10$R8l8zNw8bG.9i8I5w0G4GONqB7tK3m0hL9cQda5mQ0cQpJ6o3oZ1i','admin'),
('viewer','$2y$10$VJf0nB7Q0mXn1dJ9s0zTxeKXwB1lUo6wD8n1bGv4Pl7S0s8Jp8oO2','viewer')
ON DUPLICATE KEY UPDATE username=VALUES(username);


CREATE TABLE IF NOT EXISTS `geojson_data` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `kelurahan` VARCHAR(255) NULL,
  `kategori` VARCHAR(100) NULL,
  `name` VARCHAR(255) NULL,
  `color` VARCHAR(20) NOT NULL DEFAULT '#3388ff',
  `properties` JSON NULL,
  `geometry` JSON NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `geojson_data` (`kelurahan`,`kategori`,`name`,`color`,`properties`,`geometry`) VALUES ('Lubuk Semut',NULL,'Lubuk Semut','#3388ff',NULL,'{"type":"FeatureCollection","features":[{"type":"Feature","properties":{"name":"Lubuk Semut","kelurahan":"Lubuk Semut"},"geometry":{"type":"Polygon","coordinates":[[[103.45,0.65],[103.46,0.65],[103.46,0.66],[103.45,0.66],[103.45,0.65]]]}}]}');

INSERT INTO `geojson_data` (`kelurahan`,`kategori`,`name`,`color`,`properties`,`geometry`) VALUES ('Tanjung Batu Kota',NULL,'Tanjung Batu Kota','#3388ff',NULL,'{"type":"FeatureCollection","features":[{"type":"Feature","properties":{"name":"Tanjung Batu Kota","kelurahan":"Tanjung Batu Kota"},"geometry":{"type":"Polygon","coordinates":[[[103.468,0.67],[103.472,0.67],[103.472,0.674],[103.468,0.674],[103.468,0.67]]]}}]}');

SET FOREIGN_KEY_CHECKS = 1;