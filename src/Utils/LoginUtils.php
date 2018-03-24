<?php

namespace App\Utils;

use App\Entity\Token;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

class LoginUtils {

	/**
	 * Given the request, either the token instance or null will be returned
	 * @param $request
	 */
	public static function getToken(ObjectManager $doc, Request $request) {
		$token = null;
		$givenToken = $request->headers->get('x-alohomora-token');

		if (!empty($givenToken)){
			$token = $doc->getRepository(Token::class)->findBy([ 'token' => $givenToken]);
			if (count($token) > 0) $token = $token[0];
			else $token = null;
		}

		return $token;
	}

}
