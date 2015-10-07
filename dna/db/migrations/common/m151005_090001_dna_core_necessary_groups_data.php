<?php

class m151005_090001_dna_core_necessary_groups_data extends EDbMigration
{
	public function up()
	{
        $this->execute("INSERT INTO `group` (`id`,
`title`,
`ref`,
`created`,
`modified`) VALUES
(1,'root',NULL,NULL,NULL);
");
	}

	public function down()
	{
		echo "m151006_224327_dna_core_necessary_groups_data does not support migration down.\n";
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