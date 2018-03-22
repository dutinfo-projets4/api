<?php

namespace App\Controller;

use App\Entity\Config;
use App\Entity\Token;
use App\Entity\User;
use App\Utils\RequestUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ConfigController extends Controller
{
    /**
     * @Route("/config", name="config")
     */
    public function index()
    {
    	$request = Request::createFromGlobals();
    	$response = new JsonResponse();
    	$response->setStatusCode(Response::HTTP_FORBIDDEN);

        if($request->getMethod() == 'GET'){

	        $current_token = $this->getDoctrine()->getRepository(Token::class)->findBy([
	        	'token' => $request->headers->get('token'),
	        ]);

	        if($current_token->getUser()->isAdmin()){

		        $config = $this->getDoctrine()->getRepository(Config::class)->find(1);

		        $response->setStatusCode(Response::HTTP_OK);
		        $response->setData([
			        'register_captcha' => $config->getCaptcha(),
		        	'limit_update' => $config->getLimit(),
		        	'public_register' => $config->getPublic(),
		        	'api_registering' => $config->getApi(),
		        ]);

	        }

        }
        elseif ($request->getMethod() == 'PUT' && !empty($request->get('register_captcha'))
	        && !empty($request->get('limit_update')) && !empty($request->get('public_register'))
	        && !empty($request->get('api_registering'))){

	        $current_token = $this->getDoctrine()->getRepository(Token::class)->findBy([
		        'token' => $request->headers->get('token'),
	        ]);

	        if($current_token->getUser()->isAdmin()){

		        $config = new Config();

		        $config->setCaptcha($request->query->get('register_captcha'));
		        $config->setLimit($request->query->get('limit_update'));
		        $config->setPublic($request->query->get('public_register'));
		        $config->setApi($request->query->get('api_registering'));

		        $this->getDoctrine()->getManager()->persist($config);
		        $this->getDoctrine()->getManager()->flush();

	        }

        }

        return $response;
    }
}
