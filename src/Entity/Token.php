<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TokenRepository")
 */
class Token {
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $token;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $machineName;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $ip;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $loginTS;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $lastUpdateTS;

	/**
	 * @ORM\Column(type="string", length=1024)
	 */
	private $publicKey;

	/**
	 * @ORM\Column(type="integer", options={"default": 0})
	 */
	private $requestID;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tokens")
	 */
	private $user;


	public function __construct($user, $req) {
		$this->user         = $user;
		$this->token        = Token::uuid();
		$this->machineName  = $req->get('machine_name');
		$this->ip           = $req->getClientIp();
		$this->publicKey    = $req->get('public_key');
		$this->lastUpdateTS = new \DateTime();
		$this->loginTS      = new \DateTime();
	}

	/**
	 * Thanks to https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid
	 * random_bytes is cryptographically secure
	 */
	public static function uuid() {
		$data = random_bytes(15);

		$data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}

	/**
	 * @return int Identifier for the token
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
	 * @return string User's token
	 */
	public function getToken() {
		return $this->token;
	}

	/**
	 * @param string $token
	 */
	public function setToken($token): void {
		$this->token = $token;
	}

	/**
	 * @return string Encrypted machine name
	 */
	public function getMachineName() {
		return $this->machineName;
	}

	/**
	 * @param string $machineName
	 */
	public function setMachineName($machineName): void {
		$this->machineName = $machineName;
	}

	/**
	 * @return String
	 */
	public function getIP() {
		return $this->ip;
	}

	/**
	 * @param string $ip
	 */
	public function setIP($ip): void
	{
		$this->ip = $ip;
	}

	/**
	 * @return datetime
	 */
	public function getLoginTS() {
		return $this->loginTS;
	}

	/**
	 * @param datetime $loginTS
	 */
	public function setLoginTS($loginTS): void {
		$this->loginTS = $loginTS;
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
	 * @return String publickKey
	 */
	public function getPublicKey()
	{
		return $this->publicKey;
	}

	/**
	 * @param String $publicKey
	 */
	public function setPublicKey($publicKey): void
	{
		$this->publicKey = $publicKey;
	}

	/**
	 * @return User
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @param User $user
	 */
	public function setUser($user): void {
		$this->user = $user;
	}


	public function getRequestID() {
		return $this->requestID;
	}

	public function setRequestID($id) : void {
		$this->requestID = $id;
	}

}
