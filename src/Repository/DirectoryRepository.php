<?php

namespace App\Repository;

use App\Entity\Directory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Directory|null find($id, $lockMode = null, $lockVersion = null)
 * @method Directory|null findOneBy(array $criteria, array $orderBy = null)
 * @method Directory[]    findAll()
 * @method Directory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DirectoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Directory::class);
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
