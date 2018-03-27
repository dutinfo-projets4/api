<?php

namespace App\Utils;

class TokenManager {

	private static $manager;

	private $token;

	private function __construct() { }

	public static function getInstance() {
		if (TokenManager::$manager == null){
			TokenManager::$manager = new TokenManager();
		}

		return TokenManager::$manager;
	}

	public function getToken() {
		return $this->token;
	}

	public function setToken($tok) {
		$this->token = $tok;
	}

}
