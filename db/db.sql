-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.6.11 - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL Version:             9.1.0.4867
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping database structure for gitftp
CREATE DATABASE IF NOT EXISTS `gitftp` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `gitftp`;


-- Dumping structure for table gitftp.project
DROP TABLE IF EXISTS `project`;
CREATE TABLE IF NOT EXISTS `project` (
  `pj_id` int(11) NOT NULL AUTO_INCREMENT,
  `pj_dir` varchar(128) NOT NULL DEFAULT '0',
  `pj_ftp_server` varchar(128) NOT NULL DEFAULT '0',
  `pj_ftp_user` varchar(64) NOT NULL DEFAULT '0',
  `pj_ftp_pass` varchar(64) NOT NULL DEFAULT '0',
  `pj_ftp_dir` varchar(255) NOT NULL DEFAULT '0',
  `pj_last_push` text NOT NULL,
  PRIMARY KEY (`pj_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Dumping data for table gitftp.project: ~2 rows (approximately)
DELETE FROM `project`;
/*!40000 ALTER TABLE `project` DISABLE KEYS */;
INSERT INTO `project` (`pj_id`, `pj_dir`, `pj_ftp_server`, `pj_ftp_user`, `pj_ftp_pass`, `pj_ftp_dir`, `pj_last_push`) VALUES
	(1, 'zonareplika', '31.170.160.87', 'a3303664', 'ASgoperty0T', 'zonareplika/', 'Commit To: '),
	(2, 'bakmi-gk', 'lingkar9.com', 'lingk345', 'RkLDmtIgx1jN', '/public_html/clients/bakmi-gk2/', 'Commit To: ');
/*!40000 ALTER TABLE `project` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
