DROP TABLE IF EXISTS `reading`;

CREATE TABLE `reading` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `auth_id` int(10) NOT NULL,
  `date_created` datetime NOT NULL,
  `systolic` int(10) NOT NULL,
  `diastolic` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_auth_id` (`auth_id`),
  KEY `idx_date` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;