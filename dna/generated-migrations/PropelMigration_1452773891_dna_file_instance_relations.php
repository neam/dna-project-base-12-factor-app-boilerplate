<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1452773891.
 * Generated on 2016-01-14 12:18:11 by root
 */
class PropelMigration_1452773891_dna_file_instance_relations
{
    public $comment = '';

    public function preUp($manager)
    {
        // add the pre-migration code here
    }

    public function postUp($manager)
    {
        // add the post-migration code here
    }

    public function preDown($manager)
    {
        // add the pre-migration code here
    }

    public function postDown($manager)
    {
        // add the post-migration code here
    }

    /**
     * Get the SQL statements for the Up migration
     *
     * @return array list of the SQL strings to execute for the Up migration
     *               the keys being the datasources
     */
    public function getUpSQL()
    {
        return array (
  'default' => 'SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE=\'TRADITIONAL,ALLOW_INVALID_DATES\';

ALTER TABLE `file`
ADD COLUMN `local_file_instance_id` BIGINT(20) NULL DEFAULT NULL AFTER `original_filename`,
ADD COLUMN `public_files_s3_file_instance_id` BIGINT(20) NULL DEFAULT NULL AFTER `local_file_instance_id`,
ADD COLUMN `filestack_file_instance_id` BIGINT(20) NULL DEFAULT NULL AFTER `public_files_s3_file_instance_id`,
ADD COLUMN `filestack_pending_file_instance_id` BIGINT(20) NULL DEFAULT NULL AFTER `filestack_file_instance_id`,
ADD INDEX `fk_file_file_instance1_idx` (`local_file_instance_id` ASC),
ADD INDEX `fk_file_file_instance2_idx` (`public_files_s3_file_instance_id` ASC),
ADD INDEX `fk_file_file_instance3_idx` (`filestack_file_instance_id` ASC),
ADD INDEX `fk_file_file_instance4_idx` (`filestack_pending_file_instance_id` ASC);

ALTER TABLE `file`
ADD CONSTRAINT `fk_file_file_instance1`
  FOREIGN KEY (`local_file_instance_id`)
  REFERENCES `file_instance` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_file_file_instance2`
  FOREIGN KEY (`public_files_s3_file_instance_id`)
  REFERENCES `file_instance` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_file_file_instance3`
  FOREIGN KEY (`filestack_file_instance_id`)
  REFERENCES `file_instance` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_file_file_instance4`
  FOREIGN KEY (`filestack_pending_file_instance_id`)
  REFERENCES `file_instance` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;',
);
    }

    /**
     * Get the SQL statements for the Down migration
     *
     * @return array list of the SQL strings to execute for the Down migration
     *               the keys being the datasources
     */
    public function getDownSQL()
    {
        return array (
  'default' => '',
);
    }

}