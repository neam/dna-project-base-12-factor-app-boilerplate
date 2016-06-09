<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1452846982.
 * Generated on 2016-01-15 08:36:22 by root
 */
class PropelMigration_1452846982_dna_file_data_migration
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
  'default' => 'UPDATE `file` parent_query_file_table
SET
    local_file_instance_id = (SELECT
            id
        FROM
            file_instance
        WHERE
            file_instance.file_id = parent_query_file_table.id
                AND storage_component_ref = \'local\' ORDER BY id DESC LIMIT 1),
    filestack_file_instance_id = (SELECT
            id
        FROM
            file_instance
        WHERE
            file_instance.file_id = parent_query_file_table.id
                AND storage_component_ref IN (\'filepicker\' , \'filestack\') ORDER BY id DESC LIMIT 1)
WHERE
    1;',
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