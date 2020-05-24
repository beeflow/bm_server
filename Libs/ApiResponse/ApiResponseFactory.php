<?php

declare(strict_types=1);

namespace BMServerBundle\Server\Libs\ApiResponse;

class ApiResponseFactory
{
    public function createApiResponse(): ApiResponse
    {
        return new ApiResponse();
    }

    public function createApiProblem(): ApiProblem
    {
        return new ApiProblem();
    }
}
