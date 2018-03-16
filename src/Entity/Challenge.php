<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChallengeRepository")
 */
class Challenge {
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=64)
	 */
	private $challenge;

	/**
	 * @return int Identifier for the challenge
	 */
	public function getID() {
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setID($id): Challenge
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return string Challenge
	 */
	public function getChallenge()
	{
		return $this->challenge;
	}

	/**
	 * @param string $challenge
	 */
	public function setChallenge($challenge): Challenge
	{
		$this->challenge = $challenge;
		return $this;
	}


}