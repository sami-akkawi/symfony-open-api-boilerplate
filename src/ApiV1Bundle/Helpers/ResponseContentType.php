<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Helpers;

final class ResponseContentType
{
    const APPLICATION_XML  = 'application/xml';
    const APPLICATION_JSON = 'application/json';
    const ANY              = '*/*';

    private array $acceptedTypes = [];

    public function __construct(string $requestAccept)
    {
        foreach (explode(',', $requestAccept) as $type) {
            $type=trim($type);
            $qPos = strpos($type, ';');
            if($qPos) {
                $type = substr($type, 0, $qPos);
            }
            $this->acceptedTypes[] = trim($type);
        }
    }

    public function mustBeXml(): bool
    {
        if (!in_array(self::APPLICATION_XML, $this->acceptedTypes, true)) {
            return false;
        }

        if (in_array(self::ANY, $this->acceptedTypes, true)) {
            $flippedArray = array_flip($this->acceptedTypes);
            return $flippedArray[self::APPLICATION_XML] < $flippedArray[self::ANY];
        }

        return true;
    }

    public function toArray(): array
    {
        return $this->acceptedTypes;
    }
}