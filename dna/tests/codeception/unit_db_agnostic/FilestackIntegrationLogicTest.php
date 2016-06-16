<?php
use Codeception\Util\Stub;

class FilestackIntegrationLogicTest extends \Codeception\TestCase\Test
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

    public static function filestackUrlHandleProvider()
    {
        return [
            ['https://www.filepicker.io/api/file/rROOlm9qABMWvbaZrfu5', 'rROOlm9qABMWvbaZrfu5'],
            ['https://www.filepicker.io/api/file/in8hKtocK0tHwM03XhS4/convert?crop=0,131,640,202', 'in8hKtocK0tHwM03XhS4'],
            ['https://www.filestackapi.com/api/file/rROOlm9qABMWvbaZrfu5', 'rROOlm9qABMWvbaZrfu5'],
            ['https://www.filestackapi.com/api/file/in8hKtocK0tHwM03XhS4/convert?crop=0,131,640,202', 'in8hKtocK0tHwM03XhS4'],
            ['https://cdn.filestackcontent.com/VgvFVdvvTkml0WXPIoGn', 'VgvFVdvvTkml0WXPIoGn'],
        ];
    }

    /**
     * @group coverage:full
     * @dataProvider filestackUrlHandleProvider
     */
    public function testExtractHandleFromFilestackUrl($filestackUrl, $expectedHandle)
    {

        $handle = File::extractHandleFromFilestackUrl($filestackUrl);
        $this->assertEquals($expectedHandle, $handle, 'filestack url handle extraction behaves as expected');

    }
}
