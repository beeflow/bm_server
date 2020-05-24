<?php

declare(strict_types=1);

namespace BMServerBundle\Server\Tests\ValueObjects;

use BMServerBundle\Server\ValueObjects\PositiveOrZeroNumber;
use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class PositiveOrZeroNumberTest extends TestCase
{
    public function testShouldThrowExceptionWithIncorrectNumber(): void
    {
        $this->expectException(UnexpectedValueException::class);
        new PositiveOrZeroNumber(FakerFactory::create()->numberBetween(-100, -1));
    }

    public function testShouldReturnCorrectIntNumber(): void
    {
        $randomNumber = FakerFactory::create()->numberBetween(0, 100);
        $number = new PositiveOrZeroNumber($randomNumber);
        $this->assertEquals($randomNumber, $number->getInt());
    }

    public function testShouldReturnCorrectFloatNumber(): void
    {
        $randomNumber = FakerFactory::create()->numberBetween(0, 100) + 0.1;
        $number = new PositiveOrZeroNumber($randomNumber);
        $this->assertEquals($randomNumber, $number->getFloat());
    }
}
