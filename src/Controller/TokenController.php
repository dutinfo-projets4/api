<?php

namespace App\Controller;

use App\Entity\Token;
use App\Entity\User;
use App\Utils\RequestUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TokenController extends Controller
{
    /**
     * @Route("/token", name="token")
     */
    public function index()
    {

    	$request = Request::createFromGlobals();
    	$response = new JsonResponse();
    	$response->setStatusCode(Response::HTTP_BAD_REQUEST);

	    if($request->getMethod() == 'GET'){

			$current_token = $this->getDoctrine()->getRepository(Token::class)->findOneBy([
				'token' => $request->headers->get('token'),
			]);

			$response->setData([
				'tokens' => $current_token->getUser()->getTokens(),
			]);

	    }
	    elseif($request->getMethod() == 'DELETE'){

	    	$response->setStatusCode(Response::HTTP_FORBIDDEN);

			if(!is_null($this->getDoctrine()->getRepository(Token::class)->findOneBy([
				'token' => $request->headers->get('token'),
			]))) {

				$token = $this->getDoctrine()->getRepository(Token::class)->findOneBy([
					'token' => $request->headers->get('token'),
				]);

				$this->getDoctrine()->getManager()->remove($token);
				$this->getDoctrine()->getManager()->flush();

				$response->setStatusCode(Response::HTTP_GONE);

			}

	    }
	    elseif($request->getMethod() == 'PUT'){

		    $response->setStatusCode(Response::HTTP_FORBIDDEN);

		    if(!is_null($this->getDoctrine()->getRepository(Token::class)->findOneBy([
			    'token' => $request->headers->get('token'),
		    ]))) {

			    $token = $this->getDoctrine()->getRepository(Token::class)->findOneBy([
				    'token' => $request->headers->get('token'),
			    ]);

			    $response->setStatusCode(Response::HTTP_OK);
			    $response->setData([
			    	'IP' => $token->getIP(),
				    'loginTS' => $token->getLoginTS(),
			    ]);

		    }

	    }

    }
}
