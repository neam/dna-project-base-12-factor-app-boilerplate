<?php

use Propel\Runtime\Propel;
use Propel\Runtime\Exception\PropelException;

class Suggestions
{
    // use FooSuggestionsTrait;

    const CREATE = 'create';
    const UPDATE = 'update';
    const DELETE = 'delete';
    const ANY = 'any';

    static public $statusLog = [];

    static public function getAvailableAlgorithms()
    {

        $algorithms = array_merge_recursive(
            static::getAvailableAlgorithms_Foo(),
            [
                "foo" => [
                    "affected-item-types" => [
                        "Foo" => [
                            static::UPDATE,
                        ],
                    ],
                ],
            ]
        );

        // TODO: Filter
        return $algorithms;

    }

    /**
     * Prepares an algorithm-array that contains "data", "ref" and "config"
     *
     * @param $postedAlgorithms
     * @param $requiresRollbackSupport
     * @return array
     * @throws Exception
     */
    static public function preparePostedAlgorithmData($postedAlgorithms, $requiresRollbackSupport)
    {

        $availableAlgorithms = static::getAvailableAlgorithms();

        $algorithms = [];

        foreach ($postedAlgorithms as $postedAlgorithm) {

            $algorithm = [];
            $ref = null;

            // ref and data
            if (is_object($postedAlgorithm)) {
                foreach ($postedAlgorithm as $key => $value) {
                    $ref = $key;
                    $algorithm["data"] = $value;
                    break;
                }
            } else {
                $ref = $postedAlgorithm;
                $algorithm["data"] = null;
            }
            $algorithm["ref"] = $ref;

            // config
            if (!isset($availableAlgorithms[$ref])) {
                throw new Exception("Algorithm not available: $ref");
            }
            $algorithm["config"] = $availableAlgorithms[$ref];

            // double check rollback support (if nothing specified assume rollback supported - this flag is for algorithms that affect other things than database contents)
            $rollbackSupported = !isset($algorithm["config"]["rollback-supported"]) ? true : $algorithm["config"]["rollback-supported"];
            if ($requiresRollbackSupport && !$rollbackSupported) {
                throw new Exception("Algorithm does not have rollback support: $ref");
            }

            $algorithms[$ref] = $algorithm;

        }

        return $algorithms;

    }

    /**
     * @param $algorithms An array with either string names of algorithms or a key value pair where the key is the algorithm and the value is the data that the algorithm requires
     * @param null $byAction
     * @return array
     */
    static public function getItemTypesAffectedByAlgorithms($algorithms, $byAction = null)
    {

        foreach ($algorithms as $ref => $algorithm) {

            // config
            $config = $algorithm["config"];

            $itemTypesAffectedByAlgorithm = [];

            foreach ($config["affected-item-types"] as $affectedItemType => $actions) {
                if ($byAction === static::ANY || in_array($byAction, $actions)) {
                    $itemTypesAffectedByAlgorithm[] = $affectedItemType;
                }
            }

        }

        return array_unique($itemTypesAffectedByAlgorithm);

    }

    static public function getPdoForSuggestions()
    {

        // PDO
        $pdo = Propel::getWriteConnection('default');

        return $pdo;

    }

    static public function run($algorithms, \Propel\Runtime\Connection\ConnectionInterface &$pdo)
    {

        $return = [];

        // get initial metadata
        $return["initial_metadata"] = [];

        // set autocommit to 0 to prevent saving of data within transaction
        $pdo->exec("SET SESSION autocommit = 0");

        // perform suggested actions - retry one time per second 10 times in case of deadlock
        $retry = 0;
        $done = false;
        while (!$done and $retry < 10) {

            // start transaction
            $pdo->beginTransaction();

            try {

                // perform suggested actions
                foreach ($algorithms as $ref => $algorithm) {
                    static::$ref($algorithm["data"]);
                }

                $done = true;

            } catch (PDOException $e) {

                // If deadlock - retry
                if (strpos(
                        $e->getMessage(),
                        "Deadlock found when trying to get lock; try restarting transaction"
                    ) !== false
                ) {

                    // rollback transaction
                    static::rollbackTransactionAndReclaimAutoIncrement($algorithms, $pdo);
                    sleep(1);
                    $retry++;

                } else {
                    throw $e;
                }

            }

        }

        // get new metadata
        $return["new_metadata"] = [];

        // return metadata and transaction
        return $return;

    }

    static public $autoIncrementValues = [];

    static public function rollbackTransactionAndReclaimAutoIncrement(
        $algorithms,
        \Propel\Runtime\Connection\ConnectionInterface &$pdo
    ) {

        $pdo->rollback();
        //$pdo = Propel::getWriteConnection('default');

        // reclaim auto-increment - http://stackoverflow.com/a/9312793/682317
        $itemTypesAffectedByAlgorithms = static::getItemTypesAffectedByAlgorithms($algorithms, static::CREATE);
        foreach ($itemTypesAffectedByAlgorithms as $itemType) {
            $tableMapClass = '\\propel\\models\\Map\\' . $itemType . "TableMap";
            /** @var \Propel\Runtime\Map\TableMap $tableMap */
            $tableMap = $tableMapClass::getTableMap();
            $table = $tableMap->getName();
            // TODO: Find a better way to determine if the "table" indeed is a view
            if (strpos($table, "denormalized_") !== false) {
                continue;
            }
            $autoIncrementValue = 1;
            $pdo->exec("ALTER TABLE $table auto_increment = $autoIncrementValue");
        }

    }

    /**
     * @param $itemType
     * @return Propel\Runtime\ActiveRecord\ActiveRecordInterface
     * @throws HttpException
     */
    static public function getModelOfItemType($itemType)
    {

        $modelName = '\\propel\\models\\' . $itemType;
        if (!class_exists($modelName)) {
            throw new HttpException(500, 'Invalid configuration');
        }
        $model = new $modelName();
        return $model;

    }

    static public function status($message)
    {
        static::$statusLog[] = $message;
    }

}

class MultipleCandidatesException extends Exception
{
}