<?php

abstract class DatabaseView
{

    public static $dbViewClassName;
    public static $dbViewName;

    abstract public function sql();

    /**
     * @var
     */
    public $_db;

    public function initDb($connectionID = "db")
    {
        $this->_db =& Yii::app()->$connectionID;
    }

    public function viewSql($dbViewName, $sql)
    {
        $dbViewSql = "CREATE OR REPLACE VIEW $dbViewName AS $sql";
        return $dbViewSql;
    }

    public function createView($dbViewSql)
    {
        $this->_db->getPdoInstance()->exec($dbViewSql);
    }

    /**
     * @param $table
     * @param $column
     * @return bool
     */
    protected function _checkTableAndColumnExists($table, $column)
    {
        $tables = $this->_db->schema->getTables();
        // The column does not exist if the table does not exist
        return isset($tables[$table]) && (isset($tables[$table]->columns[$column]));
    }

    /**
     * Factory create view method for cli scripts
     *
     * @param int $dryRun
     * @param int $verbose
     */
    public function cliFactoryCreateView($connectionID, $dryRun = 0, $echo)
    {

        $dbViewName = static::$dbViewName;

        $this->initDb($connectionID);

        $echo("Connecting to '" . $this->_db->connectionString . "'\n");

        $sql = $this->sql();

        $selectResult = $this->_db->createCommand($sql . " LIMIT 2")->queryAll();

        $echo(print_r(compact("selectResult"), true) . "\n");

        $dbViewSql = $this->viewSql($dbViewName, $sql);

        $echo("\n");
        $echo($dbViewSql);
        $echo("\n");

        if (!empty($dryRun)) {
            $echo("\n");
            $echo("Not actually creating view, since --dryRun=1 was specified\n");
            $echo("\n");
            return;
        }

        $this->createView($dbViewSql);

        $selectViewResult = $this->_db->createCommand("SELECT * FROM $dbViewName LIMIT 2")->queryAll();

        $echo(print_r(compact("selectViewResult"), true) . "\n");

    }

}