<?php

namespace neam;

/**
 * Overridden to supply additional chars that we want to transliterate by default in DNA projects
 *
 * Note: This class needed a lot of copy-pasting to override thanks to the original class's use of
 * self:: instead of static::
 *
 * @package neam
 */
class URLify extends \URLify
{

    protected static $charsAdded = false;

    public static function downcode($text, $language = "")
    {
        if (!static::$charsAdded) {
            parent::add_chars(
                array(
                    '¿' => '?',
                    '®' => '(r)',
                    '¼' => '1-4',
                    '½' => '1-2',
                    '¾' => '3-4',
                    '¶' => 'P',
                    '€' => 'EUR',
                    'Ÿ' => 'Y',
                    'µ' => 'u',
                    '¥' => 'Y',
                    'Ĉ' => 'C',
                    'ĉ' => 'c',
                    'Ċ' => 'C',
                    'ċ' => 'c',
                    'Ĝ' => 'G',
                    'ĝ' => 'g',
                    'Ġ' => 'G',
                    'ġ' => 'g',
                    'Ĥ' => 'H',
                    'ĥ' => 'h',
                    'Ħ' => 'H',
                    'ħ' => 'h',
                    'Ĕ' => 'E',
                    'ĕ' => 'e',
                    'Ĭ' => 'I',
                    'ĭ' => 'i',
                    'Ĵ' => 'J',
                    'ĵ' => 'j',
                    'Ĺ' => 'L',
                    'ĺ' => 'l',
                    'Ľ' => 'L',
                    'ľ' => 'l',
                    'Ŀ' => 'L',
                    'ŀ' => 'l',
                    'ŉ' => 'n',
                    'Ō' => 'O',
                    'ō' => 'o',
                    'Ŏ' => 'O',
                    'ŏ' => 'o',
                    'Ŕ' => 'R',
                    'ŕ' => 'r',
                    'Ŗ' => 'R',
                    'ŗ' => 'r',
                    'Ŝ' => 'S',
                    'ŝ' => 's',
                    'Ŧ' => 'T',
                    'ŧ' => 't',
                    'Ŭ' => 'U',
                    'ŭ' => 'u',
                    'Ŵ' => 'W',
                    'ŵ' => 'w',
                    'Ŷ' => 'Y',
                    'ŷ' => 'y',
                    'ſ' => 'i',
                    'ƒ' => 'f',
                    'O' => 'O',
                    'o' => 'o',
                    'U' => 'U',
                    'u' => 'u',
                    'Ǎ' => 'A',
                    'ǎ' => 'a',
                    'Ǐ' => 'I',
                    'ǐ' => 'i',
                    'Ǒ' => 'O',
                    'ǒ' => 'o',
                    'Ǔ' => 'U',
                    'ǔ' => 'u',
                    'Ǖ' => 'U',
                    'ǖ' => 'u',
                    'Ǘ' => 'U',
                    'ǘ' => 'u',
                    'Ǚ' => 'U',
                    'ǚ' => 'u',
                    'Ǜ' => 'U',
                    'ǜ' => 'u',
                    'Ǻ' => 'A',
                    'ǻ' => 'a',
                    'Ǿ' => 'O',
                    'ǿ' => 'o',
                    'Ǽ' => 'Ae',
                    'ǽ' => 'ae',
                    'Ĳ' => 'IJ',
                    'ĳ' => 'ij',
                    'J' => 'J',
                    'ĸ' => 'k',
                    'Ŋ' => 'N',
                    'ŋ' => 'n',
                    'Ẁ' => 'W',
                    'ẁ' => 'w',
                    'Ẃ' => 'W',
                    'ẃ' => 'w',
                    'Ẅ' => 'W',
                    'ẅ' => 'w',
                )
            );
            static::$charsAdded = true;
        }
        return parent::downcode($text, $language);
    }

    /**
     * Filters a string, e.g., "Petty theft" to "petty-theft"
     */
    public static function filter($text, $length = 60, $language = "", $file_name = false, $use_remove_list = true)
    {
        $text = self::downcode($text, $language);

        if ($use_remove_list) {
            // remove all these words from the string before urlifying
            $text = preg_replace('/\b(' . join('|', self::$remove_list) . ')\b/i', '', $text);
        }

        // if downcode doesn't hit, the char will be stripped here
        $remove_pattern = ($file_name) ? '/[^_\-.\-a-zA-Z0-9\s]/u' : '/[^\s_\-a-zA-Z0-9]/u';
        $text = preg_replace($remove_pattern, '', $text); // remove unneeded chars
        $text = str_replace('_', ' ', $text);             // treat underscores as spaces
        $text = preg_replace('/^\s+|\s+$/u', '', $text);  // trim leading/trailing spaces
        $text = preg_replace('/[-\s]+/u', '-', $text);    // convert spaces to hyphens
        $text = strtolower($text);                        // convert to lowercase
        return trim(substr($text, 0, $length), '-');     // trim to first $length chars
    }

    /**
     * Alias of `URLify::downcode()`.
     */
    public static function transliterate($text)
    {
        return self::downcode($text);
    }

}