<?php
use Codeception\Util\Stub;
use neam\URLify;

class StringTransliterationTest extends \Codeception\TestCase\Test
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

    public static function transliterationDataProvider()
    {
        $propel2TestData = [
            ['foo', 'foo'],
            ['fôo', 'foo'],
            ['€', 'EUR'],
            [
                'CŠŒŽšœžŸµÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝßàáâãäåæçèéêëìíîïñòóôõöùúûüýÿ',
                'CSOEZsoezYuAAAAAAAECEEEEIIIINOOOOOUUUUYssaaaaaaaeceeeeiiiinooooouuuuyy'
            ],
            ['ø', 'o'],
            ['Ø', 'O'],
            ['¥Ðð', 'YDd'],
        ];

        // Adapter from https://raw.githubusercontent.com/lingtalfi/Bat/master/btests/StringTool/removeAccents/stringTool.removeAccents.test.php
        $batStringToolTestData = array_combine(
            [
                // easy
                '',
                'a',
                'après',
                'dédé fait la fête ?',
                // hard
                'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
                'ŻŹĆŃĄŚŁĘÓżźćńąśłęó',
                'qqqqŻŹĆŃĄŚŁĘÓżźćńąśłęóqqq',
                'ŠŽšžŸÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝàáâãäåçèéêëìíîïðñòóôõöøùúûüýÿ',
                'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝàáâãäåçèéêëìíîïñòóôõöøùúûüýÿ',
                'ĀāĂăĄąĆćĈĉĊċČčĎďĐđĒēĔĕĖėĘęĚěĜĝĞğĠġĢģĤĥĦħĨĩĪīĬĭĮįİĴĵĶķ',
                'ĹĺĻļĽľĿŀŁłŃńŅņŇňŉŌōŎŏŐőŔŕŖŗŘřŚśŜŝŞşŠšŢţŤťŦŧŨũŪūŬŭŮůŰűŲųŴŵŶŷŸŹźŻżŽž',
                'ſƒƠơƯưǍǎǏǐǑǒǓǔǕǖǗǘǙǚǛǜǺǻǾǿ',
                'Ǽǽ',
            ],
            [
                // easy
                '',
                'a',
                'apres',
                'dede fait la fete ?',
                // hard
                'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY',
                'ZZCNASLEOzzcnasleo',
                'qqqqZZCNASLEOzzcnasleoqqq',
                //'SZszYAAAAAACEEEEIIIIDNOOOOOOUUUUYaaaaaaceeeeiiiionoooooouuuuyy', // original
                'SZszYAAAAAACEEEEIIIIDNOOOOOOUUUUYaaaaaaceeeeiiiidnoooooouuuuyy',
                'AAAAAACEEEEIIIIDNOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy',
                //'AaAaAaCcCcCcCcDdDdEeEeEeEeEeGgGgGgGgHhHhIiIiIiIiIJjKk', // original
                'AaAaAaCcCcCcCcDdDjdjEeEeEeEeEeGgGgGgGgHhHhIiiiIiIiIJjkk',
                'LlLlLlLlLlNnNnNnnOoOoOoRrRrRrSsSsSsSsTtTtTtUuUuUuUuUuUuWwYyYZzZzZz',
                'ifOoUuAaIiOoUuUuUuUuUuAaOo',
                //'Aa', // original
                'Aeae',
            ]
        );
        $batStringToolTestDataFormatted = [];
        foreach ($batStringToolTestData as $k => $v) {
            $batStringToolTestDataFormatted[] = [$k, $v];
        }

        // https://github.com/infralabs/DiacriticsRemovePHP/blob/master/test_SpecialCharacters_to_Latin.php
        $removeDiaCriticsTestData = [
            //Latin-1 Supplement
            [
                "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúûüýþÿ",
                "AAAAAAAECEEEEIIIIDNOOOOO×OUUUUYTHssaaaaaaaeceeeeiiiidnooooo÷ouuuuythy"
            ],
            //Latin Extended-A
            [
                "ĀāĂăĄąĆćĈĉĊċČčĎďĐđĒēĔĕĖėĘęĚěĜĝĞğĠġĢģĤĥĦħĨĩĪīĬĭĮįİıĲĳĴĵĶķĸĹĺĻļĽľĿŀŁłŃńŅņŇňŉŊŋŌōŎŏŐőŒœŔŕŖŗŘřŚśŜŝŞşŠšŢţŤťŦŧŨũŪūŬŭŮůŰűŲųŴŵŶŷŸŹźŻżŽžſ",
                //"AaAaAaCcCcCcCcDdDdEeEeEeEeEeGgGgGgGgHhHhIiIiIiIiIiĲijJjKkĸLlLlLlLlLlNnNnNnnNnOoOoOoOEoeRrRrRrSsSsSsSsTtTtTtUuUuUuUuUuUuWwYyYZzZzZzs" // original
                "AaAaAaCcCcCcCcDdDjdjEeEeEeEeEeGgGgGgGgHhHhIiiiIiIiIiIJijJjkkkLlLlLlLlLlNnNnNnnNnOoOoOoOEoeRrRrRrSsSsSsSsTtTtTtUuUuUuUuUuUuWwYyYZzZzZzi"
            ],
            //Latin Extended-B
            [
                "ƒǺǻǼǽǾǿ",
                //"fAaAEaeOo" // original
                "fAaAeaeOo"
            ],
            //Latin Extended Additional
            [
                "ẀẁẂẃẄẅỲỳ",
                "WwWwWwYy"
            ],
        ];

        $data = array_merge(
            $propel2TestData,
            $batStringToolTestDataFormatted,
            $removeDiaCriticsTestData
        );
        return $data;

    }

    /**
     * @group coverage:full
     * @dataProvider transliterationDataProvider
     */
    /*
    public function testTransliterationUsingRemoveDiacritics($in, $out)
    {
        $translit = RemoveDiacritics::process($in);
        $this->assertEquals($out, $translit, 'RemoveDiacritics behaves as expected');
    }
    */

    /**
     * @group coverage:full
     * @dataProvider transliterationDataProvider
     */
    public function testTransliterationUsingDjangosUrlify($in, $out)
    {
        $translit = URLify::transliterate($in);
        $this->assertEquals($out, $translit, 'djangos urlify transliteration behaves as expected');
    }

    /**
     * @group coverage:full
     * @dataProvider transliterationDataProvider
     */
    /*
    public function testTransliterationUsingPHPNormalizer($in, $out)
    {
        $this->assertTrue(extension_loaded('intl'));
        $translit = Normalizer::normalize($in, Normalizer::FORM_KD);
        $this->assertEquals($out, $translit, 'php\'s normalizer transliterates as expected');
    }
    */

    /**
     * @group coverage:full
     * @dataProvider transliterationDataProvider
     */
    /*
    public function testTransliterationUsingIconv($in, $out)
    {
        if (!function_exists('iconv')) {
            $this->markTestSkipped();
        }
        $translit = iconv('utf-8', 'us-ascii//TRANSLIT', $in);
        $this->assertEquals($out, $translit, 'iconv transliteration behaves as expected');
    }
    */

}
