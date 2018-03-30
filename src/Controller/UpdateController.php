<?php

namespace App\Controller;

use App\Entity\Directory;
use App\Entity\Element;
use App\Entity\User;
use App\Entity\Token;
use App\Utils\LoginUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UpdateController extends Controller
{
	/**
	 * @Route("/update", name="update")
	 */
	public function index() {
		$request  = Request::createFromGlobals();
		$response = new JsonResponse();
		$response->setStatusCode(Response::HTTP_BAD_REQUEST);

		$em = $this->getDoctrine()->getManager();

		$token = LoginUtils::getToken($em, $request);

		if ($token != null) {

				$user = $token->getUser();

				$group = $this->getDoctrine()->getRepository(Directory::class)->findBy([
					'user' => $user
				]);

				$elements = $this->getDoctrine()->getRepository(Element::class)->findBy([
					'user' => $user
				]);

				$groupArray   = array();
				$elementArray = array();

				foreach ($group as $g){
					// @TODO: Faire ça avec Doctrine
					if ($token->getLastUpdateTS() < $g->getLastUpdateTS()) {
						array_push($groupArray, $g->asArray());
					}
				}

				foreach ($elements as $e){
					// @TODO: Faire ça avec Doctrine
					if ($token->getLastUpdateTS() < $e->getLastUpdateTS()) {
						array_push($elementArray, $e->asArray());
					}
				}

				$response->setStatusCode(Response::HTTP_OK);
				$response->setData([
					"groups" => $groupArray,
					"elements" => $elementArray,
				]);

				$token->setLastUpdateTS(new \DateTime());
				$em->flush();

		} else {
			$response->setStatusCode(Response::HTTP_FORBIDDEN);
		}

		return $response;
	}
}
