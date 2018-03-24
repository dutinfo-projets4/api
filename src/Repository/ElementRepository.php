<?php

namespace App\Repository;

use App\Entity\Element;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Element|null find($id, $lockMode = null, $lockVersion = null)
 * @method Element|null findOneBy(array $criteria, array $orderBy = null)
 * @method Element[]    findAll()
 * @method Element[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ElementRepository extends ServiceEntityRepository {

	public function __construct(RegistryInterface $registry) {
		parent::__construct($registry, Element::class);
	}

	public function findByToken($value) {
		return $this->getEntityManager()->createQuery(<<<SQL
			SELECT d
			FROM App\Entity\Element d, App\Entity\Token t, App\Entity\User u
			WHERE t.token = :token
			AND   t.user_id = u.id
			AND   u.id = d.user_id
SQL
	)
		->setParameter('token', $value)
		->execute();
	}
}
