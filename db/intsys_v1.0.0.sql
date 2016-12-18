-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 06, 2014 at 03:10 AM
-- Server version: 5.5.27
-- PHP Version: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `lcheadend`
--
CREATE SCHEMA lcheadend;
USE lcheadend;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_role`
--

CREATE TABLE IF NOT EXISTS `tbl_role` (
  `id_role` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `desc` varchar(128) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id_role`)
) ENGINE=InnoDB  COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `tbl_role`
--

INSERT INTO `tbl_role` (`id_role`, `name`, `desc`) VALUES
(1, 'admin', 'Has full privileges over all application'),
(2, 'super-user', 'Has additional privileges over the application'),
(3, 'normal-user', 'Has normal privileges over the application');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE IF NOT EXISTS `tbl_user` (
  `id_user` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(128) COLLATE latin1_general_ci NOT NULL,
  `password` varchar(128) COLLATE latin1_general_ci NOT NULL,
  `fk_role` int(11) unsigned NOT NULL DEFAULT '3',
  `email` varchar(128) COLLATE latin1_general_ci NOT NULL,
  `first_name` varchar(128) COLLATE latin1_general_ci NOT NULL,
  `last_name` varchar(128) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`),
  FOREIGN KEY (`fk_role`) references tbl_role(id_role)
) ENGINE=InnoDB  COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id_user`, `username`, `password`, `fk_role`, `email`, `first_name`, `last_name`) VALUES
(1, 'admin', '$2a$13$5S7I6zrlXa/P1oJgioX09.cHAOGQza.B3k76oQ8It4CHpyFyacKGK', 1, 'a@a.pt', 'a', 'a');

--
-- Table structure for table `tbl_configuration`
--

CREATE TABLE IF NOT EXISTS `tbl_configuration` (
  `id_configuration` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(128) COLLATE latin1_general_ci NOT NULL UNIQUE,
  `type` varchar(128) COLLATE latin1_general_ci NOT NULL,
  `label` varchar(128) COLLATE latin1_general_ci NOT NULL UNIQUE,
  `value` varchar(128) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id_configuration`)
) ENGINE=InnoDB  COLLATE=latin1_general_ci;

--
-- Populate Table `tbl_configuration` with initial values
--
INSERT INTO `tbl_configuration` (`key`, `type`,`label`, `value`) VALUES
('header_message','textField','Header Right Corner Message',''),
('client_name','textField','Client Name',''),
('building_name','textField','Building Name',''),
('client_logo','fileField','Client Logo','');


--
-- Table structure for table `tbl_area`
--

CREATE TABLE IF NOT EXISTS `tbl_area` (
  `id_area` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE latin1_general_ci NOT NULL UNIQUE,
  `desc` varchar(128) COLLATE latin1_general_ci NOT NULL,
  `plan` varchar(128) COLLATE latin1_general_ci NOT NULL,
  `tree_config` TEXT NULL,
  `props_config` TEXT NULL,  
  PRIMARY KEY (`id_area`)
) ENGINE=InnoDB  COLLATE=latin1_general_ci;


--
-- Table structure for table `tbl_ethernet`
--

CREATE TABLE IF NOT EXISTS `tbl_ethernet` (
  `id_ethernet` int(11) unsigned NOT NULL UNIQUE,
  `name` varchar(20) COLLATE latin1_general_ci NOT NULL UNIQUE,
  `desc` varchar(128) COLLATE latin1_general_ci NOT NULL,
  `host` varchar(32) COLLATE latin1_general_ci NOT NULL UNIQUE,
  `config` MEDIUMTEXT NULL,
  `fk_area` int(11) unsigned NOT NULL,
  `lock` bool NOT NULL default FALSE,
  `locked_at` timestamp NULL,
  `locked_by` int(11) unsigned NULL,
  PRIMARY KEY (`id_ethernet`),
  FOREIGN KEY (`fk_area`) references tbl_area(id_area) ON DELETE CASCADE ,
  FOREIGN KEY (`locked_by`) references tbl_user(id_user) ON DELETE CASCADE 
) ENGINE=InnoDB  COLLATE=latin1_general_ci;


--
-- Table structure for table `tbl_command_queue`
--

CREATE TABLE IF NOT EXISTS `tbl_command_queue` (
  `id_command` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ethernet_id` int(11) unsigned COLLATE latin1_general_ci NOT NULL,
  `cmd_name` varchar(64) COLLATE latin1_general_ci NOT NULL,
  `cmd` varchar(256) COLLATE latin1_general_ci NOT NULL,
  `hash` varchar(32) COLLATE latin1_general_ci NULL,
  `status` enum('pending','processing','finished','error') COLLATE latin1_general_ci NOT NULL,
  `fk_user` int(11) unsigned NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_command`),
  FOREIGN KEY (`ethernet_id`) references tbl_ethernet(id_ethernet) ON DELETE CASCADE,
  FOREIGN KEY (`fk_user`) references tbl_user(id_user) ON DELETE SET NULL
) ENGINE=InnoDB  COLLATE=latin1_general_ci;


--
-- Table structure for table `tbl_command_schedule`
--

CREATE TABLE IF NOT EXISTS `tbl_command_schedule` (
  `id_schedule` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(256) COLLATE latin1_general_ci NOT NULL,
  `periodicity` enum('unique','weekly','monthly') COLLATE latin1_general_ci NOT NULL,
  `fk_area` int(11) unsigned NOT NULL,
  `fk_ethernet` int(11) unsigned NOT NULL,
  `lc_id` int(11) DEFAULT NULL,
  `dvc_id` int(11) DEFAULT NULL,
  `group` int(1) DEFAULT NULL,
  `type` enum('normal','function','duration') COLLATE latin1_general_ci NOT NULL,
  `cci_data` enum('on','off','momentary') COLLATE latin1_general_ci DEFAULT NULL,
  `cmd` varchar(256) COLLATE latin1_general_ci NOT NULL,
  `cmd_name` varchar(64) COLLATE latin1_general_ci NOT NULL,
  `start_date` datetime NOT NULL,
  `event_time` datetime NOT NULL,
  `month_repeat` enum('monthly','bimonthly','quarterly','biannualy') COLLATE latin1_general_ci DEFAULT NULL,
  `monday` bit(1) COLLATE latin1_general_ci NULL,
  `tuesday` bit(1) COLLATE latin1_general_ci NULL,
  `wednesday` bit(1) COLLATE latin1_general_ci NULL,
  `thursday` bit(1) COLLATE latin1_general_ci NULL,
  `friday` bit(1) COLLATE latin1_general_ci NULL,
  `saturday` bit(1) COLLATE latin1_general_ci NULL,
  `sunday` bit(1) COLLATE latin1_general_ci NULL,
  `fk_user` int(11) unsigned NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_schedule`),
  FOREIGN KEY (`fk_area`) references tbl_area(id_area) ON DELETE CASCADE, 
  FOREIGN KEY (`fk_ethernet`) references tbl_ethernet(id_ethernet) ON DELETE CASCADE,
  FOREIGN KEY (`fk_user`) references tbl_user(id_user) ON DELETE SET NULL 
) ENGINE=InnoDB  COLLATE=latin1_general_ci;


--
-- Table structure for table `tbl_executed_schedule`
--

CREATE TABLE IF NOT EXISTS `tbl_executed_schedule` (
  `id_exec_schedule` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(256) COLLATE latin1_general_ci NOT NULL,
  `periodicity` enum('unique','weekly','monthly') COLLATE latin1_general_ci NOT NULL,
  `fk_area` int(11) unsigned NOT NULL,
  `fk_ethernet` int(11) unsigned NOT NULL,
  `lc_id` int(11) DEFAULT NULL,
  `dvc_id` int(11) DEFAULT NULL,
  `group` int(1) DEFAULT NULL,
  `type` enum('normal','functional','duration') COLLATE latin1_general_ci NOT NULL,
  `cci_data` enum('on','off','momentary') COLLATE latin1_general_ci DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `fk_command` int(11) unsigned NULL,
  `fk_user` int(11) unsigned NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_exec_schedule`),
  FOREIGN KEY (`fk_area`) references tbl_area(id_area) ON DELETE CASCADE, 
  FOREIGN KEY (`fk_ethernet`) references tbl_ethernet(id_ethernet) ON DELETE CASCADE,
  FOREIGN KEY (`fk_command`) references tbl_command_queue(id_command) ON DELETE SET NULL,
  FOREIGN KEY (`fk_user`) references tbl_user(id_user) ON DELETE SET NULL 
) ENGINE=InnoDB  COLLATE=latin1_general_ci;


--
-- Table structure for table `tbl_description`
--

CREATE TABLE IF NOT EXISTS `tbl_description` (
  `id_description` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(32) NOT NULL,
  PRIMARY KEY (`id_description`)
) ENGINE=InnoDB  COLLATE=latin1_general_ci;


--
-- Table structure for table `tbl_status`
--

CREATE TABLE IF NOT EXISTS `tbl_status` (
  `fk_ethernet` int(11) unsigned NOT NULL,
  `lc_id` int(11) unsigned NOT NULL,
  `dvc_id` int(11) unsigned NOT NULL,
  `type` varchar(12) NOT NULL,
  `fk_description` int(5) unsigned DEFAULT NULL,
  `status_hex` char(8) COLLATE latin1_general_ci NOT NULL,
  `lamp_status` enum('on','off','failure') COLLATE latin1_general_ci NULL,
  `lux_level` tinyint unsigned COLLATE latin1_general_ci NULL,
  `emergency_mode` enum('reset mode','normal operation','emergency mode','extended emergency mode','function test in progress','duration test in progress') COLLATE latin1_general_ci  NULL,
  `battery_status` enum('charged','charging') COLLATE latin1_general_ci  NULL,
  `input_status` enum('not detecting','detecting') COLLATE latin1_general_ci NULL,
  `current_scene` tinyint COLLATE latin1_general_ci NULL,
  `alternate_scene` bit(1) COLLATE latin1_general_ci NULL,
  `switch_input_1` bit(1) COLLATE latin1_general_ci NULL,
  `switch_input_2` bit(1) COLLATE latin1_general_ci NULL,
  `switch_input_3` bit(1) COLLATE latin1_general_ci NULL,
  `switch_input_4` bit(1) COLLATE latin1_general_ci NULL,
  `switch_input_5` bit(1) COLLATE latin1_general_ci NULL,
  `switch_input_6` bit(1) COLLATE latin1_general_ci NULL,
  `switch_input_7` bit(1) COLLATE latin1_general_ci NULL,
  `switch_input_8` bit(1) COLLATE latin1_general_ci NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`fk_ethernet`, `lc_id`, `dvc_id`),
  FOREIGN KEY (`fk_ethernet`) references tbl_ethernet(`id_ethernet`) ON DELETE CASCADE, 
  FOREIGN KEY (`fk_description`) references tbl_description(`id_description`) ON DELETE CASCADE
) ENGINE=InnoDB  COLLATE=latin1_general_ci;

--
-- Table structure for table `tbl_status_hist`
--

CREATE TABLE IF NOT EXISTS `tbl_status_hist` (
  `id_status_hist` int(11) NOT NULL AUTO_INCREMENT,
  `fk_ethernet` int(11) unsigned NOT NULL,
  `lc_id` int(11) unsigned NOT NULL,
  `dvc_id` int(11) unsigned NOT NULL,
  `type` varchar(12) NOT NULL,
  `fk_description` int(5) unsigned DEFAULT NULL,
  `status_hex` char(8) COLLATE latin1_general_ci NOT NULL,
  `lamp_status` enum('on','off','failure') COLLATE latin1_general_ci NULL,
  `new_lamp_failed`  bit(1) NOT NULL DEFAULT 0,
  `lux_level` tinyint unsigned COLLATE latin1_general_ci NULL,
  `emergency_mode` enum('reset mode','normal operation','emergency mode','extended emergency mode','function test in progress','duration test in progress') COLLATE latin1_general_ci  NULL,
  `battery_status` enum('charged','charging') COLLATE latin1_general_ci  NULL,
  `input_status` enum('not detecting','detecting') COLLATE latin1_general_ci NULL,
  `current_scene` tinyint COLLATE latin1_general_ci NULL,
  `alternate_scene` bit(1) COLLATE latin1_general_ci NULL,
  `switch_input_1` bit(1) COLLATE latin1_general_ci NULL,
  `switch_input_2` bit(1) COLLATE latin1_general_ci NULL,
  `switch_input_3` bit(1) COLLATE latin1_general_ci NULL,
  `switch_input_4` bit(1) COLLATE latin1_general_ci NULL,
  `switch_input_5` bit(1) COLLATE latin1_general_ci NULL,
  `switch_input_6` bit(1) COLLATE latin1_general_ci NULL,
  `switch_input_7` bit(1) COLLATE latin1_general_ci NULL,
  `switch_input_8` bit(1) COLLATE latin1_general_ci NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_status_hist`),
  FOREIGN KEY (`fk_ethernet`) references tbl_ethernet(`id_ethernet`) ON DELETE CASCADE,
  FOREIGN KEY (`fk_description`) references tbl_description(`id_description`) ON DELETE CASCADE
) ENGINE=InnoDB  COLLATE=latin1_general_ci;

--
-- Indexes for table `tbl_status_hist`
--

CREATE INDEX `ethernet_lc_dvc` ON `tbl_status_hist`(`fk_ethernet`, `lc_id`, `dvc_id`);
CREATE INDEX `created_at` ON `tbl_status_hist`(`created_at`);


--
-- Triggers for table `tbl_status_hist`
--

DELIMITER $$
CREATE TRIGGER after_status_insert 
    AFTER INSERT ON tbl_status
    FOR EACH ROW BEGIN
 
	IF NEW.`type` <> 132 THEN
		INSERT INTO tbl_status_hist
		SET 
		`fk_ethernet` = NEW.`fk_ethernet`,
		`lc_id` = NEW.`lc_id`,
		`dvc_id` = NEW.`dvc_id`,
		`type` = NEW.`type`,
		`fk_description` = NEW.`fk_description`,
		`status_hex` = NEW.`status_hex`,
		`lamp_status` = NEW.`lamp_status`,
		`lux_level` = NEW.`lux_level`,
		`emergency_mode` = NEW.`emergency_mode`,
		`battery_status` = NEW.`battery_status`,
		`input_status` = NEW.`input_status`,
		`current_scene` = NEW.`current_scene`,
		`alternate_scene` = NEW.`alternate_scene`,
		`switch_input_1` = NEW.`switch_input_1`,
		`switch_input_2` = NEW.`switch_input_2`,
		`switch_input_3` = NEW.`switch_input_3`,
		`switch_input_4` = NEW.`switch_input_4`,
		`switch_input_5` = NEW.`switch_input_5`,
		`switch_input_6` = NEW.`switch_input_6`,
		`switch_input_7` = NEW.`switch_input_7`,
		`switch_input_8` = NEW.`switch_input_8`,
		`created_at` = now();
    END IF;
	
	
END$$

CREATE TRIGGER after_status_update 
    AFTER UPDATE ON tbl_status
    FOR EACH ROW BEGIN
 
	IF NEW.`type` <> 132 THEN
		INSERT INTO tbl_status_hist
		SET 
		`fk_ethernet` = NEW.`fk_ethernet`,
		`lc_id` = NEW.`lc_id`,
		`dvc_id` = NEW.`dvc_id`,
		`type` = NEW.`type`,
		`fk_description` = NEW.`fk_description`,
		`status_hex` = NEW.`status_hex`,
		`lamp_status` = NEW.`lamp_status`,
		`lux_level` = NEW.`lux_level`,
		`emergency_mode` = NEW.`emergency_mode`,
		`battery_status` = NEW.`battery_status`,
		`input_status` = NEW.`input_status`,
		`current_scene` = NEW.`current_scene`,
		`alternate_scene` = NEW.`alternate_scene`,
		`switch_input_1` = NEW.`switch_input_1`,
		`switch_input_2` = NEW.`switch_input_2`,
		`switch_input_3` = NEW.`switch_input_3`,
		`switch_input_4` = NEW.`switch_input_4`,
		`switch_input_5` = NEW.`switch_input_5`,
		`switch_input_6` = NEW.`switch_input_6`,
		`switch_input_7` = NEW.`switch_input_7`,
		`switch_input_8` = NEW.`switch_input_8`,
		`created_at` = now();
    END IF;
	
	
END$$

DELIMITER ;

--
-- Table structure for table `tbl_emergency`
--

CREATE TABLE IF NOT EXISTS `tbl_emergency` (
  `fk_ethernet` int(11) unsigned NOT NULL,
  `lc_id` int(11) unsigned NOT NULL,
  `dvc_id` int(11) unsigned NOT NULL,
  `fk_description` int(5) unsigned DEFAULT NULL,
  `test_type` enum('function','duration') COLLATE latin1_general_ci NOT NULL,
  `circuit_failure` bit(1) COLLATE latin1_general_ci NOT NULL,
  `battery_duration_failed` bit(1) COLLATE latin1_general_ci NOT NULL,
  `battery_failed` bit(1) COLLATE latin1_general_ci NOT NULL,
  `emergency_lamp_failed` bit(1) COLLATE latin1_general_ci NOT NULL,
  `function_test_overdue` bit(1) COLLATE latin1_general_ci NOT NULL,
  `duration_test_overdue` bit(1) COLLATE latin1_general_ci NOT NULL,
  `function_test_failed` bit(1) COLLATE latin1_general_ci NOT NULL,
  `duration_test_failed` bit(1) COLLATE latin1_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`fk_ethernet`, `lc_id`, `dvc_id`),
  FOREIGN KEY (`fk_ethernet`) references tbl_ethernet(`id_ethernet`) ON DELETE CASCADE,
  FOREIGN KEY (`fk_description`) references tbl_description(`id_description`) ON DELETE CASCADE
) ENGINE=InnoDB  COLLATE=latin1_general_ci;


--
-- Table structure for table `tbl_emergency_hist`
--

CREATE TABLE IF NOT EXISTS `tbl_emergency_hist` (
  `id_emergency_hist` int(11) NOT NULL UNIQUE AUTO_INCREMENT,
  `fk_ethernet` int(11) unsigned NOT NULL,
  `lc_id` int(11) unsigned NOT NULL,
  `dvc_id` int(11) unsigned NOT NULL,
  `fk_description` int(5) unsigned DEFAULT NULL,
  `test_type` enum('function','duration') COLLATE latin1_general_ci NOT NULL,
  `circuit_failure` bit(1) COLLATE latin1_general_ci NOT NULL,
  `new_circuit_failure` bit(1) NOT NULL DEFAULT 0,
  `battery_duration_failed` bit(1) COLLATE latin1_general_ci NOT NULL,
  `new_battery_duration_failed` bit(1) NOT NULL DEFAULT 0,
  `battery_failed` bit(1) COLLATE latin1_general_ci NOT NULL,
  `new_battery_failed` bit(1) NOT NULL DEFAULT 0,
  `emergency_lamp_failed` bit(1) COLLATE latin1_general_ci NOT NULL,
  `new_emergency_lamp_failed` bit(1) NOT NULL DEFAULT 0,
  `function_test_overdue` bit(1) COLLATE latin1_general_ci NOT NULL,
  `new_function_test_overdue` bit(1) NOT NULL DEFAULT 0,
  `duration_test_overdue` bit(1) COLLATE latin1_general_ci NOT NULL,
  `new_duration_test_overdue` bit(1) NOT NULL DEFAULT 0,
  `function_test_failed` bit(1) COLLATE latin1_general_ci NOT NULL,
  `new_function_test_failed` bit(1) NOT NULL DEFAULT 0,
  `duration_test_failed` bit(1) COLLATE latin1_general_ci NOT NULL,
  `new_duration_test_failed` bit(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_emergency_hist`),
  FOREIGN KEY (`fk_ethernet`) references tbl_ethernet(`id_ethernet`) ON DELETE CASCADE,
  FOREIGN KEY (`fk_description`) references tbl_description(`id_description`) ON DELETE CASCADE
) ENGINE=InnoDB  COLLATE=latin1_general_ci;


--
-- Indexes for table `tbl_emergency_hist`
--

CREATE INDEX `ethernet_lc_dvc` ON `tbl_emergency_hist`(`fk_ethernet`, `lc_id`, `dvc_id`);
CREATE INDEX `created_at` ON `tbl_emergency_hist`(`created_at`);



--
-- Triggers for table `tbl_emergency_hist`
--

DELIMITER $$
CREATE TRIGGER after_emergency_insert 
    AFTER INSERT ON tbl_emergency
    FOR EACH ROW BEGIN
 
	INSERT INTO tbl_emergency_hist
	SET 
	`fk_ethernet` = NEW.`fk_ethernet`,
	`lc_id` = NEW.`lc_id`,
	`dvc_id` = NEW.`dvc_id`,
	`fk_description` = NEW.`fk_description`,
	`test_type` = NEW.`test_type`,
	`circuit_failure` = NEW.`circuit_failure`,
	`battery_duration_failed` = NEW.`battery_duration_failed`,
	`battery_failed` = NEW.`battery_failed`,
	`emergency_lamp_failed` = NEW.`emergency_lamp_failed`,
	`function_test_overdue` = NEW.`function_test_overdue`,
	`duration_test_overdue` = NEW.`duration_test_overdue`,
	`function_test_failed` = NEW.`function_test_failed`,
	`duration_test_failed` = NEW.`duration_test_failed`,
	`created_at` = now();
	
	
END$$

CREATE TRIGGER after_emergency_update 
    AFTER UPDATE ON tbl_emergency
    FOR EACH ROW BEGIN
 
	INSERT INTO tbl_emergency_hist
	SET 
	`fk_ethernet` = NEW.`fk_ethernet`,
	`lc_id` = NEW.`lc_id`,
	`dvc_id` = NEW.`dvc_id`,
	`fk_description` = NEW.`fk_description`,
	`test_type` = NEW.`test_type`,
	`circuit_failure` = NEW.`circuit_failure`,
	`battery_duration_failed` = NEW.`battery_duration_failed`,
	`battery_failed` = NEW.`battery_failed`,
	`emergency_lamp_failed` = NEW.`emergency_lamp_failed`,
	`function_test_overdue` = NEW.`function_test_overdue`,
	`duration_test_overdue` = NEW.`duration_test_overdue`,
	`function_test_failed` = NEW.`function_test_failed`,
	`duration_test_failed` = NEW.`duration_test_failed`,
	`created_at` = now();
	
END$$

DELIMITER ;

CREATE VIEW vw_failure AS
	SELECT
	    S.fk_ethernet,
	    E.name AS ethernet_name,
	    S.lc_id,
	    S.dvc_id,
	    S.type,
	    S.fk_description,
	    D.description AS type_description,
	    S.new_lamp_failed as lamp_failed,
	    0 AS circuit_failure,
	    0 AS battery_duration_failed,
	    0 AS battery_failed,
	    0 AS emergency_lamp_failed,
	    0 AS function_test_overdue,
	    0 AS duration_test_overdue,
	    0 AS function_test_failed,
	    0 AS duration_test_failed,
	    S.created_at
	FROM tbl_status_hist S
	LEFT JOIN tbl_ethernet E ON S.fk_ethernet=E.id_ethernet
	LEFT JOIN tbl_description D ON S.fk_description=D.id_description
	WHERE new_lamp_failed=1
	UNION
	SELECT
	    T.fk_ethernet,
	    E.name AS ethernet_name,
	    T.lc_id,
	    T.dvc_id,
	    1 AS type,
	    T.fk_description,
	    D.description AS type_description,
	    0 AS lamp_failed,
	    T.new_circuit_failure AS circuit_failure,
	    T.new_battery_duration_failed AS battery_duration_failed,
	    T.new_battery_failed AS battery_failed,
	    T.new_emergency_lamp_failed AS emergency_lamp_failed,
	    T.new_function_test_overdue AS function_test_overdue,
	    T.new_duration_test_overdue AS duration_test_overdue,
	    T.new_function_test_failed AS function_test_failed,
	    T.new_duration_test_failed AS duration_test_failed,
	    T.created_at
	FROM tbl_emergency_hist T
	LEFT JOIN tbl_ethernet E ON T.fk_ethernet=E.id_ethernet
	LEFT JOIN tbl_description D ON T.fk_description=D.id_description
	WHERE new_circuit_failure=1
	OR new_battery_duration_failed=1
	OR new_battery_failed=1
	OR new_emergency_lamp_failed=1
	OR new_function_test_overdue=1
	OR new_duration_test_overdue=1
	OR new_function_test_failed=1
	OR new_duration_test_failed=1;
