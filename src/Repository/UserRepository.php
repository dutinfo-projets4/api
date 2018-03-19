<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
	public function __construct(RegistryInterface $registry)
	{
		parent::__construct($registry, User::class);
	}

	/**
	 * @param $pass
	 * @param $challenge
	 * @return User[]
	 */
	public function findAllByPass($pass, $challenge) :array
	{
		return $this->getEntityManager()->createQuery(
			'SELECT u 
				  FROM App\Entity\User u 
				  WHERE SHA2(CONCAT(SHA2(u.username,512), :challenge, u.password),512) = :pass'
		)
			->setParameter('challenge', $challenge)
			->setParameter('pass', $pass)
			->setMaxResults(1)
			->execute();
	}

	/*
	public function findBySomething($value)
	{
		return $this->createQueryBuilder('u')
			->where('u.something = :value')->setParameter('value', $value)
			->orderBy('u.id', 'ASC')
			->setMaxResults(10)
			->getQuery()
			->getResult()
		;
	}
	*/
}