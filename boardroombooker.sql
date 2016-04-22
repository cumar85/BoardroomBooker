

SET FOREIGN_KEY_CHECKS=0;
CREATE DATABASE IF NOT EXISTS `boardroombooker`;
USE `boardroombooker`;
-- ----------------------------
-- Table structure for `appointments`
-- ----------------------------



DROP TABLE IF EXISTS `appointments`;
CREATE TABLE `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `boardroom` tinyint(3) unsigned NOT NULL,
  `employee_id` int(11) unsigned NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `recurring` tinyint(3) unsigned NOT NULL,
  `recurring_id` int(11) DEFAULT NULL,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `recurring_id` (`recurring_id`),
  KEY `recurring_id_2` (`recurring_id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of appointments
-- ----------------------------

-- ----------------------------
-- Table structure for `employees`
-- ----------------------------
DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `employees` VALUES 
(1,'employee1','employee1@gmail.com'),
(2,'employee2','employee2@gmail.com'),
(3,'employee3','employee3@gmail.com'),
(4,'employee4','employee4@gmail.com')	;


