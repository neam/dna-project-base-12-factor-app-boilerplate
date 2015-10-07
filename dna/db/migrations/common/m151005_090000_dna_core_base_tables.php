<?php

class m151005_090000_dna_core_base_tables extends EDbMigration
{
    public function up()
    {
        $this->execute(
            "CREATE TABLE `account` (
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
  `auth0_user_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `auth0_last_authentication_at` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `auth0_last_verified_token` text COLLATE utf8_bin,
  `auth0_last_verified_token_expires` int(11) DEFAULT NULL,
  `node_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_username` (`username`),
  UNIQUE KEY `user_email` (`email`),
  KEY `fk_account_node1_idx` (`node_id`),
  CONSTRAINT `fk_account_node1` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;"
        );
        $this->execute(
            "CREATE TABLE `changeset` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;"
        );
        $this->execute(
            "CREATE TABLE `edge` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;"
        );
        $this->execute(
            "CREATE TABLE `file` (
  `id` int(11) NOT NULL,
  `path` text COLLATE utf8_bin,
  `node_id` bigint(20) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_file_registry_entry_node1_idx` (`node_id`),
  CONSTRAINT `fk_file_registry_entry_node1` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;"
        );
        $this->execute(
            "CREATE TABLE `file_instance` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) DEFAULT NULL,
  `storage_component_ref` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_file_file_registry_entry1_idx` (`file_id`),
  CONSTRAINT `fk_file_instance_file1` FOREIGN KEY (`file_id`) REFERENCES `file` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;"
        );
        $this->execute(
            "CREATE TABLE `group` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;"
        );
        $this->execute(
            "CREATE TABLE `group_has_account` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;"
        );
        $this->execute(
            "CREATE TABLE `group_qa_state` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;"
        );
        $this->execute(
            "CREATE TABLE `node` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;"
        );
        $this->execute(
            "CREATE TABLE `node_has_group` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;"
        );
        $this->execute(
            "CREATE TABLE `profile` (
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
  CONSTRAINT `fk_profiles_p3_media1` FOREIGN KEY (`profile_picture_media_id`) REFERENCES `p3_media` (`id`),
  CONSTRAINT `fk_profile_account1` FOREIGN KEY (`owner_id`) REFERENCES `account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_profile_account2` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_profile_node1` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_profile_profile1` FOREIGN KEY (`cloned_from_id`) REFERENCES `profile` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `profile_qa_state_id_fk` FOREIGN KEY (`profile_qa_state_id`) REFERENCES `profile_qa_state` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;"
        );
        $this->execute(
            "CREATE TABLE `profile_qa_state` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;"
        );
        $this->execute(
            "CREATE TABLE `role` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;"
        );

    }

    public function down()
    {
        echo "m151006_224326_dna_core_base_tables does not support migration down.\n";
        return false;
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}