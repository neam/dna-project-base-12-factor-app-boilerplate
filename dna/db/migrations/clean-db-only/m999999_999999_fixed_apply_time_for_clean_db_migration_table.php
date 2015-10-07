<?php

class m999999_999999_fixed_apply_time_for_clean_db_migration_table extends EDbMigration
{
	public function up()
	{
        $this->getDbConnection()->createCommand(
            "UPDATE `migration` SET apply_time = 1400000000;"
        )->query();
	}

	public function down()
	{
		echo "m140830_223340_fixed_apply_time_for_clean_db_migration_table does not support migration down.\n";
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