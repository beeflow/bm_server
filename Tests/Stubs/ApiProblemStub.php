<?php

declare(strict_types=1);

namespace BMServerBundle\Server\Tests\Stubs;

use BMServerBundle\Server\Libs\ApiResponse\ApiProblem;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiProblemStub extends ApiProblem
{
    private $invalidParams = [];
    
    public function addInvalidParam(array $invalidParam): ApiProblem
    {
        $this->invalidParams[] = $invalidParam;

        return $this;
    }

    public function getResponse(): JsonResponse
    {
        $response = [
            'title' => 'Bad request'
        ];

        if (!empty($this->invalidParams)) {
            $response['invalidParams'] = $this->invalidParams;
        }

        return new JsonResponse($response, 400, ['Content-Type' => 'application/api-problem+json']);
    }
}
