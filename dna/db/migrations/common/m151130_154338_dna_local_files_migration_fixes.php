<?php

class m151130_154338_dna_local_files_migration_fixes extends EDbMigration
{
    public function up()
    {
        $this->execute(
            "UPDATE file_instance fi INNER JOIN `file` f ON fi.file_id = f.id SET fi.uri = f.path WHERE fi.uri IS NULL;"
        );
    }

    public function down()
    {
        echo "m151130_154338_dna_local_files_migration_fixes does not support migration down.\n";
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