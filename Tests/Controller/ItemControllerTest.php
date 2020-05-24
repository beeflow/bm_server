<?php

declare(strict_types=1);

namespace BMServerBundle\Server\Tests\Controller;

use BMServerBundle\Server\Controller\ItemController;
use BMServerBundle\Server\Libs\ApiResponse\ApiProblem;
use BMServerBundle\Server\Libs\ApiResponse\ApiResponse;
use BMServerBundle\Server\Libs\ApiResponse\ApiResponseFactory;
use BMServerBundle\Server\Repository\ItemRepository;
use BMServerBundle\Server\Tests\Stubs\ApiProblemStub;
use BMServerBundle\Server\Tests\Stubs\ApiResponseForCreateStub;
use BMServerBundle\Server\ValueObjects\ProductName;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory as FakerFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class ItemControllerTest extends TestCase
{
    /**
     * @var ItemRepository|MockObject
     */
    private $itemRepositoryMock;

    /**
     * @var EntityManagerInterface|MockObject
     */
    private $entityManagerMock;

    /**
     * @var ApiResponseFactory|MockObject
     */
    private $responseFactoryMock;

    /**
     * @var ApiResponse|MockObject
     */
    private $apiResponseStub;

    /**
     * @var ApiProblem|MockObject
     */
    private $apiProblemStub;

    /**
     * @var ItemController
     */
    private $controller;

    /**
     * @var Request|MockObject
     */
    private $requestMock;

    /**
     * @var ParameterBag|MockObject
     */
    private $parameterBagMock;

    protected function setUp(): void
    {
        $this->itemRepositoryMock = $this->createMock(ItemRepository::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->apiResponseStub = new ApiResponseForCreateStub();
        $this->apiProblemStub = new ApiProblemStub();

        $this->responseFactoryMock = $this->createMock(ApiResponseFactory::class);
        $this->responseFactoryMock
            ->method('createApiResponse')
            ->willReturnReference($this->apiResponseStub);
        $this->responseFactoryMock
            ->method('createApiProblem')
            ->willReturnReference($this->apiProblemStub);

        $this->controller = new ItemController(
            $this->itemRepositoryMock,
            $this->entityManagerMock,
            $this->responseFactoryMock
        );

        $this->requestMock = $this->createMock(Request::class);
        $this->parameterBagMock = $this->createMock(ParameterBag::class);

        $this->requestMock->request = $this->parameterBagMock;
    }

    /**
     * @dataProvider invalidParamsProvider
     */
    public function testShouldReturnApiProblemWithTwoInvalidParams(int $invalidParamsCount, array $invalidParams): void
    {
        $this->parameterBagMock->method('all')->willReturn($invalidParams);
        $result = json_decode(
            $this->controller->create($this->requestMock)->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertEquals('Bad request', $result['title']);
        $this->assertCount($invalidParamsCount, $result['invalidParams']);
    }

    public function testShouldReturnApiResponse(): void
    {
        $faker = FakerFactory::create();
        $params = ['name' => $faker->word, 'amount' => $faker->numberBetween(1, 100)];
        $this->parameterBagMock->method('all')->willReturn($params);
        $this->assertEquals(201, $this->controller->create($this->requestMock)->getStatusCode());
    }

    public function invalidParamsProvider(): array
    {
        return [
            'empty request' => [2, []],
            'missing amount param' => [1, ['name' => FakerFactory::create()->word]],
            'missing product name' => [1, [
                'amount' => FakerFactory::create()->numberBetween(0, 100)
            ]],
            'too long product name' => [1, [
                'amount' => FakerFactory::create()->numberBetween(0, 100),
                'name' => FakerFactory::create()->regexify(
                    '[A-Za-z0-9]{' . (ProductName::MAX_PRODUCT_NAME_LENGTH + 1) . '}'
                )
            ]],
            'invalid amount' => [1, [
                'name' => FakerFactory::create()->word,
                'amount' => FakerFactory::create()->numberBetween(-100, -1)
            ]]
        ];
    }

    protected function tearDown(): void
    {
        unset(
            $this->itemRepositoryMock,
            $this->entityManagerMock,
            $this->responseFactoryMock,
            $this->apiProblemStub,
            $this->apiResponseStub,
            $this->controller
        );
    }
}
