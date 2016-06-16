<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class DbDependentCodeGuy extends \Codeception\Actor
{
    use _generated\DbDependentCodeGuyActions;

    /**
     * Define custom actions here
     */

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

}
