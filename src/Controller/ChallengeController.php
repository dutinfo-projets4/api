<?php

namespace App\Controller;

use App\Entity\Challenge;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ChallengeController extends Controller
{
	/**
	 * @Route("/challenge", name="challenge")
	 */
	public function index()
	{

		$string_alphanumeric = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
		$randomString = "";

		for($i = 0; $i < 255; $i++){
			$randomString .= $string_alphanumeric[random_int(0, strlen($string_alphanumeric))];
		}

		$this->getDoctrine()->getManager()->persist((new Challenge())->setChallenge($randomString));
		$this->getDoctrine()->getManager()->flush();

		return (new JsonResponse())->setData([
			"id" => $challenge->getId(),
			"challenge" => $challenge->getChallenge(),
		]);

	}
}
