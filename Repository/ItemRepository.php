<?php

declare(strict_types=1);

namespace BMServerBundle\Server\Repository;

use BMServerBundle\Server\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    public function findAvailable(): array
    {
        return $this->findByAmount('>', 0);
    }

    public function findUnAvailable(): array
    {
        return $this->findByAmount('=', 0);
    }

    public function findByAmount(string $comparisonSign, int $amount): array
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $query = $queryBuilder->select('p')
            ->from(Item::class, 'p')
            ->where(
                $this->getExpression($queryBuilder, $comparisonSign, 'p.amount', $amount)
            )
            ->getQuery();

        return $query->getArrayResult();
    }

    /**
     * @param QueryBuilder $builder
     * @param string $comparisonSign
     * @param string $key
     * @param $value
     *
     * @return Comparison
     */
    private function getExpression(QueryBuilder $builder, string $comparisonSign, string $key, $value): Comparison
    {
        switch ($comparisonSign) {
            case '>':
                return $builder->expr()->gt($key, $value);
            case '>=':
                return $builder->expr()->gte($key, $value);
            case '<=':
                return $builder->expr()->lte($key, $value);
            case '<':
                return $builder->expr()->lt($key, $value);
            default:
                return $builder->expr()->eq($key, $value);
        }
    }
}
