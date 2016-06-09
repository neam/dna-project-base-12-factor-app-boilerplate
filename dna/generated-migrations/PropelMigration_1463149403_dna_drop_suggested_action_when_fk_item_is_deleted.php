<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1463149403.
 * Generated on 2016-05-13 14:23:23 by root
 */
class PropelMigration_1463149403_dna_drop_suggested_action_when_fk_item_is_deleted
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
  'default' => '
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE=\'TRADITIONAL,ALLOW_INVALID_DATES\';

ALTER TABLE `suggested_action` 
DROP FOREIGN KEY `fk_suggested_action_clerk_fiscal_year1`,
DROP FOREIGN KEY `fk_suggested_action_clerk_tax_entity1`,
DROP FOREIGN KEY `fk_suggested_action_clerk_transaction_row1`;

ALTER TABLE `suggested_action` 
DROP FOREIGN KEY `fk_suggested_action_clerk_account1`,
DROP FOREIGN KEY `fk_suggested_action_input_result1`;

ALTER TABLE `suggested_action` ADD CONSTRAINT `fk_suggested_action_clerk_fiscal_year1`
  FOREIGN KEY (`relevant_clerk_fiscal_year_id`)
  REFERENCES `clerk_fiscal_year` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_suggested_action_clerk_tax_entity1`
  FOREIGN KEY (`relevant_clerk_tax_entity_id`)
  REFERENCES `clerk_tax_entity` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_suggested_action_clerk_account1`
  FOREIGN KEY (`relevant_clerk_account_id`)
  REFERENCES `clerk_account` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_suggested_action_input_result1`
  FOREIGN KEY (`input_result_id`)
  REFERENCES `input_result` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_suggested_action_clerk_transaction_row1`
  FOREIGN KEY (`clerk_transaction_row_id`)
  REFERENCES `clerk_transaction_row` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
',
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