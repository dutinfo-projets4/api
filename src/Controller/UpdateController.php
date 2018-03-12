<?php

namespace App\Controller;

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

		$current_token = $this->getDoctrine()->getRepository(User::class)->findBy([
			'token' => $request->headers->get('token'),
		]);
		$user = $current_token->getUser();

		if(!is_null($user)) {
			$group = $this->getDoctrine()->getRepository(Group::class)->findBy([
				'user' => $user,
			]);

			$elements = $this->getDoctrine()->getRepository(Element::class)->findBy([
				'user' => $user,
			]);

			$groupArray   = array();
			$elementArray = array();

			foreach ($group as $g){
				array_push($groupArray, $g);
			}

			foreach ($elements as $e){
				array_push($elementArray, $e);
			}

			$response->setStatusCode(Response::HTTP_OK);
			$response->setContent([
				"groups" => $groupArray,
				"elements" => $elementArray,
			]);
		}

		return $response;
	}
}
