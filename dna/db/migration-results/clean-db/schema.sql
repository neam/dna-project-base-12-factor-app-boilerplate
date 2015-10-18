-- MySQL dump 10.13  Distrib 5.5.44, for debian-linux-gnu (x86_64)
--
-- Host: localdb    Database: db_clean_db
-- ------------------------------------------------------
-- Server version	5.6.27

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
-- Table structure for table `account`
--

DROP TABLE IF EXISTS `account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) COLLATE utf8_bin NOT NULL DEFAULT '',
  `password` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `email` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `activkey` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `superuser` int(1) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '0',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastvisit_at` timestamp NULL DEFAULT NULL,
  `salt` varchar(255) COLLATE utf8_bin NOT NULL,
  `passwordStrategy` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'legacy',
  `requireNewPassword` tinyint(1) NOT NULL DEFAULT '0',
  `lastLoginAt` timestamp NULL DEFAULT NULL,
  `lastActiveAt` timestamp NULL DEFAULT NULL,
  `node_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_username` (`username`),
  KEY `fk_account_node1_idx` (`node_id`),
  CONSTRAINT `fk_account_node1` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auth0_user`
--

DROP TABLE IF EXISTS `auth0_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth0_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL,
  `auth0_app` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `auth0_user_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `auth0_last_authentication_at` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `auth0_last_verified_token` text COLLATE utf8_bin,
  `auth0_last_verified_token_expires` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_auth0_user_account1_idx` (`account_id`),
  CONSTRAINT `fk_auth0_user_account1` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `changeset`
--

DROP TABLE IF EXISTS `changeset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `changeset` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `contents` text COLLATE utf8_bin,
  `user_id` int(11) NOT NULL,
  `node_id` bigint(20) NOT NULL,
  `reward` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_changeset_users1_idx` (`user_id`),
  KEY `fk_changeset_node1_idx` (`node_id`),
  CONSTRAINT `fk_changeset_node1` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_changeset_users1` FOREIGN KEY (`user_id`) REFERENCES `account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `edge`
--

DROP TABLE IF EXISTS `edge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `edge` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `from_node_id` bigint(20) NOT NULL,
  `to_node_id` bigint(20) NOT NULL,
  `weight` int(11) DEFAULT NULL,
  `_title` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `relation` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_node_has_node_node2_idx` (`to_node_id`),
  KEY `fk_node_has_node_node1_idx` (`from_node_id`),
  CONSTRAINT `fk_node_has_node_node1` FOREIGN KEY (`from_node_id`) REFERENCES `node` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_node_has_node_node2` FOREIGN KEY (`to_node_id`) REFERENCES `node` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `file`
--

DROP TABLE IF EXISTS `file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `file` (
  `id` int(11) NOT NULL,
  `path` text COLLATE utf8_bin,
  `node_id` bigint(20) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_file_registry_entry_node1_idx` (`node_id`),
  CONSTRAINT `fk_file_registry_entry_node1` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `file_instance`
--

DROP TABLE IF EXISTS `file_instance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `file_instance` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) DEFAULT NULL,
  `storage_component_ref` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_file_file_registry_entry1_idx` (`file_id`),
  CONSTRAINT `fk_file_instance_file1` FOREIGN KEY (`file_id`) REFERENCES `file` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `group`
--

DROP TABLE IF EXISTS `group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `ref` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `node_id` bigint(20) DEFAULT NULL,
  `group_qa_state_id` bigint(20) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `group_qa_state_id_fk` (`group_qa_state_id`),
  KEY `fk_group_node1_idx` (`node_id`),
  KEY `fk_group_account1_idx` (`owner_id`),
  CONSTRAINT `fk_group_account1` FOREIGN KEY (`owner_id`) REFERENCES `account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_group_node1` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `group_qa_state_id_fk` FOREIGN KEY (`group_qa_state_id`) REFERENCES `group_qa_state` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB /*AUTO_INCREMENT omitted*/ DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `group_has_account`
--

DROP TABLE IF EXISTS `group_has_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_has_account` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `group_id` bigint(20) NOT NULL,
  `role_id` bigint(20) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_group_has_account_account1_idx` (`account_id`),
  KEY `fk_group_has_account_group1_idx` (`group_id`),
  KEY `fk_group_has_account_role1_idx` (`role_id`),
  CONSTRAINT `fk_group_has_account_account1` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_group_has_account_group1` FOREIGN KEY (`group_id`) REFERENCES `group` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_group_has_account_role1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `group_qa_state`
--

DROP TABLE IF EXISTS `group_qa_state`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_qa_state` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `status` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `draft_validation_progress` int(11) DEFAULT NULL,
  `reviewable_validation_progress` int(11) DEFAULT NULL,
  `publishable_validation_progress` int(11) DEFAULT NULL,
  `translate_into_en_validation_progress` int(11) DEFAULT NULL,
  `translate_into_ar_validation_progress` int(11) DEFAULT NULL,
  `translate_into_bg_validation_progress` int(11) DEFAULT NULL,
  `translate_into_ca_validation_progress` int(11) DEFAULT NULL,
  `translate_into_cs_validation_progress` int(11) DEFAULT NULL,
  `translate_into_da_validation_progress` int(11) DEFAULT NULL,
  `translate_into_de_validation_progress` int(11) DEFAULT NULL,
  `translate_into_en_gb_validation_progress` int(11) DEFAULT NULL,
  `translate_into_en_us_validation_progress` int(11) DEFAULT NULL,
  `translate_into_el_validation_progress` int(11) DEFAULT NULL,
  `translate_into_es_validation_progress` int(11) DEFAULT NULL,
  `translate_into_fa_validation_progress` int(11) DEFAULT NULL,
  `translate_into_fi_validation_progress` int(11) DEFAULT NULL,
  `translate_into_fil_validation_progress` int(11) DEFAULT NULL,
  `translate_into_fr_validation_progress` int(11) DEFAULT NULL,
  `translate_into_he_validation_progress` int(11) DEFAULT NULL,
  `translate_into_hi_validation_progress` int(11) DEFAULT NULL,
  `translate_into_hr_validation_progress` int(11) DEFAULT NULL,
  `translate_into_hu_validation_progress` int(11) DEFAULT NULL,
  `translate_into_id_validation_progress` int(11) DEFAULT NULL,
  `translate_into_it_validation_progress` int(11) DEFAULT NULL,
  `translate_into_ja_validation_progress` int(11) DEFAULT NULL,
  `translate_into_ko_validation_progress` int(11) DEFAULT NULL,
  `translate_into_lt_validation_progress` int(11) DEFAULT NULL,
  `translate_into_lv_validation_progress` int(11) DEFAULT NULL,
  `translate_into_nl_validation_progress` int(11) DEFAULT NULL,
  `translate_into_no_validation_progress` int(11) DEFAULT NULL,
  `translate_into_pl_validation_progress` int(11) DEFAULT NULL,
  `translate_into_pt_validation_progress` int(11) DEFAULT NULL,
  `translate_into_pt_br_validation_progress` int(11) DEFAULT NULL,
  `translate_into_pt_pt_validation_progress` int(11) DEFAULT NULL,
  `translate_into_ro_validation_progress` int(11) DEFAULT NULL,
  `translate_into_ru_validation_progress` int(11) DEFAULT NULL,
  `translate_into_sk_validation_progress` int(11) DEFAULT NULL,
  `translate_into_sl_validation_progress` int(11) DEFAULT NULL,
  `translate_into_sr_validation_progress` int(11) DEFAULT NULL,
  `translate_into_sv_validation_progress` int(11) DEFAULT NULL,
  `translate_into_th_validation_progress` int(11) DEFAULT NULL,
  `translate_into_tr_validation_progress` int(11) DEFAULT NULL,
  `translate_into_uk_validation_progress` int(11) DEFAULT NULL,
  `translate_into_vi_validation_progress` int(11) DEFAULT NULL,
  `translate_into_zh_validation_progress` int(11) DEFAULT NULL,
  `translate_into_zh_cn_validation_progress` int(11) DEFAULT NULL,
  `translate_into_zh_tw_validation_progress` int(11) DEFAULT NULL,
  `approval_progress` int(11) DEFAULT NULL,
  `proofing_progress` int(11) DEFAULT NULL,
  `allow_review` tinyint(1) DEFAULT NULL,
  `allow_publish` tinyint(1) DEFAULT NULL,
  `id_approved` tinyint(1) DEFAULT NULL,
  `title_approved` tinyint(1) DEFAULT NULL,
  `id_proofed` tinyint(1) DEFAULT NULL,
  `title_proofed` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration` (
  `version` varchar(255) COLLATE utf8_bin NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  `module` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `node`
--

DROP TABLE IF EXISTS `node`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `node` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `node_has_group`
--

DROP TABLE IF EXISTS `node_has_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `node_has_group` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `visibility` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `node_id` bigint(20) NOT NULL,
  `group_id` bigint(20) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_node_has_group_node1_idx` (`node_id`),
  KEY `fk_node_has_group_group1_idx` (`group_id`),
  CONSTRAINT `fk_node_has_group_group1` FOREIGN KEY (`group_id`) REFERENCES `group` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_node_has_group_node1` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profile` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `version` int(11) NOT NULL DEFAULT '1',
  `account_id` int(11) DEFAULT NULL,
  `cloned_from_id` bigint(20) DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `may_contact` tinyint(1) DEFAULT NULL,
  `professional_title` text COLLATE utf8_bin,
  `lives_in` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `language1` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `language2` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `language3` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `language4` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `language5` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `about_me` text COLLATE utf8_bin,
  `profile_picture_media_id` int(11) DEFAULT NULL,
  `my_links` text COLLATE utf8_bin,
  `profile_qa_state_id` bigint(20) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `node_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `profile_qa_state_id_fk` (`profile_qa_state_id`),
  KEY `fk_profile_account1_idx` (`owner_id`),
  KEY `fk_profile_node1_idx` (`node_id`),
  KEY `fk_profile_account2_idx` (`account_id`),
  KEY `fk_profile_profile1_idx` (`cloned_from_id`),
  KEY `fk_profiles_p3_media1` (`profile_picture_media_id`),
  CONSTRAINT `fk_profile_account1` FOREIGN KEY (`owner_id`) REFERENCES `account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_profile_account2` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_profile_node1` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_profile_profile1` FOREIGN KEY (`cloned_from_id`) REFERENCES `profile` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_profiles_p3_media1` FOREIGN KEY (`profile_picture_media_id`) REFERENCES `p3_media` (`id`),
  CONSTRAINT `profile_qa_state_id_fk` FOREIGN KEY (`profile_qa_state_id`) REFERENCES `profile_qa_state` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `profile_qa_state`
--

DROP TABLE IF EXISTS `profile_qa_state`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profile_qa_state` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `status` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `draft_validation_progress` int(11) DEFAULT NULL,
  `reviewable_validation_progress` int(11) DEFAULT NULL,
  `publishable_validation_progress` int(11) DEFAULT NULL,
  `translate_into_en_validation_progress` int(11) DEFAULT NULL,
  `translate_into_ar_validation_progress` int(11) DEFAULT NULL,
  `translate_into_bg_validation_progress` int(11) DEFAULT NULL,
  `translate_into_ca_validation_progress` int(11) DEFAULT NULL,
  `translate_into_cs_validation_progress` int(11) DEFAULT NULL,
  `translate_into_da_validation_progress` int(11) DEFAULT NULL,
  `translate_into_de_validation_progress` int(11) DEFAULT NULL,
  `translate_into_en_gb_validation_progress` int(11) DEFAULT NULL,
  `translate_into_en_us_validation_progress` int(11) DEFAULT NULL,
  `translate_into_el_validation_progress` int(11) DEFAULT NULL,
  `translate_into_es_validation_progress` int(11) DEFAULT NULL,
  `translate_into_fa_validation_progress` int(11) DEFAULT NULL,
  `translate_into_fi_validation_progress` int(11) DEFAULT NULL,
  `translate_into_fil_validation_progress` int(11) DEFAULT NULL,
  `translate_into_fr_validation_progress` int(11) DEFAULT NULL,
  `translate_into_he_validation_progress` int(11) DEFAULT NULL,
  `translate_into_hi_validation_progress` int(11) DEFAULT NULL,
  `translate_into_hr_validation_progress` int(11) DEFAULT NULL,
  `translate_into_hu_validation_progress` int(11) DEFAULT NULL,
  `translate_into_id_validation_progress` int(11) DEFAULT NULL,
  `translate_into_it_validation_progress` int(11) DEFAULT NULL,
  `translate_into_ja_validation_progress` int(11) DEFAULT NULL,
  `translate_into_ko_validation_progress` int(11) DEFAULT NULL,
  `translate_into_lt_validation_progress` int(11) DEFAULT NULL,
  `translate_into_lv_validation_progress` int(11) DEFAULT NULL,
  `translate_into_nl_validation_progress` int(11) DEFAULT NULL,
  `translate_into_no_validation_progress` int(11) DEFAULT NULL,
  `translate_into_pl_validation_progress` int(11) DEFAULT NULL,
  `translate_into_pt_validation_progress` int(11) DEFAULT NULL,
  `translate_into_pt_br_validation_progress` int(11) DEFAULT NULL,
  `translate_into_pt_pt_validation_progress` int(11) DEFAULT NULL,
  `translate_into_ro_validation_progress` int(11) DEFAULT NULL,
  `translate_into_ru_validation_progress` int(11) DEFAULT NULL,
  `translate_into_sk_validation_progress` int(11) DEFAULT NULL,
  `translate_into_sl_validation_progress` int(11) DEFAULT NULL,
  `translate_into_sr_validation_progress` int(11) DEFAULT NULL,
  `translate_into_sv_validation_progress` int(11) DEFAULT NULL,
  `translate_into_th_validation_progress` int(11) DEFAULT NULL,
  `translate_into_tr_validation_progress` int(11) DEFAULT NULL,
  `translate_into_uk_validation_progress` int(11) DEFAULT NULL,
  `translate_into_vi_validation_progress` int(11) DEFAULT NULL,
  `translate_into_zh_validation_progress` int(11) DEFAULT NULL,
  `translate_into_zh_cn_validation_progress` int(11) DEFAULT NULL,
  `translate_into_zh_tw_validation_progress` int(11) DEFAULT NULL,
  `approval_progress` int(11) DEFAULT NULL,
  `proofing_progress` int(11) DEFAULT NULL,
  `allow_review` tinyint(1) DEFAULT NULL,
  `allow_publish` tinyint(1) DEFAULT NULL,
  `first_name_approved` tinyint(1) DEFAULT NULL,
  `professional_title_approved` tinyint(1) DEFAULT NULL,
  `lives_in_approved` tinyint(1) DEFAULT NULL,
  `about_me_approved` tinyint(1) DEFAULT NULL,
  `profile_picture_media_id_approved` tinyint(1) DEFAULT NULL,
  `my_links_approved` tinyint(1) DEFAULT NULL,
  `first_name_proofed` tinyint(1) DEFAULT NULL,
  `professional_title_proofed` tinyint(1) DEFAULT NULL,
  `lives_in_proofed` tinyint(1) DEFAULT NULL,
  `about_me_proofed` tinyint(1) DEFAULT NULL,
  `profile_picture_media_id_proofed` tinyint(1) DEFAULT NULL,
  `my_links_proofed` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB /*AUTO_INCREMENT omitted*/ DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

