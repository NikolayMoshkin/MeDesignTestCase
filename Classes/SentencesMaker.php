<?php


namespace Classes;

class SentencesMaker
{

    protected static $counter;
    protected static $groups;
    protected static $baseString;
    protected static $sentences;

    public static function init($string)
    {
        self::$counter = 0;

        $string = preg_replace('/\n/', ' ', $string);
        self::$baseString = self::makeBaseString($string);

        self::makeGroup($string);

        self::makeSentences(self::$baseString);

        return self::$sentences;
    }

    protected static function makeGroup($string)
    {
        if (preg_match_all('/\{([^\{]*?)\}/', $string, $matches)) {
            self::$groups[] = array_map(function ($value) {
                return explode('|', $value);
            }, $matches[1]);
            foreach ($matches[0] as $key => $value) {
                $string = str_replace($value, '%%' . self::$counter . '-' . $key . '%%', $string);
            }
            self::$counter++;
            self::makeGroup($string);

        }
    }

    protected static function makeBaseString($string)
    {
        if (preg_match_all('/\{[^\{]*?\}/', $string, $matches)) {
            foreach ($matches[0] as $key => $value) {
                $string = str_replace($value, '%%' . self::$counter . '-' . $key . '%%', $string);
            }
            self::$counter++;
            $string = self::makeBaseString($string);
        }

        self::$counter = 0;
        return $string;
    }

    protected static function makeSentences($string)
    {

        if (preg_match('/%%(\d-\d)%%/', $string, $match)) {
            $layerUnitArray = explode('-', $match[1]);
            $layer = $layerUnitArray[0];
            $unit = $layerUnitArray[1];
            foreach (self::$groups[$layer][$unit] as $option) {
                $stringOption = str_replace($match[0], $option, $string);
                self::makeSentences($stringOption);
            }
        } else {
            self::$sentences[] = $string;
        }

    }

}