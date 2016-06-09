<?php
use Codeception\Util\Stub;

class EdgeOrderingTest extends \Codeception\TestCase\Test
{
    /**
     * @var \CodeGuy
     */
    protected $codeGuy;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public static function assertEquals(
        $expected,
        $actual,
        $message = '',
        $delta = 0,
        $maxDepth = 10,
        $canonicalize = false,
        $ignoreCase = false
    )
    {
        $trace = debug_backtrace();
        $message = "assertEquals($expected, $actual) on line {$trace[0]["line"]}:";
        parent::assertEquals($expected, $actual, $message, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }

    /**
     * @group todo
     */
    public function testEdgeOrder()
    {
    }
}
