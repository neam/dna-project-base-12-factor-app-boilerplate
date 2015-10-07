<?php

/**
 * To build SQL views from cli
 *
 * Class DatabaseViewGeneratorCommand
 */
class DatabaseViewGeneratorCommand extends CConsoleCommand
{

    use GraphRelationsItemViewAndTableGeneratorTrait;

    /**
     * @var string database connection component
     */
    public $connectionID = "db";

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
    public function actionPostResetDb($verbose = false)
    {

        if (!empty($verbose)) {
            $this->_verbose = true;
        }

        // TODO: Refactor to use DatabaseView
        /*
        $this->actionItem(
            $dryRun = 0,
            $echo = function ($msg) {
                $this->d($msg);
            }
        );
        $this->actionItemTable(
            $dryRun = 0,
            $echo = function ($msg) {
                $this->d($msg);
            }
        );
        */

    }

}
