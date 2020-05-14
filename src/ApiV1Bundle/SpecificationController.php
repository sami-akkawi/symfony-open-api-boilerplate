<?php declare(strict=1);

namespace App\ApiV1Bundle;

use Symfony\Component\HttpFoundation\Response;

final class SpecificationController
{
    public function show(): Response
    {
        $specification = new Specification();
        return new Response('<pre>' . $specification->toJson() . '</pre>');
    }
}