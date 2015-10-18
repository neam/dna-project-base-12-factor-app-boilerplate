<?php

class m151005_090001_dna_core_default_restricted_access_metadata extends EDbMigration
{
    public function up()
    {
        $this->execute(
            "INSERT INTO `group` (`id`,
`title`,
`ref`,
`created`,
`modified`) VALUES
(1,'root',NULL,NULL,NULL);
"
        );
        $this->execute("INSERT INTO `role` (`id`, `title`) VALUES (1,'Developer');");
        $this->execute("INSERT INTO `role` (`id`, `title`) VALUES (2,'SuperAdministrator');");
        $this->execute("INSERT INTO `role` (`id`, `title`) VALUES (3,'Authenticated');");
        $this->execute("INSERT INTO `role` (`id`, `title`) VALUES (4,'Guest');");
        $this->execute("INSERT INTO `role` (`id`, `title`) VALUES (5,'GroupAdministrator');");
        $this->execute("INSERT INTO `role` (`id`, `title`) VALUES (6,'GroupPublisher');");
        $this->execute("INSERT INTO `role` (`id`, `title`) VALUES (7,'GroupEditor');");
        $this->execute("INSERT INTO `role` (`id`, `title`) VALUES (8,'GroupApprover');");
        $this->execute("INSERT INTO `role` (`id`, `title`) VALUES (9,'GroupModerator');");
        $this->execute("INSERT INTO `role` (`id`, `title`) VALUES (10,'GroupContributor');");
        $this->execute("INSERT INTO `role` (`id`, `title`) VALUES (11,'GroupReviewer');");
        $this->execute("INSERT INTO `role` (`id`, `title`) VALUES (12,'GroupTranslator');");
        $this->execute("INSERT INTO `role` (`id`, `title`) VALUES (13,'GroupMember');");
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