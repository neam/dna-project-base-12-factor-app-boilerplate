<?php

class m151005_105615_auth0_user_table extends EDbMigration
{
    public function up()
    {
        $this->execute(
            "ALTER TABLE `account`
DROP COLUMN `auth0_last_verified_token_expires`,
DROP COLUMN `auth0_last_verified_token`,
DROP COLUMN `auth0_last_authentication_at`,
DROP COLUMN `auth0_user_id`,
DROP INDEX `user_email` ;"
        );
        $this->execute(
            "CREATE TABLE IF NOT EXISTS `auth0_user` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `account_id` INT(11) NULL DEFAULT NULL,
  `auth0_app` VARCHAR(255) NULL DEFAULT NULL,
  `auth0_user_id` VARCHAR(255) NULL DEFAULT NULL,
  `auth0_last_authentication_at` VARCHAR(255) NULL DEFAULT NULL,
  `auth0_last_verified_token` TEXT NULL DEFAULT NULL,
  `auth0_last_verified_token_expires` INT(11) NULL DEFAULT NULL,
  `created` DATETIME NULL DEFAULT NULL,
  `modified` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_auth0_user_account1_idx` (`account_id` ASC),
  CONSTRAINT `fk_auth0_user_account1`
    FOREIGN KEY (`account_id`)
    REFERENCES `account` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;"
        );

    }

    public function down()
    {
        echo "m151005_105615_auth0_user_table does not support migration down.\n";
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