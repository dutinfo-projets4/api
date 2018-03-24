<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User {
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $username;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $password;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $email;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $isAdmin;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Element", mappedBy="user")
	 */
	private $elements;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Directory", mappedBy="user")
	 */
	private $groups;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Token", mappedBy="user")
	 */
	private $tokens;

	public function __construct($request) {
		$this->username = $request->get('username');
		$this->email    = $request->get('email');
		$this->password = hash('sha512', $request->get('password'));
		$this->isAdmin  = false;
	}

	/**
	 * @return int Identifier for the user
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
	 * @return string User's name
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username): void {
		$this->username = $username;
	}

	/**
	 * @return string SHA512'd password
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password): void {
		$this->password = hash('sha512', $password);
	}

	/**
	 * @return string User's email
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	public function setEmail($email): void {
		$this->email = $email;
	}

	/**
	 * @return bool
	 */
	public function isAdmin() {
		return $this->isAdmin;
	}

	/**
	 * @param bool $isAdmin
	 */
	public function setAdmin($isAdmin): void {
		$this->isAdmin = $isAdmin;
	}

	/**
	 * @return Token[]
	 */
	public function getTokens() {
		return $this->tokens;
	}

	/**
	 * @return Element[]
	 */
	public function getElements()
	{
		return $this->elements;
	}

	/**
	 * @param Element
	 */
	public function setElements($elements): void
	{
		$this->elements = $elements;
	}

	/**
	 * @return Directory[]
	 */
	public function getGroups()
	{
		return $this->groups;
	}

	/**
	 * @param Directory
	 */
	public function setGroups($groups): void
	{
		$this->groups = $groups;
	}



}
