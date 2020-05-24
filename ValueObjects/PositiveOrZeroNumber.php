<?php

declare(strict_types=1);

namespace BMServerBundle\Server\ValueObjects;

use UnexpectedValueException;

class PositiveOrZeroNumber
{
    protected const EXPECTED_POSITIVE_NUMBER_MESSAGE = 'Expected positive or zero number.';

    private $number;

    public function __construct($number)
    {
        if ((float)$number < 0) {
            throw new UnexpectedValueException(self::EXPECTED_POSITIVE_NUMBER_MESSAGE);
        }

        $this->number = $number;
    }

    public function getInt(): int
    {
        return (int)$this->number;
    }

    public function getFloat(): float
    {
        return (float)$this->number;
    }
}
