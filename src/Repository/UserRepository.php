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
class UserRepository extends ServiceEntityRepository {

	public function __construct(RegistryInterface $registry) {
		parent::__construct($registry, User::class);
	}

	/**
	 * @return User[]
	 */
	public function findAllByPass($passcode, $challenge) : array {
		return $this->getEntityManager()->createQuery(<<<SQL
			SELECT u
			FROM App\Entity\User u
			WHERE SHA2(CONCAT(SHA2(u.username, 512), :challenge, u.password), 512) = :pwd
SQL
)			->setParameter('challenge', $challenge)
			->setParameter('pwd', $passcode)
			->execute();
	}

	public function findByEmail($value) {
		return $this->createQueryBuilder('u')
			->where('u.email = :value')->setParameter('value', $value)
			->setMaxResults(1)
			->getQuery()
			->getResult()
		;
	}
}
