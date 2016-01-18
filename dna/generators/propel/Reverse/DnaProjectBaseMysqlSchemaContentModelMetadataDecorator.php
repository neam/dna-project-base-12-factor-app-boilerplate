<?php

namespace generators\propel\Reverse;

use Propel\Generator\Model\Behavior;
use Propel\Generator\Model\Column;
use Propel\Generator\Model\Database;
use Propel\Generator\Model\ForeignKey;
use Propel\Generator\Model\Index;
use Propel\Generator\Model\Table;
use Propel\Generator\Model\Unique;
use Propel\Generator\Model\PropelTypes;
use Propel\Generator\Model\ColumnDefaultValue;
use Propel\Generator\Behavior\Timestampable\TimestampableBehavior;
use Propel\Generator\Behavior\Versionable\VersionableBehavior;

class DnaProjectBaseMysqlSchemaContentModelMetadataDecorator extends \Propel\Generator\Reverse\MysqlSchemaParser
{

    /**
     * Overridden to ensure that the resulting schema.xml will include all available information
     * necessary to build propel models that utilize the full content model and it's metadata
     *
     * @param  Database $database
     * @param  Table[] $additionalTables
     * @return int
     */
    public function parse(Database $database, array $additionalTables = [])
    {
        $tableCount = parent::parse($database, $additionalTables);

        // Only decorate the schema if requested
        if (getenv('DECORATE_PROPEL_SCHEMA') != 1) {
            return $tableCount;
        }

        // TODO: Make configurable
        $modelNamespace = 'propel\\models';
        $contentModelMetadataJsonPath = \Paths::dna() . DIRECTORY_SEPARATOR . 'content-model-metadata.json';
        $defaultCreatedAtColumn = 'created';
        $defaultUpdatedAtColumn = 'modified';

        $database->setNamespace(''); // The actual namespace is set at table level
        $database->setIdentifierQuoting(true);

        // Load content model metadata
        $contentModelMetadata = new \__PROJECT__\dna\config\ContentModelMetadata(
            $contentModelMetadataJsonPath
        );

        $itemTypes = $contentModelMetadata->getItemTypes();

        // Now populate behaviors
        foreach ($database->getTables() as $table) {

            if (!isset($itemTypes[$table->getPhpName()])) {
                continue;
            }

            // The content model metadata for the item type
            $itemType = $itemTypes[$table->getPhpName()];

            // Auto-detect configuration for timestampable behavior
            if ($itemType->is_timestamped) {
                $behavior = new TimestampableBehavior();
                $behavior->setName("timestampable");
                $create_column = $defaultCreatedAtColumn;
                $update_column = $defaultUpdatedAtColumn;
                foreach (['created_at', 'create_at', 'created', 'createdAt'] as $candidateColumn) {
                    if ($table->hasColumn($candidateColumn)) {
                        $create_column = $candidateColumn;
                        break;
                    }
                }
                foreach (['updated_at', 'modified_at', 'modified', 'modifiedAt'] as $candidateColumn) {
                    if ($table->hasColumn($candidateColumn)) {
                        $update_column = $candidateColumn;
                        break;
                    }
                }
                $disable_created_at = 'false';
                $disable_updated_at = 'false';
                $behavior->setParameters(
                    compact('create_column', 'update_column', 'disable_created_at', 'disable_updated_at')
                );
                $table->addBehavior($behavior);
            }

            if ($itemType->is_versioned) {
                $behavior = new VersionableBehavior();
                $behavior->setName("versionable");
                $table->addBehavior($behavior);
            }

        }

        // Allow behaviors to modify the database before writing the XML
        $database->doFinalInitialization();

        // Set namespace for all tables
        foreach ($database->getTables() as $table) {
            $table->setNamespace($modelNamespace);
        }

        return count($database->getTables());
    }

    /**
     * Overridden to include views in reverse-engineered schema.xml
     * Mostly copy-pasted from parent class, except as mentioned in comments below
     */
    protected function parseTables(Database $database, $filterTable = null)
    {
        $sql = 'SHOW FULL TABLES';

        if ($filterTable) {
            if ($schema = $filterTable->getSchema()) {
                $sql .= ' FROM ' . $database->getPlatform()->doQuoting($schema);
            }
            $sql .= sprintf(" LIKE '%s'", $filterTable->getCommonName());
        } else {
            if ($schema = $database->getSchema()) {
                $sql .= ' FROM ' . $database->getPlatform()->doQuoting($schema);
            }
        }

        $dataFetcher = $this->dbh->query($sql);

        // First load the tables (important that this happen before filling out details of tables)
        $tables = [];
        foreach ($dataFetcher as $row) {
            $name = $row[0];
            $type = $row[1];

            // Line changed to include views in reverse-engineered schema.xml (https://github.com/propelorm/Propel/issues/458)
            if ($name == $this->getMigrationTable() || !in_array($type, array("BASE TABLE", "VIEW"))) {
                continue;
            }

            $table = new Table($name);
            $table->setIdMethod($database->getDefaultIdMethod());
            if ($filterTable && $filterTable->getSchema()) {
                $table->setSchema($filterTable->getSchema());
            }
            $database->addTable($table);
            $tables[] = $table;
        }
    }

}