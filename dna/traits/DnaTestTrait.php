<?php

trait DnaTestTrait
{

    public static function assertEqualsWithLineTrace(
        $expected,
        $actual,
        $message = '',
        $delta = 0,
        $maxDepth = 10,
        $canonicalize = false,
        $ignoreCase = false
    ) {
        $trace = debug_backtrace();
        $message = "assertEquals($expected, $actual) on line {$trace[0]["line"]}:";
        parent::assertEquals($expected, $actual, $message, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }

    protected function exceptionAsString(Exception $e)
    {
        return "{" . get_class($e) . "} " . $e->getMessage()
        . " in " . $e->getFile() . ":" . $e->getLine();
        //. "\nStack trace: " . $e->getTraceAsString();
    }

    protected function codeceptDebugException(Exception $e)
    {
        codecept_debug("Exception: " . $this->exceptionAsString($e));
        if (!empty($e->getPrevious())) {
            codecept_debug(
                "Exception P1: " . $this->exceptionAsString($e)
            );
            if (!empty($e->getPrevious()->getPrevious())) {
                codecept_debug(
                    "Exception P2: " . $this->exceptionAsString($e)
                );
            }
        }
    }

    protected function tableNodeToArray(\Behat\Gherkin\Node\TableNode $tableNode)
    {

        $array = [];
        $rows = $tableNode->getRows();
        $keys = array_shift($rows);
        foreach ($rows as $k => $row) {
            $arrayEntry = [];
            foreach ($row as $column => $string) {
                $arrayEntry[$keys[$column]] = $string;
            }
            $array[] = $arrayEntry;
        }
        return $array;

    }

}