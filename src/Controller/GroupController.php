<?php

namespace App\Controller;

use App\RequestUtils;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GroupController extends Controller {

	/**
	 * @Route("/group", name="group")
	 */
	public function index() {
		$request  = Request::createFromGlobals();
		$response = new JsonResponse();
		$response->setStatusCode(Response::HTTP_BAD_REQUEST);

		if (RequestUtils::checkPOST($rq, array())){

		} else if (RequestUtils::checkPUT($rq, array())){

		} else if (RequestUtils::checkDELETE($rq, array())){

		}

		return $response;
	}
}
