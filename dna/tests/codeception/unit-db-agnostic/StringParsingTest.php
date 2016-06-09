<?php
use Codeception\Util\Stub;

class StringParsingTest extends \Codeception\TestCase\Test
{

    use DnaTestTrait;

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

    public static function standardizeDateFormatProvider()
    {
        return [
            ['07-29-13', 'm-d-y', '2013-07-29', 'Y-m-d'],
            ['07-29-13 00:00:00', 'm-d-y H:i:s', '2013-07-29', 'Y-m-d'],
        ];
    }

    /**
     * @group coverage:full
     * @dataProvider standardizeDateFormatProvider
     */
    public function testStandardizeDateFormat(
        $rawDateString,
        $dateFormat,
        $expectedDateInStandardFormat,
        $standardDateFormat
    ) {

        $dateTime = DateTime::createFromFormat($dateFormat, $rawDateString);
        $this->assertNotEquals(
            false,
            $dateTime,
            'verifying that date ' . $rawDateString . ' can be parsed using the format ' . $dateFormat
        );
        codecept_debug(compact("dateTime", "dateFormat", "rawDateString"));
        $this->assertEquals(
            $expectedDateInStandardFormat,
            $dateTime->format($standardDateFormat),
            'verifying that date formatting behaves as expected'
        );

    }

}
