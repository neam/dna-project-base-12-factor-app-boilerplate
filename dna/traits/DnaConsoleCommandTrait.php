<?php

trait DnaConsoleCommandTrait
{

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
    public function d($string)
    {
        if ($this->_verbose) {
            print "\033[37m" . $string . "\033[30m";
        }
    }

    protected function status($msg)
    {
        echo round(Yii::getLogger()->getExecutionTime(), 2) . "s - $msg\n";
    }

    protected function doneExecutionStatus($pageSize, $currentPage, $records)
    {
        $this->status("Done execution with pageSize $pageSize, current page $currentPage. Record count: " . count($records) . ". Memory usage: " . round(memory_get_usage() / 1024 / 1024, 2) . " MiB");
        echo "\n";
    }

    protected function getCriteria($modelRef, $pageSize, $currentPage)
    {

        $criteria = new CDbCriteria;

        if ($currentPage > 1) {
            $pages = new CPagination($modelRef::model()->count($criteria));
            $pages->pageSize = $pageSize;
            $pages->setCurrentPage($currentPage - 1); // -1 so that the argument can be the same as in webview
            $pages->applyLimit($criteria);
        } else {
            $criteria->limit = $pageSize;
        }

        return $criteria;
    }

    protected function exceptionStatus(Exception $e, $throw = false)
    {
        $this->status("Exception: " . $e->getMessage()
            . "\n File: " . $e->getFile()
            . "\n Line: " . $e->getLine());
        if ($throw) throw $e;
    }

}