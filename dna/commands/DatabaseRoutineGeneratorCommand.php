<?php

/**
 * Helpers to build SQL triggers used in the application
 *
 * Class DatabaseRoutineGeneratorCommand
 */
class DatabaseRoutineGeneratorCommand extends CConsoleCommand
{

    use RelatedNodesDatabaseRoutineGeneratorTrait;

    /**
     * @var string database connection component
     */
    public $connectionID = "db";

    /**
     * @var
     */
    public $_db;

    /**
     * If we should be verbose
     *
     * @var bool
     */
    private $_verbose = false;

    /**
     * Write a string to standard output if we're verbose
     *
     * @param $string
     */
    protected function d($string)
    {
        if ($this->_verbose) {
            print "\033[37m" . $string . "\033[30m";
        }
    }

    /**
     * Hook that reset-db shell script invokes.
     * Should generate view necessary to generate after the db is reset
     *
     * @param bool $verbose
     */
    public function actionPostResetDb($verbose = false, $dryRun = 0) {

        /*
        $verbose = true;
        $dryRun = 1;
        */

        if (!empty($verbose)) {
            $this->_verbose = true;
        }

        $this->_db =& Yii::app()->{$this->connectionID};

        $this->relatedNodesRoutines(
            (int) $dryRun,
            $echo = function ($msg) {
                $this->d($msg);
            }
        );

    }

    /**
     * @param $model
     * @param $column
     * @return bool
     */
    protected function _checkTableAndColumnExists($table, $column)
    {
        $tables = $this->_db->schema->getTables();
        // The column does not exist if the table does not exist
        return isset($tables[$table]) && (isset($tables[$table]->columns[$column]));
    }

}
