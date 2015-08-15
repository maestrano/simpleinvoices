-- MySQL dump 10.13  Distrib 5.5.28, for Linux (x86_64)
--
-- Host: localhost    Database: simpleinvoices
-- ------------------------------------------------------
-- Server version	5.5.28

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
-- Table structure for table `si_biller`
--

DROP TABLE IF EXISTS `si_biller`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_biller` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `domain_id` int(11) NOT NULL DEFAULT '1',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `street_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `street_address2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zip_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile_phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `footer` text COLLATE utf8_unicode_ci,
  `paypal_business_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `paypal_notify_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `paypal_return_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `eway_customer_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `custom_field1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `custom_field2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `custom_field3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `custom_field4` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `enabled` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  PRIMARY KEY (`domain_id`,`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_biller`
--

LOCK TABLES `si_biller` WRITE;
/*!40000 ALTER TABLE `si_biller` DISABLE KEYS */;
/*!40000 ALTER TABLE `si_biller` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_cron`
--

DROP TABLE IF EXISTS `si_cron`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_cron` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `recurrence` int(11) NOT NULL,
  `recurrence_type` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `email_biller` int(1) DEFAULT NULL,
  `email_customer` int(1) DEFAULT NULL,
  PRIMARY KEY (`domain_id`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_cron`
--

LOCK TABLES `si_cron` WRITE;
/*!40000 ALTER TABLE `si_cron` DISABLE KEYS */;
/*!40000 ALTER TABLE `si_cron` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_cron_log`
--

DROP TABLE IF EXISTS `si_cron_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_cron_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_id` int(11) NOT NULL,
  `cron_id` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `run_date` date NOT NULL,
  PRIMARY KEY (`domain_id`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_cron_log`
--

LOCK TABLES `si_cron_log` WRITE;
/*!40000 ALTER TABLE `si_cron_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `si_cron_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_custom_fields`
--

DROP TABLE IF EXISTS `si_custom_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_custom_fields` (
  `cf_id` int(11) NOT NULL AUTO_INCREMENT,
  `cf_custom_field` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cf_custom_label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cf_display` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `domain_id` int(11) NOT NULL,
  PRIMARY KEY (`cf_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_custom_fields`
--

LOCK TABLES `si_custom_fields` WRITE;
/*!40000 ALTER TABLE `si_custom_fields` DISABLE KEYS */;
INSERT INTO `si_custom_fields` VALUES (1,'biller_cf1',NULL,'0',1),(2,'biller_cf2',NULL,'0',1),(3,'biller_cf3',NULL,'0',1),(4,'biller_cf4',NULL,'0',1),(5,'customer_cf1',NULL,'0',1),(6,'customer_cf2',NULL,'0',1),(7,'customer_cf3',NULL,'0',1),(8,'customer_cf4',NULL,'0',1),(9,'product_cf1',NULL,'0',1),(10,'product_cf2',NULL,'0',1),(11,'product_cf3',NULL,'0',1),(12,'product_cf4',NULL,'0',1),(13,'invoice_cf1',NULL,'0',1),(14,'invoice_cf2',NULL,'0',1),(15,'invoice_cf3',NULL,'0',1),(16,'invoice_cf4',NULL,'0',1);
/*!40000 ALTER TABLE `si_custom_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_customers`
--

DROP TABLE IF EXISTS `si_customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_customers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `domain_id` int(11) NOT NULL DEFAULT '1',
  `attention` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `street_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `street_address2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zip_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile_phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `credit_card_holder_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `credit_card_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `credit_card_expiry_month` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `credit_card_expiry_year` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `custom_field1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `custom_field2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `custom_field3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `custom_field4` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `enabled` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  PRIMARY KEY (`domain_id`,`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_customers`
--

LOCK TABLES `si_customers` WRITE;
/*!40000 ALTER TABLE `si_customers` DISABLE KEYS */;
/*!40000 ALTER TABLE `si_customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_extensions`
--

DROP TABLE IF EXISTS `si_extensions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_extensions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_extensions`
--

LOCK TABLES `si_extensions` WRITE;
/*!40000 ALTER TABLE `si_extensions` DISABLE KEYS */;
INSERT INTO `si_extensions` VALUES (1,0,'core','Core part of Simple Invoices - always enabled','1');
/*!40000 ALTER TABLE `si_extensions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_index`
--

DROP TABLE IF EXISTS `si_index`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_index` (
  `id` int(11) NOT NULL,
  `node` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sub_node` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sub_node_2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `domain_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_index`
--

LOCK TABLES `si_index` WRITE;
/*!40000 ALTER TABLE `si_index` DISABLE KEYS */;
/*!40000 ALTER TABLE `si_index` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_inventory`
--

DROP TABLE IF EXISTS `si_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` decimal(25,6) NOT NULL,
  `cost` decimal(25,6) DEFAULT NULL,
  `date` date NOT NULL,
  `note` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`domain_id`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_inventory`
--

LOCK TABLES `si_inventory` WRITE;
/*!40000 ALTER TABLE `si_inventory` DISABLE KEYS */;
/*!40000 ALTER TABLE `si_inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_invoice_item_tax`
--

DROP TABLE IF EXISTS `si_invoice_item_tax`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_invoice_item_tax` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_item_id` int(11) NOT NULL,
  `tax_id` int(11) NOT NULL,
  `tax_type` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `tax_rate` decimal(25,6) NOT NULL,
  `tax_amount` decimal(25,6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_invoice_item_tax`
--

LOCK TABLES `si_invoice_item_tax` WRITE;
/*!40000 ALTER TABLE `si_invoice_item_tax` DISABLE KEYS */;
/*!40000 ALTER TABLE `si_invoice_item_tax` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_invoice_items`
--

DROP TABLE IF EXISTS `si_invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_invoice_items` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) NOT NULL DEFAULT '0',
  `quantity` decimal(25,6) NOT NULL DEFAULT '0.000000',
  `product_id` int(10) DEFAULT '0',
  `unit_price` decimal(25,6) DEFAULT '0.000000',
  `tax_amount` decimal(25,6) DEFAULT '0.000000',
  `gross_total` decimal(25,6) DEFAULT '0.000000',
  `description` text COLLATE utf8_unicode_ci,
  `total` decimal(25,6) DEFAULT '0.000000',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_invoice_items`
--

LOCK TABLES `si_invoice_items` WRITE;
/*!40000 ALTER TABLE `si_invoice_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `si_invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_invoice_type`
--

DROP TABLE IF EXISTS `si_invoice_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_invoice_type` (
  `inv_ty_id` int(11) NOT NULL AUTO_INCREMENT,
  `inv_ty_description` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`inv_ty_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_invoice_type`
--

LOCK TABLES `si_invoice_type` WRITE;
/*!40000 ALTER TABLE `si_invoice_type` DISABLE KEYS */;
INSERT INTO `si_invoice_type` VALUES (1,'Total'),(2,'Itemised'),(3,'Consulting');
/*!40000 ALTER TABLE `si_invoice_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_invoices`
--

DROP TABLE IF EXISTS `si_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_invoices` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `index_id` int(11) NOT NULL,
  `domain_id` int(11) NOT NULL DEFAULT '1',
  `biller_id` int(10) NOT NULL DEFAULT '0',
  `customer_id` int(10) NOT NULL DEFAULT '0',
  `type_id` int(10) NOT NULL DEFAULT '0',
  `preference_id` int(10) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `custom_field1` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `custom_field2` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `custom_field3` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `custom_field4` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`domain_id`,`id`),
  KEY `domain_id` (`domain_id`),
  KEY `biller_id` (`biller_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_invoices`
--

LOCK TABLES `si_invoices` WRITE;
/*!40000 ALTER TABLE `si_invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `si_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_log`
--

DROP TABLE IF EXISTS `si_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userid` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `sqlquerie` text COLLATE utf8_unicode_ci NOT NULL,
  `last_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_log`
--

LOCK TABLES `si_log` WRITE;
/*!40000 ALTER TABLE `si_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `si_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_payment`
--

DROP TABLE IF EXISTS `si_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_payment` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ac_inv_id` int(11) NOT NULL,
  `ac_amount` decimal(25,6) NOT NULL,
  `ac_notes` text COLLATE utf8_unicode_ci NOT NULL,
  `ac_date` datetime NOT NULL,
  `ac_payment_type` int(10) NOT NULL DEFAULT '1',
  `domain_id` int(11) NOT NULL,
  `online_payment_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`domain_id`,`id`),
  KEY `domain_id` (`domain_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_payment`
--

LOCK TABLES `si_payment` WRITE;
/*!40000 ALTER TABLE `si_payment` DISABLE KEYS */;
/*!40000 ALTER TABLE `si_payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_payment_types`
--

DROP TABLE IF EXISTS `si_payment_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_payment_types` (
  `pt_id` int(10) NOT NULL AUTO_INCREMENT,
  `domain_id` int(11) NOT NULL DEFAULT '1',
  `pt_description` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `pt_enabled` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  PRIMARY KEY (`domain_id`,`pt_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_payment_types`
--

LOCK TABLES `si_payment_types` WRITE;
/*!40000 ALTER TABLE `si_payment_types` DISABLE KEYS */;
INSERT INTO `si_payment_types` VALUES (1,1,'Cash','1'),(2,1,'Credit Card','1');
/*!40000 ALTER TABLE `si_payment_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_preferences`
--

DROP TABLE IF EXISTS `si_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_preferences` (
  `pref_id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_id` int(11) NOT NULL DEFAULT '1',
  `pref_description` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pref_currency_sign` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pref_inv_heading` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pref_inv_wording` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pref_inv_detail_heading` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pref_inv_detail_line` text COLLATE utf8_unicode_ci,
  `pref_inv_payment_method` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pref_inv_payment_line1_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pref_inv_payment_line1_value` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pref_inv_payment_line2_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pref_inv_payment_line2_value` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pref_enabled` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `status` int(1) NOT NULL,
  `locale` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `language` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `index_group` int(11) NOT NULL,
  `currency_code` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `include_online_payment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `currency_position` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`domain_id`,`pref_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_preferences`
--

LOCK TABLES `si_preferences` WRITE;
/*!40000 ALTER TABLE `si_preferences` DISABLE KEYS */;
INSERT INTO `si_preferences` VALUES (1,1,'Invoice','$','Invoice','Invoice','Details','Payment is to be made within 14 days of the invoice being sent','Electronic Funds Transfer','Account name','H. & M. Simpson','Account number:','0123-4567-7890','1',1,'en_GB','en_GB',1,'USD',NULL,'left'),(2,1,'Receipt','$','Receipt','Receipt','Details','<br />This transaction has been paid in full, please keep this receipt as proof of purchase.<br /> Thank you','','','','','','1',1,'en_GB','en_GB',1,'USD',NULL,'left'),(3,1,'Estimate','$','Estimate','Estimate','Details','<br />This is an estimate of the final value of services rendered.<br />Thank you','','','','','','1',1,'en_GB','en_GB',1,'USD',NULL,'left'),(4,1,'Quote','$','Quote','Quote','Details','<br />This is a quote of the final value of services rendered.<br />Thank you','','','','','','1',1,'en_GB','en_GB',1,'USD',NULL,'left');
/*!40000 ALTER TABLE `si_preferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_products`
--

DROP TABLE IF EXISTS `si_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_id` int(11) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `unit_price` decimal(25,6) DEFAULT '0.000000',
  `default_tax_id` int(11) DEFAULT NULL,
  `default_tax_id_2` int(11) DEFAULT NULL,
  `cost` decimal(25,6) DEFAULT '0.000000',
  `reorder_level` int(11) DEFAULT NULL,
  `custom_field1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `custom_field2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `custom_field3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `custom_field4` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8_unicode_ci NOT NULL,
  `enabled` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`domain_id`,`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_products`
--

LOCK TABLES `si_products` WRITE;
/*!40000 ALTER TABLE `si_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `si_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_sql_patchmanager`
--

DROP TABLE IF EXISTS `si_sql_patchmanager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_sql_patchmanager` (
  `sql_id` int(11) NOT NULL AUTO_INCREMENT,
  `sql_patch_ref` int(11) NOT NULL,
  `sql_patch` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sql_release` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sql_statement` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`sql_id`)
) ENGINE=MyISAM AUTO_INCREMENT=798 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_sql_patchmanager`
--

LOCK TABLES `si_sql_patchmanager` WRITE;
/*!40000 ALTER TABLE `si_sql_patchmanager` DISABLE KEYS */;
INSERT INTO `si_sql_patchmanager` VALUES (1,1,'Create sql_patchmanger table','20060514','CREATE TABLE si_sql_patchmanager (sql_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,sql_patch_ref VARCHAR( 50 ) NOT NULL ,sql_patch VARCHAR( 255 ) NOT NULL ,sql_release VARCHAR( 25 ) NOT NULL ,sql_statement TEXT NOT NULL) ENGINE = MYISAM '),(2,2,'','',''),(3,3,'','',''),(4,4,'','',''),(5,5,'','',''),(6,6,'','',''),(7,7,'','',''),(8,8,'','',''),(9,9,'','',''),(10,10,'','',''),(11,11,'','',''),(12,12,'','',''),(13,13,'','',''),(14,14,'','',''),(15,15,'','',''),(16,16,'','',''),(17,17,'','',''),(18,18,'','',''),(19,19,'','',''),(20,20,'','',''),(21,21,'','',''),(22,22,'','',''),(23,23,'','',''),(24,24,'','',''),(25,25,'','',''),(26,26,'','',''),(27,27,'','',''),(28,28,'','',''),(29,29,'','',''),(30,30,'','',''),(31,31,'','',''),(32,32,'','',''),(33,33,'','',''),(34,34,'','',''),(35,35,'','',''),(36,36,'','',''),(37,0,'Start','20060514',''),(38,37,'','',''),(39,38,'','',''),(40,39,'','',''),(41,40,'','',''),(42,41,'','',''),(43,42,'','',''),(44,43,'','',''),(45,44,'','',''),(46,45,'','',''),(47,46,'','',''),(48,47,'','',''),(49,48,'','',''),(50,49,'','',''),(51,50,'','',''),(52,51,'','',''),(53,52,'','',''),(54,53,'','',''),(599,54,'','',''),(600,55,'','',''),(601,56,'','',''),(602,57,'','',''),(603,58,'','',''),(604,59,'','',''),(605,60,'','',''),(606,61,'','',''),(607,62,'','',''),(608,63,'','',''),(609,64,'','',''),(610,65,'','',''),(611,66,'','',''),(612,67,'','',''),(613,68,'','',''),(614,69,'','',''),(615,70,'','',''),(616,71,'','',''),(617,72,'','',''),(618,73,'','',''),(619,74,'','',''),(620,75,'','',''),(621,76,'','',''),(622,77,'','',''),(623,78,'','',''),(624,79,'','',''),(625,80,'','',''),(626,81,'','',''),(627,82,'','',''),(628,83,'','',''),(629,84,'','',''),(630,85,'','',''),(631,86,'','',''),(632,87,'','',''),(633,88,'','',''),(634,89,'','',''),(635,90,'','',''),(636,91,'','',''),(637,92,'','',''),(638,93,'','',''),(639,94,'','',''),(640,95,'','',''),(641,96,'','',''),(642,97,'','',''),(643,98,'','',''),(644,99,'','',''),(645,100,'','',''),(646,101,'','',''),(647,102,'','',''),(648,103,'','',''),(649,104,'','',''),(650,105,'','',''),(651,106,'','',''),(652,107,'','',''),(653,108,'','',''),(654,109,'','',''),(655,110,'','',''),(656,111,'','',''),(657,112,'','',''),(658,113,'','',''),(659,114,'','',''),(660,115,'','',''),(661,116,'','',''),(662,117,'','',''),(663,118,'','',''),(664,119,'','',''),(665,120,'','',''),(666,121,'','',''),(667,122,'','',''),(668,123,'','',''),(669,124,'','',''),(670,125,'','',''),(671,126,'','',''),(672,127,'','',''),(673,128,'','',''),(674,129,'','',''),(675,130,'','',''),(676,131,'','',''),(677,132,'','',''),(678,133,'','',''),(679,134,'','',''),(680,135,'','',''),(681,136,'','',''),(682,137,'','',''),(683,138,'','',''),(684,139,'','',''),(685,140,'','',''),(686,141,'','',''),(687,142,'','',''),(688,143,'','',''),(689,144,'','',''),(690,145,'','',''),(691,146,'','',''),(692,147,'','',''),(693,148,'','',''),(694,149,'','',''),(695,150,'','',''),(696,151,'','',''),(697,152,'','',''),(698,153,'','',''),(699,154,'','',''),(700,155,'','',''),(701,156,'','',''),(702,157,'','',''),(703,158,'','',''),(704,159,'','',''),(705,160,'','',''),(706,161,'','',''),(707,162,'','',''),(708,163,'','',''),(709,164,'','',''),(710,165,'','',''),(711,166,'','',''),(712,167,'','',''),(713,168,'','',''),(714,169,'','',''),(715,170,'','',''),(716,171,'','',''),(717,172,'','',''),(718,173,'','',''),(719,174,'','',''),(720,175,'','',''),(721,176,'','',''),(722,177,'','',''),(723,178,'','',''),(724,179,'','',''),(725,180,'','',''),(726,181,'','',''),(727,182,'','',''),(728,183,'','',''),(729,184,'','',''),(730,185,'','',''),(731,186,'','',''),(732,187,'','',''),(733,188,'','',''),(734,189,'','',''),(735,190,'','',''),(736,191,'','',''),(737,192,'','',''),(738,193,'','',''),(739,194,'','',''),(740,195,'','',''),(741,196,'','',''),(742,197,'','',''),(743,198,'','',''),(744,199,'','',''),(745,200,'Update extensions table','20090529','UPDATE si_extensions SET id = 0 WHERE name = core LIMIT 1'),(746,201,'Set domain_id on system defaults table to 1','20090622','UPDATE si_system_defaults SET domain_id = 1'),(747,202,'Set extension_id on system defaults table to 1','20090622','UPDATE si_system_defaults SET extension_id = 1'),(748,203,'Move all old consulting style invoices to itemised','20090704','UPDATE si_invoices SET type_id = 2 where type_id = 3'),(749,204,'','',''),(750,205,'','',''),(751,206,'','',''),(752,207,'','',''),(753,208,'','',''),(754,209,'','',''),(755,210,'','',''),(756,210,'','',''),(757,211,'','',''),(758,212,'','',''),(759,213,'','',''),(760,214,'','',''),(761,215,'','',''),(762,216,'','',''),(763,217,'','',''),(764,218,'','',''),(765,219,'','',''),(766,220,'','',''),(767,221,'','',''),(768,222,'','',''),(769,223,'','',''),(770,224,'','',''),(771,225,'','',''),(772,226,'','',''),(773,227,'','',''),(774,228,'','',''),(775,229,'','',''),(776,230,'','',''),(777,231,'','',''),(778,232,'','',''),(779,233,'','',''),(780,234,'','',''),(781,235,'','',''),(782,236,'','',''),(783,237,'','',''),(784,238,'','',''),(785,239,'','',''),(786,240,'','',''),(787,241,'','',''),(788,242,'','',''),(789,243,'','',''),(790,244,'','',''),(791,245,'','',''),(792,246,'','',''),(793,247,'','',''),(794,248,'','',''),(795,249,'','',''),(796,250,'','',''),(797,251,'','','');
/*!40000 ALTER TABLE `si_sql_patchmanager` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_system_defaults`
--

DROP TABLE IF EXISTS `si_system_defaults`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_system_defaults` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `domain_id` int(5) NOT NULL DEFAULT '0',
  `extension_id` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`domain_id`,`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_system_defaults`
--

LOCK TABLES `si_system_defaults` WRITE;
/*!40000 ALTER TABLE `si_system_defaults` DISABLE KEYS */;
INSERT INTO `si_system_defaults` VALUES (1,'biller','4',1,1),(2,'customer','3',1,1),(3,'tax','1',1,1),(4,'preference','1',1,1),(5,'line_items','5',1,1),(6,'template','default',1,1),(7,'payment_type','1',1,1),(8,'language','en_GB',1,1),(9,'dateformate','Y-m-d',1,1),(10,'spreadsheet','xls',1,1),(11,'wordprocessor','doc',1,1),(12,'pdfscreensize','800',1,1),(13,'pdfpapersize','A4',1,1),(14,'pdfleftmargin','15',1,1),(15,'pdfrightmargin','15',1,1),(16,'pdftopmargin','15',1,1),(17,'pdfbottommargin','15',1,1),(18,'emailhost','localhost',1,1),(19,'emailusername','',1,1),(20,'emailpassword','',1,1),(21,'logging','0',1,1),(22,'delete','N',1,1),(23,'tax_per_line_item','1',1,1),(24,'inventory','0',1,1);
/*!40000 ALTER TABLE `si_system_defaults` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_tax`
--

DROP TABLE IF EXISTS `si_tax`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_tax` (
  `tax_id` int(11) NOT NULL AUTO_INCREMENT,
  `tax_description` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tax_percentage` decimal(25,6) DEFAULT '0.000000',
  `type` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tax_enabled` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `domain_id` int(11) NOT NULL,
  PRIMARY KEY (`domain_id`,`tax_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_tax`
--

LOCK TABLES `si_tax` WRITE;
/*!40000 ALTER TABLE `si_tax` DISABLE KEYS */;
INSERT INTO `si_tax` VALUES (1,'GST',10.000000,'%','1',1),(2,'VAT',10.000000,'%','1',1),(3,'Sales Tax',10.000000,'%','1',1),(4,'No Tax',0.000000,'%','1',1),(5,'Postage',20.000000,'$','1',1);
/*!40000 ALTER TABLE `si_tax` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_user`
--

DROP TABLE IF EXISTS `si_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `domain_id` int(11) NOT NULL DEFAULT '0',
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `enabled` int(1) NOT NULL,
  PRIMARY KEY (`domain_id`,`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_user`
--

LOCK TABLES `si_user` WRITE;
/*!40000 ALTER TABLE `si_user` DISABLE KEYS */;
INSERT INTO `si_user` VALUES (1,'admin@example.com',1,1,'5f4dcc3b5aa765d61d8327deb882cf99',1);
/*!40000 ALTER TABLE `si_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_user_domain`
--

DROP TABLE IF EXISTS `si_user_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_user_domain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_user_domain`
--

LOCK TABLES `si_user_domain` WRITE;
/*!40000 ALTER TABLE `si_user_domain` DISABLE KEYS */;
INSERT INTO `si_user_domain` VALUES (1,'default');
/*!40000 ALTER TABLE `si_user_domain` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `si_user_role`
--

DROP TABLE IF EXISTS `si_user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `si_user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `si_user_role`
--

LOCK TABLES `si_user_role` WRITE;
/*!40000 ALTER TABLE `si_user_role` DISABLE KEYS */;
INSERT INTO `si_user_role` VALUES (1,'administrator');
/*!40000 ALTER TABLE `si_user_role` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-12-23  7:31:49