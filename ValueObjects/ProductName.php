<?php

declare(strict_types=1);

namespace BMServerBundle\Server\ValueObjects;

use UnexpectedValueException;

class ProductName
{
    public const    MAX_PRODUCT_NAME_LENGTH = 250;
    protected const ELEMENT_CANNOT_BE_EMPTY_MESSAGE = 'Product name cannot be empty.';
    protected const STRING_ELEMENT_IS_TOO_LONG_MESSAGE = 'Product name is too long';

    /**
     * @var string
     */
    private $value;

    public function __construct(string $productName)
    {
        if (empty($productName)) {
            throw new UnexpectedValueException(self::ELEMENT_CANNOT_BE_EMPTY_MESSAGE);
        }

        if (mb_strlen($productName) > self::MAX_PRODUCT_NAME_LENGTH) {
            throw new UnexpectedValueException(self::STRING_ELEMENT_IS_TOO_LONG_MESSAGE);
        }

        $this->value = $productName;
    }

    /**
     * @return string
     */
    public function get(): string
    {
        return $this->value;
    }
}
