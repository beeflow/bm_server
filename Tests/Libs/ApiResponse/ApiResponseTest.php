<?php

declare(strict_types=1);

namespace BMServerBundle\Server\Tests\Libs\ApiResponse;

use BMServerBundle\Server\Libs\ApiResponse\ApiResponse;
use PHPUnit\Framework\TestCase;
use TypeError;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ApiResponseTest extends TestCase
{
    protected $apiResponse;

    protected function setUp(): void
    {
        $this->apiResponse = new ApiResponse();
    }

    public function testSetVersion()
    {
        $apiResponse = $this->apiResponse->setVersion('1.2');

        $this->assertTrue($apiResponse instanceof ApiResponse);
    }

    public function testSetIncorrectLinks()
    {
        $this->expectException(TypeError::class);
        $this->apiResponse->setLinks('');
    }

    public function testSetLinks()
    {
        $response = $this->apiResponse->setLinks([]);

        $this->assertTrue($response instanceof ApiResponse);
    }

    public function testAddIncorrectLinks()
    {
        $this->expectException(TypeError::class);
        $this->apiResponse->addLink('');
    }

    public function testAddLinks()
    {
        $response = $this->apiResponse->addLink([]);

        $this->assertTrue($response instanceof ApiResponse);
    }

    public function testSetIncluded()
    {
        $response = $this->apiResponse->setIncluded([]);

        $this->assertTrue($response instanceof ApiResponse);
    }

    public function testAddIncluded()
    {
        $response = $this->apiResponse->addIncluded('');

        $this->assertTrue($response instanceof ApiResponse);
    }

    public function testAddIncorrectIncluded()
    {
        $this->expectException(TypeError::class);
        $this->apiResponse->addIncluded([]);
    }

    public function testSetMeta()
    {
        $response = $this->apiResponse->setMeta([]);

        $this->assertTrue($response instanceof ApiResponse);
    }

    public function testSetIncorrectMeta()
    {
        $this->expectException(TypeError::class);
        $this->apiResponse->setMeta(11.22);
    }

    public function testAddMeta()
    {
        $response = $this->apiResponse->addMeta('aa', 'bb');

        $this->assertTrue($response instanceof ApiResponse);
    }

    public function testAddIncorrectMeta()
    {
        $this->expectException(TypeError::class);
        $this->apiResponse->addMeta([], []);
    }

    public function testSetStatus()
    {
        $response = $this->apiResponse->setStatus(200);

        $this->assertTrue($response instanceof ApiResponse);
    }

    public function testSetIncorrectStatus()
    {
        $this->expectException(TypeError::class);
        $this->apiResponse->setStatus('');
    }

    public function testSetData()
    {
        $response = $this->apiResponse->setData([]);

        $this->assertTrue($response instanceof ApiResponse);
    }


    public function testSetIncorrectData()
    {
        $this->expectException(TypeError::class);
        $this->apiResponse->setData(11.22);
    }
}
