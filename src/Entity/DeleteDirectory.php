<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DeleteDirectoryRepository")
 */
class DeleteDirectory
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="change")
	 */
	private $user;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $directory;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $updateTS;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id): void
	{
		$this->id = $id;
	}

	/**
	 * @return User
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @param User $user
	 */
	public function setUser($user): void
	{
		$this->user = $user;
	}

	/**
	 * @return integer
	 */
	public function getDirectory()
	{
		return $this->directory;
	}

	/**
	 * @param integer $directory
	 */
	public function setDirectory($directory): void
	{
		$this->directory = $directory;
	}

	/**
	 * @return \DateTime
	 */
	public function getUpdateTS()
	{
		return $this->updateTS;
	}

	/**
	 * @param \DateTime $updateTS
	 */
	public function setUpdateTS($updateTS): void
	{
		$this->updateTS = $updateTS;
	}

	public function asArray() {
		return array(
			"id" => $this->getID(),
			"user" => $this->getUser()->getID(),
			"directory" => $this->directory,
			"timestamp" => $this->updateTS,
		);
	}

}
