<?php

namespace App\Controller;

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

		if (RequestUtils::checkGET($request, array())){
			
		}

		return $response;
	}
}
