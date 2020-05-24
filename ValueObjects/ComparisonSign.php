<?php

declare(strict_types=1);

namespace BMServerBundle\Server\ValueObjects;

class ComparisonSign
{
    /**
     * @var string
     */
    private $comparisonSign;

    public function __construct(string $comparisonSign)
    {
        if (!in_array($comparisonSign, ['>', '>=', '=', '<=', '<'])) {
            throw new \UnexpectedValueException('Expected comparison sign');
        }

        $this->comparisonSign = $comparisonSign;
    }

    public function get(): string
    {
        return $this->comparisonSign;
    }
}
