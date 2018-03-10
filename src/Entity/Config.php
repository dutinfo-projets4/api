<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConfigRepository")
 */
class Config
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $isCaptcha;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $limit;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $public;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $api;

	/**
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param integer $id
	 */
	public function setId($id): void
	{
		$this->id = $id;
	}

	/**
	 * @return boolean true if captcha enabled false then
	 */
	public function isCaptcha()
	{
		return $this->isCaptcha;
	}

	/**
	 * @param boolean $isCaptcha
	 */
	public function setCaptcha($isCaptcha): void
	{
		$this->isCaptcha = $isCaptcha;
	}

	/**
	 * @return integer Limit the amount of /update request per minute

	 */
	public function getLimit()
	{
		return $this->limit;
	}

	/**
	 * @param integer $limit
	 */
	public function setLimit($limit): void
	{
		$this->limit = $limit;
	}

	/**
	 * @return boolean People can register by themself
	 */
	public function getPublic()
	{
		return $this->public;
	}

	/**
	 * @param boolean $public
	 */
	public function setPublic($public): void
	{
		$this->public = $public;
	}

	/**
	 * @return boolean People can be registered through the API
	 */
	public function getApi()
	{
		return $this->api;
	}

	/**
	 * @param boolean $api
	 */
	public function setApi($api): void
	{
		$this->api = $api;
	}


}
