<?php

namespace App\Repository;

use App\Entity\DeleteDirectory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DeleteDirectory|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeleteDirectory|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeleteDirectory[]    findAll()
 * @method DeleteDirectory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeleteDirectoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DeleteDirectory::class);
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
