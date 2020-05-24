<?php

declare(strict_types=1);

namespace BMServerBundle\Server\Tests\Stubs;

use BMServerBundle\Server\Libs\ApiResponse\ApiResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponseForCreateStub extends ApiResponse
{
    public function getResponse(): JsonResponse
    {
        return new JsonResponse([], 201, ['Content-Type' => 'application/vnd.api+json']);
    }
}
