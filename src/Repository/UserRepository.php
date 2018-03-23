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
	public function findAllByPass($passcode, $challenge) :array
	{
		$rq = $this->getEntityManager()->createQuery(<<<SQL
			SELECT u
			FROM App\Entity\User u
			WHERE SHA2(CONCAT(SHA2(u.username, 512), :challenge, u.password), 512) = :pwd
SQL
)			->setParameter('challenge', $challenge)
			->setParameter('pwd', $passcode)
			->execute();
		return $rq;
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