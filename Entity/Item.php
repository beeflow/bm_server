<?php

declare(strict_types=1);

namespace BMServerBundle\Server\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Item
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $amount;

    public function __construct(string $name, int $amount, int $id = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->amount = $amount;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}
