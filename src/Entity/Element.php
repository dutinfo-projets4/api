<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\ElementRepository")
 */
class Element {
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="text")
	 */
	private $content;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $lastUpdateTS;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="elements")
	 */
	private $user;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Group", inversedBy="elements")
	 */
	private $group;

	/**
	 * @return int Identifier for the element
	 */
	public function getID() {
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setID($id): void
	{
		$this->id = $id;
	}

	/**
	 * @return string Content
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @param string $content
	 */
	public function setContent($content): void {
		$this->content = $content;
	}

	/**
	 * @return datetime
	 */
	public function getLastUpdateTS() {
		return $this->lastUpdateTS;
	}

	/**
	 * @param datetime $lastUpdateTS
	 */
	public function setLastUpdateTS($lastUpdateTS): void {
		$this->lastUpdateTS = $lastUpdateTS;
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
	 * @return Group
	 */
	public function getGroup()
	{
		return $this->group;
	}

	/**
	 * @param Group $group
	 */
	public function setGroup($group): void {
		$this->group = $group;
	}

}
