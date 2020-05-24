<?php

declare(strict_types=1);

namespace BMServerBundle\Server\Tests\ValueObjects;

use BMServerBundle\Server\ValueObjects\ProductName;
use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;

class ProductNameTest extends TestCase
{
    public function testShouldReturnCorrectProductName(): void
    {
        $randomProductName = FakerFactory::create()->word();
        $productName = new ProductName($randomProductName);
        $this->assertEquals($randomProductName, $productName->get());
    }

    /**
     * @dataProvider incorrectProductNameProvider
     */
    public function testShouldThrowException(string $incorrectName): void
    {
        $this->expectException(\UnexpectedValueException::class);
        new ProductName($incorrectName);
    }

    public function incorrectProductNameProvider(): array
    {
        return [
            'empty product name' => [''],
            'product name too long' => [
                FakerFactory::create()->regexify('[A-Za-z0-9]{' . (ProductName::MAX_PRODUCT_NAME_LENGTH + 1) . '}')
            ],
        ];
    }
}
