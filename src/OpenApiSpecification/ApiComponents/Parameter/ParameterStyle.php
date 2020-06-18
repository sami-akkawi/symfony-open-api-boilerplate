<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiComponents\Parameter;

use App\OpenApiSpecification\ApiException\SpecificationException;

/**
 * In order to support common ways of serializing simple parameters, a set of style values are defined.
 * style	        type	                    in	            Comments
 * -------------------------------------------------------------------------------------------------------
 * matrix	        primitive, array, object	path	        Path-style parameters defined by [RFC6570]
 * label	        primitive, array, object	path	        Label style parameters defined by [RFC6570]
 * form             primitive, array, object	query, cookie	Form style parameters defined by [RFC6570]. This option replaces collectionFormat with a csv (when explode is false) or multi (when explode is true) value from OpenAPI 2.0.
 * simple	        array	                    path, header	Simple style parameters defined by [RFC6570]. This option replaces collectionFormatwith a csv value from OpenAPI 2.0.
 * spaceDelimited	array	                    query	        Space separated array values. This option replaces collectionFormat equal to ssv from OpenAPI 2.0.
 * pipeDelimited	array	                    query	        Pipe separated array values. This option replaces collectionFormat equal to pipes from OpenAPI 2.0.
 * deepObject	    object	                    query	        Provides a simple way of rendering nested objects using form parameters.
 * http://spec.openapis.org/oas/v3.0.3#style-values
 */

final class ParameterStyle
{
    const MATRIX          = 'matrix';
    const LABEL           = 'label';
    const FORM            = 'form';
    const SIMPLE          = 'simple';
    const SPACE_DELIMITED = 'spaceDelimited';
    const PIPE_DELIMITED  = 'pipeDelimited';
    const DEEP_OBJECT     = 'deepObject';

    const VALID_STYLES = [
        self::MATRIX,
        self::LABEL,
        self::FORM,
        self::SIMPLE,
        self::SPACE_DELIMITED,
        self::PIPE_DELIMITED,
        self::DEEP_OBJECT
    ];

    const VALID_USAGES = [
        self::MATRIX => [
            'types' => ['primitive', 'array', 'object'],
            'locations' => ['path']
        ],
        self::LABEL => [
            'types' => ['primitive', 'array', 'object'],
            'locations' => ['path']
        ],
        self::FORM => [
            'types' => ['primitive', 'array', 'object'],
            'locations' => ['query', 'cookie']
        ],
        self::SIMPLE => [
            'types' => ['array'],
            'locations' => ['path', 'header']
        ],
        self::SPACE_DELIMITED => [
            'types' => ['array'],
            'locations' => ['query']
        ],
        self::PIPE_DELIMITED => [
            'types' => ['array'],
            'locations' => ['query']
        ],
        self::DEEP_OBJECT => [
            'types' => ['object'],
            'locations' => ['query']
        ],
    ];

    private string $style;

    private function __construct(string $style)
    {
        if (!in_array($style, self::VALID_STYLES)) {
            throw SpecificationException::generateInvalidStyle($style);
        }
        $this->style = $style;
    }

    public static function fromString(string $style): self
    {
        return new self($style);
    }

    public static function generateMatrix(): self
    {
        return new self(self::MATRIX);
    }

    public static function generateLabel(): self
    {
        return new self(self::LABEL);
    }

    public static function generateForm(): self
    {
        return new self(self::FORM);
    }

    public static function generateSimple(): self
    {
        return new self(self::SIMPLE);
    }

    public static function generateSpaceDelimited(): self
    {
        return new self(self::SPACE_DELIMITED);
    }

    public static function generatePipeDelimited(): self
    {
        return new self(self::PIPE_DELIMITED);
    }

    public static function generateDeepObject(): self
    {
        return new self(self::DEEP_OBJECT);
    }

    public function toString(): string
    {
        return $this->style;
    }

    public static function getValidStyles(): array
    {
        return self::VALID_STYLES;
    }
}