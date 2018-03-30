<?php

namespace App\Repository;

use App\Entity\DeleteElement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DeleteElement|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeleteElement|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeleteElement[]    findAll()
 * @method DeleteElement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeleteElementRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DeleteElement::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('d')
            ->where('d.something = :value')->setParameter('value', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
