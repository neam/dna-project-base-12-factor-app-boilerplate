<?php

class m151121_112015_dna_file_attributes extends EDbMigration
{
    public function up()
    {
        $this->execute(
            "ALTER TABLE `file`
ADD COLUMN `size` INT(11) NULL DEFAULT NULL AFTER `path`,
ADD COLUMN `mimetype` VARCHAR(255) NULL DEFAULT NULL AFTER `size`,
ADD COLUMN `filename` VARCHAR(255) NULL DEFAULT NULL AFTER `mimetype`,
ADD COLUMN `original_filename` VARCHAR(255) NULL DEFAULT NULL AFTER `filename`;"
        );
        $this->execute(
            "ALTER TABLE `file_instance`
ADD COLUMN `uri` VARCHAR(255) NULL DEFAULT NULL AFTER `storage_component_ref`,
ADD COLUMN `data_json` TEXT NULL DEFAULT NULL AFTER `uri`;"
        );
    }

    public function down()
    {
        echo "m151121_112015_dna_file_attributes does not support migration down.\n";
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