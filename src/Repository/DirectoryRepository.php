<?php

namespace App\Repository;

use Doctrine\ORM\Query\ResultSetMappingBuilder;
use App\Entity\Directory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Directory|null find($id, $lockMode = null, $lockVersion = null)
 * @method Directory|null findOneBy(array $criteria, array $orderBy = null)
 * @method Directory[]    findAll()
 * @method Directory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DirectoryRepository extends ServiceEntityRepository {
	public function __construct(RegistryInterface $registry) {
		parent::__construct($registry, Directory::class);
	}

	public function findByToken($value) {
		$rsm = new ResultSetMappingBuilder($this->getEntityManager());

		$rsm->addRootEntityFromClassMetadata('App\Entity\Directory', 'd');
		$rsm->addJoinedEntityFromClassMetadata('App\Entity\User', 'u', 'd', array('u.id' => 'd.user_id'));
		$rsm->addJoinedEntityFromClassMetadata('App\Entity\Token', 't', 'u', array('t.user_id' => 'u.id'));

		$rq = $this->getEntityManager()->createNativeQuery(<<<SQL
		SELECT *
		FROM Directory d INNER JOIN User u ON d.user_id = u.id INNER JOIN Token t ON u.id = t.user_id
SQL
		, $rsm);
		return $rq->getArrayResult();
	}

}
