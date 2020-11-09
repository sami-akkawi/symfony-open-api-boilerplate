<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Helpers;

final class XmlToJsonConvertor
{
    public static function convert(string $xml): string
    {
        $xml = str_replace(['&'],['&amp;'], $xml);
        try {
            $doc = simplexml_load_string($xml);
        } catch (\Throwable $throwable) {
            die(print_r([$throwable->getMessage(), $xml]));
        }

        $array = json_decode(json_encode($doc), true);
        return json_encode($array);
    }
}