-- MySQL dump 10.13  Distrib 5.7.19, for Linux (x86_64)
--
-- Host: localhost    Database: sspanel
-- ------------------------------------------------------
-- Server version	5.7.19-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `alive_ip`
--

DROP TABLE IF EXISTS `alive_ip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alive_ip` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nodeid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `ip` text NOT NULL,
  `datetime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alive_ip`
--

LOCK TABLES `alive_ip` WRITE;
/*!40000 ALTER TABLE `alive_ip` DISABLE KEYS */;
/*!40000 ALTER TABLE `alive_ip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `announcement`
--

DROP TABLE IF EXISTS `announcement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `content` longtext NOT NULL,
  `markdown` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcement`
--

LOCK TABLES `announcement` WRITE;
/*!40000 ALTER TABLE `announcement` DISABLE KEYS */;
/*!40000 ALTER TABLE `announcement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auto`
--

DROP TABLE IF EXISTS `auto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auto` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `value` longtext NOT NULL,
  `sign` longtext NOT NULL,
  `datetime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auto`
--

LOCK TABLES `auto` WRITE;
/*!40000 ALTER TABLE `auto` DISABLE KEYS */;
/*!40000 ALTER TABLE `auto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blockip`
--

DROP TABLE IF EXISTS `blockip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blockip` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nodeid` int(11) NOT NULL,
  `ip` text NOT NULL,
  `datetime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blockip`
--

LOCK TABLES `blockip` WRITE;
/*!40000 ALTER TABLE `blockip` DISABLE KEYS */;
/*!40000 ALTER TABLE `blockip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bought`
--

DROP TABLE IF EXISTS `bought`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bought` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) NOT NULL,
  `shopid` bigint(20) NOT NULL,
  `datetime` bigint(20) NOT NULL,
  `renew` bigint(11) NOT NULL,
  `coupon` text NOT NULL,
  `price` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bought`
--

LOCK TABLES `bought` WRITE;
/*!40000 ALTER TABLE `bought` DISABLE KEYS */;
/*!40000 ALTER TABLE `bought` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `code`
--

DROP TABLE IF EXISTS `code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `code` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` text NOT NULL,
  `type` int(11) NOT NULL,
  `number` decimal(11,2) NOT NULL,
  `isused` int(11) NOT NULL DEFAULT '0',
  `userid` bigint(20) NOT NULL,
  `usedatetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `code`
--

LOCK TABLES `code` WRITE;
/*!40000 ALTER TABLE `code` DISABLE KEYS */;
/*!40000 ALTER TABLE `code` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupon`
--

DROP TABLE IF EXISTS `coupon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coupon` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` text NOT NULL,
  `onetime` int(11) NOT NULL,
  `expire` bigint(20) NOT NULL,
  `shop` text NOT NULL,
  `credit` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupon`
--

LOCK TABLES `coupon` WRITE;
/*!40000 ALTER TABLE `coupon` DISABLE KEYS */;
/*!40000 ALTER TABLE `coupon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detect_list`
--

DROP TABLE IF EXISTS `detect_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detect_list` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` longtext NOT NULL,
  `text` longtext NOT NULL,
  `regex` longtext NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detect_list`
--

LOCK TABLES `detect_list` WRITE;
/*!40000 ALTER TABLE `detect_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `detect_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detect_log`
--

DROP TABLE IF EXISTS `detect_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detect_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `list_id` bigint(20) NOT NULL,
  `datetime` bigint(20) NOT NULL,
  `node_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detect_log`
--

LOCK TABLES `detect_log` WRITE;
/*!40000 ALTER TABLE `detect_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `detect_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disconnect_ip`
--

DROP TABLE IF EXISTS `disconnect_ip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disconnect_ip` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) NOT NULL,
  `ip` text NOT NULL,
  `datetime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disconnect_ip`
--

LOCK TABLES `disconnect_ip` WRITE;
/*!40000 ALTER TABLE `disconnect_ip` DISABLE KEYS */;
/*!40000 ALTER TABLE `disconnect_ip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_verify`
--

DROP TABLE IF EXISTS `email_verify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_verify` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` text NOT NULL,
  `ip` text NOT NULL,
  `code` text NOT NULL,
  `expire_in` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_verify`
--

LOCK TABLES `email_verify` WRITE;
/*!40000 ALTER TABLE `email_verify` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_verify` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `link`
--

DROP TABLE IF EXISTS `link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `link` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `address` text NOT NULL,
  `port` int(11) NOT NULL,
  `token` text NOT NULL,
  `ios` int(11) NOT NULL DEFAULT '0',
  `userid` bigint(20) NOT NULL,
  `isp` text,
  `geo` int(11) DEFAULT NULL,
  `method` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `link`
--

LOCK TABLES `link` WRITE;
/*!40000 ALTER TABLE `link` DISABLE KEYS */;
/*!40000 ALTER TABLE `link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_ip`
--

DROP TABLE IF EXISTS `login_ip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_ip` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) NOT NULL,
  `ip` text NOT NULL,
  `datetime` bigint(20) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_ip`
--

LOCK TABLES `login_ip` WRITE;
/*!40000 ALTER TABLE `login_ip` DISABLE KEYS */;
INSERT INTO `login_ip` VALUES (2,2,'107.148.255.107',1560181557,1),(3,2,'107.148.255.107',1560181574,1),(4,2,'107.148.255.107',1560181591,1),(5,2,'107.148.255.107',1560181624,1),(6,2,'107.148.255.107',1560181637,1),(7,2,'107.148.255.107',1560181655,1),(8,2,'107.148.255.107',1560181776,0);
/*!40000 ALTER TABLE `login_ip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payback`
--

DROP TABLE IF EXISTS `payback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payback` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `total` decimal(12,2) NOT NULL,
  `userid` bigint(20) NOT NULL,
  `ref_by` bigint(20) NOT NULL,
  `ref_get` decimal(12,2) NOT NULL,
  `datetime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payback`
--

LOCK TABLES `payback` WRITE;
/*!40000 ALTER TABLE `payback` DISABLE KEYS */;
/*!40000 ALTER TABLE `payback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paylist`
--

DROP TABLE IF EXISTS `paylist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paylist` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `tradeno` text,
  `datetime` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paylist`
--

LOCK TABLES `paylist` WRITE;
/*!40000 ALTER TABLE `paylist` DISABLE KEYS */;
/*!40000 ALTER TABLE `paylist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `radius_ban`
--

DROP TABLE IF EXISTS `radius_ban`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `radius_ban` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `radius_ban`
--

LOCK TABLES `radius_ban` WRITE;
/*!40000 ALTER TABLE `radius_ban` DISABLE KEYS */;
/*!40000 ALTER TABLE `radius_ban` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `relay`
--

DROP TABLE IF EXISTS `relay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `relay` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `source_node_id` bigint(20) NOT NULL,
  `dist_node_id` bigint(20) NOT NULL,
  `dist_ip` text NOT NULL,
  `port` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `relay`
--

LOCK TABLES `relay` WRITE;
/*!40000 ALTER TABLE `relay` DISABLE KEYS */;
/*!40000 ALTER TABLE `relay` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop`
--

DROP TABLE IF EXISTS `shop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `content` text NOT NULL,
  `auto_renew` int(11) NOT NULL,
  `auto_reset_bandwidth` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop`
--

LOCK TABLES `shop` WRITE;
/*!40000 ALTER TABLE `shop` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `speedtest`
--

DROP TABLE IF EXISTS `speedtest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `speedtest` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nodeid` int(11) NOT NULL,
  `datetime` bigint(20) NOT NULL,
  `telecomping` text NOT NULL,
  `telecomeupload` text NOT NULL,
  `telecomedownload` text NOT NULL,
  `unicomping` text NOT NULL,
  `unicomupload` text NOT NULL,
  `unicomdownload` text NOT NULL,
  `cmccping` text NOT NULL,
  `cmccupload` text NOT NULL,
  `cmccdownload` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `speedtest`
--

LOCK TABLES `speedtest` WRITE;
/*!40000 ALTER TABLE `speedtest` DISABLE KEYS */;
/*!40000 ALTER TABLE `speedtest` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ss_invite_code`
--

DROP TABLE IF EXISTS `ss_invite_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ss_invite_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(128) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '2016-06-01 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ss_invite_code`
--

LOCK TABLES `ss_invite_code` WRITE;
/*!40000 ALTER TABLE `ss_invite_code` DISABLE KEYS */;
/*!40000 ALTER TABLE `ss_invite_code` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ss_node`
--

DROP TABLE IF EXISTS `ss_node`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ss_node` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `type` int(3) NOT NULL,
  `server` varchar(128) NOT NULL,
  `method` varchar(64) NOT NULL,
  `info` varchar(128) NOT NULL,
  `status` varchar(128) NOT NULL,
  `sort` int(3) NOT NULL,
  `custom_method` tinyint(1) NOT NULL DEFAULT '0',
  `traffic_rate` float NOT NULL DEFAULT '1',
  `node_class` int(11) NOT NULL DEFAULT '0',
  `node_speedlimit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `node_connector` int(11) NOT NULL DEFAULT '0',
  `node_bandwidth` bigint(20) NOT NULL DEFAULT '0',
  `node_bandwidth_limit` bigint(20) NOT NULL DEFAULT '0',
  `bandwidthlimit_resetday` int(11) NOT NULL DEFAULT '0',
  `node_heartbeat` bigint(20) NOT NULL DEFAULT '0',
  `node_ip` text,
  `node_group` int(11) NOT NULL DEFAULT '0',
  `custom_rss` int(11) NOT NULL DEFAULT '0',
  `mu_only` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ss_node`
--

LOCK TABLES `ss_node` WRITE;
/*!40000 ALTER TABLE `ss_node` DISABLE KEYS */;
INSERT INTO `ss_node` VALUES (1,'统一验证登陆',0,'zhaojin97.cn','radius','统一登陆验证','可用',999,0,1,0,0.00,0,0,0,0,0,'',0,0,0),(2,'VPN 统一流量结算',0,'zhaojin97.cn','radius','VPN 统一流量结算','可用',999,0,1,0,0.00,0,0,0,0,0,NULL,0,0,0);
/*!40000 ALTER TABLE `ss_node` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ss_node_info`
--

DROP TABLE IF EXISTS `ss_node_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ss_node_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) NOT NULL,
  `uptime` float NOT NULL,
  `load` varchar(32) NOT NULL,
  `log_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ss_node_info`
--

LOCK TABLES `ss_node_info` WRITE;
/*!40000 ALTER TABLE `ss_node_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `ss_node_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ss_node_online_log`
--

DROP TABLE IF EXISTS `ss_node_online_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ss_node_online_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) NOT NULL,
  `online_user` int(11) NOT NULL,
  `log_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ss_node_online_log`
--

LOCK TABLES `ss_node_online_log` WRITE;
/*!40000 ALTER TABLE `ss_node_online_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `ss_node_online_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ss_password_reset`
--

DROP TABLE IF EXISTS `ss_password_reset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ss_password_reset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(32) NOT NULL,
  `token` varchar(128) NOT NULL,
  `init_time` int(11) NOT NULL,
  `expire_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ss_password_reset`
--

LOCK TABLES `ss_password_reset` WRITE;
/*!40000 ALTER TABLE `ss_password_reset` DISABLE KEYS */;
/*!40000 ALTER TABLE `ss_password_reset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `telegram_session`
--

DROP TABLE IF EXISTS `telegram_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `telegram_session` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `type` int(11) NOT NULL,
  `session_content` text NOT NULL,
  `datetime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `telegram_session`
--

LOCK TABLES `telegram_session` WRITE;
/*!40000 ALTER TABLE `telegram_session` DISABLE KEYS */;
INSERT INTO `telegram_session` VALUES (1,2,0,'dNeodjPGPyUqSRgo',1560181805);
/*!40000 ALTER TABLE `telegram_session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket`
--

DROP TABLE IF EXISTS `ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` longtext NOT NULL,
  `content` longtext NOT NULL,
  `rootid` bigint(20) NOT NULL,
  `userid` bigint(20) NOT NULL,
  `datetime` bigint(20) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket`
--

LOCK TABLES `ticket` WRITE;
/*!40000 ALTER TABLE `ticket` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unblockip`
--

DROP TABLE IF EXISTS `unblockip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unblockip` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ip` text NOT NULL,
  `datetime` bigint(20) NOT NULL,
  `userid` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unblockip`
--

LOCK TABLES `unblockip` WRITE;
/*!40000 ALTER TABLE `unblockip` DISABLE KEYS */;
/*!40000 ALTER TABLE `unblockip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(128) CHARACTER SET utf8mb4 NOT NULL,
  `email` varchar(32) NOT NULL,
  `pass` varchar(64) NOT NULL,
  `passwd` varchar(16) NOT NULL,
  `t` int(11) NOT NULL DEFAULT '0',
  `u` bigint(20) NOT NULL,
  `d` bigint(20) NOT NULL,
  `plan` varchar(2) CHARACTER SET utf8mb4 NOT NULL DEFAULT 'A',
  `transfer_enable` bigint(20) NOT NULL,
  `port` int(11) NOT NULL,
  `switch` tinyint(4) NOT NULL DEFAULT '1',
  `enable` tinyint(4) NOT NULL DEFAULT '1',
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `last_get_gift_time` int(11) NOT NULL DEFAULT '0',
  `last_check_in_time` int(11) NOT NULL DEFAULT '0',
  `last_rest_pass_time` int(11) NOT NULL DEFAULT '0',
  `reg_date` datetime NOT NULL,
  `invite_num` int(8) NOT NULL,
  `money` decimal(12,2) NOT NULL,
  `ref_by` int(11) NOT NULL DEFAULT '0',
  `expire_time` int(11) NOT NULL DEFAULT '0',
  `method` varchar(64) NOT NULL DEFAULT 'rc4-md5',
  `is_email_verify` tinyint(4) NOT NULL DEFAULT '0',
  `reg_ip` varchar(128) NOT NULL DEFAULT '127.0.0.1',
  `node_speedlimit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `node_connector` int(11) NOT NULL DEFAULT '0',
  `is_admin` int(2) NOT NULL DEFAULT '0',
  `im_type` int(11) DEFAULT '1',
  `im_value` text,
  `last_day_t` bigint(20) NOT NULL DEFAULT '0',
  `sendDailyMail` int(11) NOT NULL DEFAULT '0',
  `class` int(11) NOT NULL DEFAULT '0',
  `class_expire` datetime NOT NULL DEFAULT '1989-06-04 00:05:00',
  `expire_in` datetime NOT NULL DEFAULT '2099-06-04 00:05:00',
  `theme` text NOT NULL,
  `ga_token` text NOT NULL,
  `ga_enable` int(11) NOT NULL DEFAULT '0',
  `pac` longtext,
  `remark` text,
  `node_group` int(11) NOT NULL DEFAULT '0',
  `auto_reset_day` int(11) NOT NULL DEFAULT '0',
  `auto_reset_bandwidth` decimal(12,2) NOT NULL DEFAULT '0.00',
  `protocol` varchar(128) DEFAULT 'origin',
  `protocol_param` varchar(128) DEFAULT NULL,
  `obfs` varchar(128) DEFAULT 'plain',
  `obfs_param` varchar(128) DEFAULT NULL,
  `forbidden_ip` longtext,
  `forbidden_port` longtext,
  `disconnect_ip` longtext,
  `is_hide` int(11) NOT NULL DEFAULT '0',
  `is_multi_user` int(11) NOT NULL DEFAULT '0',
  `telegram_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_name` (`user_name`),
  KEY `uid` (`id`),
  KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (2,'admin','admin','ef797c8118f02dfb649607dd5d3f8c7623048c9c063d532cc95c5ed7a898a64f','NhaS8C',0,0,0,'A',1073741824,1025,1,1,1,0,0,0,'2019-06-07 10:28:29',10,0.00,0,0,'rc4-md5',0,'127.0.0.1',0.00,0,1,1,'',0,0,0,'1989-06-04 00:05:00','2029-06-04 10:28:29','material','NEI7ASEYBDPM7ORP',0,NULL,NULL,0,0,0.00,'origin',NULL,'plain',NULL,NULL,NULL,NULL,0,0,NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_token`
--

DROP TABLE IF EXISTS `user_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(256) NOT NULL,
  `user_id` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `expire_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_token`
--

LOCK TABLES `user_token` WRITE;
/*!40000 ALTER TABLE `user_token` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_traffic_log`
--

DROP TABLE IF EXISTS `user_traffic_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_traffic_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `u` bigint(20) NOT NULL,
  `d` bigint(20) NOT NULL,
  `node_id` int(11) NOT NULL,
  `rate` float NOT NULL,
  `traffic` varchar(32) NOT NULL,
  `log_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_traffic_log`
--

LOCK TABLES `user_traffic_log` WRITE;
/*!40000 ALTER TABLE `user_traffic_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_traffic_log` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-06-10 23:51:26
