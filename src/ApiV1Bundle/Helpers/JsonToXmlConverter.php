<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Helpers;

use DOMDocument;
use DOMNode;
use Exception;

final class JsonToXmlConverter
{
    private const XML_VERSION = '1.0';
    private const XML_ENCODING = 'UTF-8';
    private static ?DOMDocument $xml = null;

    private static function initialize(): DOMDocument {
        self::$xml = new DOMDocument(self::XML_VERSION, self::XML_ENCODING);
        self::$xml->formatOutput = true;
        return self::$xml;
    }

    public static function convert(string $json): string
    {
        $xml = self::initialize();
        $xml->appendChild(self::convertNode('root', json_decode($json, true)));
        $string = $xml->saveXML();
        if ($string === false) {
            throw new Exception('Unable to create XML');
        }
        return $string;
    }

    private static function convertNode(string $nodeName, $entry): DOMNode
    {
        $xml = self::getXMLRoot();
        $node = $xml->createElement($nodeName);

        if(is_array($entry)){
            foreach($entry as $key => $value){
                $key = str_replace('%', '', $key);
                if(!self::isValidTagName($key)) {
                    throw new Exception('Illegal character in tag name. tag: ' . $key . ' in node: ' . $nodeName);
                }
                if(is_array($value) && is_numeric(key($value))) {
                    foreach($value as $index => $item){
                        $node->appendChild(self::convertNode($key, $item));
                    }
                } else {
                    $node->appendChild(self::convertNode($key, $value));
                }
                unset($entry[$key]);
            }
        }

        if(!is_array($entry)) {
            $node->appendChild($xml->createTextNode(self::castToString((string)$entry)));
        }

        return $node;
    }

    private static function getXMLRoot(): DOMDocument
    {
        if(empty(self::$xml)) {
            self::initialize();
        }
        return self::$xml;
    }

    private static function castToString($value): string
    {
        if (is_null($value)) {
            return 'null';
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        return (string)$value;
    }

    private static function isValidTagName(string $tag): bool
    {
        return is_int(preg_match('/^[a-z_]+[a-z0-9:\-._]*[^:]*$/i', $tag, $matches))
            && isset($matches[0])
            && $matches[0] === $tag;
    }
}