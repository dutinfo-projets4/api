<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DirectoryRepository")
 */
class Directory {
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
	 * Sub-group for the current group
	 * @ORM\OneToMany(targetEntity="App\Entity\Directory", mappedBy="directory")
	 */
	private $groups;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Element", mappedBy="directory")
	 */
	private $elements;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="directory")
	 */
	private $user;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Directory", inversedBy="directory")
	 */
	private $parent;

	public function __construct($user, $parent, $request){
		$this->user = $user;
		$this->content = $request->get('content');
		$this->parent = $parent;
		$this->lastUpdateTS = new \DateTime();
	}

	/**
	 * @return int Identifier of the group
	 */
	public function getID() {
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setID($id): void {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name): void {
		$this->name = $name;
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
	 * @return Array<Element>
	 */
	public function getElements() {
		return $this->elements;
	}

	/**
	 * @return Array<Group>
	 */
	public function getGroups()
	{
		return $this->groups;
	}

	/**
	 * @param Array<Group>
	 */
	public function setGroups($groups): void
	{
		$this->groups = $groups;
	}

	/**
	 * @return User isntance
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @param User instance
	 */
	public function setUser($user): void
	{
		$this->user = $user;
	}

	/**
	 * @return Group instance
	 */
	public function getParentGroup() {
		return $this->parent;
	}

	/**
	 * @param Group instance
	 */
	public function setParentGroup($parent): void
	{
		$this->parent = $parent;
	}

	/**
	 * @return String
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @param String content
	 */
	public function setContent($content): void
	{
		$this->content = $content;
	}

	public function asArray() {
		$parent = $this->parent == null ? -1 : $this->parent->getID();
		return array(
			"id" => $this->getID(),
			"parent" => $parent,
			"content" => $this->content
		);
	}

	/**
	 * @TODO Add element
	 * @TODO Remove element
	 */

}
