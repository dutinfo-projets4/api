<?php

namespace App\Controller;

use App\Entity\Config;
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

        if(RequestUtils::checkGET($request, [])){

	        $user = $this->getDoctrine()->getRepository(User::class)->find($request->headers->get('token'));

	        if($user->isAdmin()){

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
        elseif (RequestUtils::checkPUT($request, ['register_captcha', 'limit_update', 'public_register', 'api_registering'])){

	        $user = $this->getDoctrine()->getRepository(User::class)->find($request->headers->get('token'));

	        if($user->isAdmin()){

		        $config = new Config();

		        $config->setCaptcha($request->query->get('register_captcha'))
			        ->setLimit($request->query->get('limit_update'))
			        ->setPublic($request->query->get('public_register'))
			        ->setApi($request->query->get('api_registering'));

		        $this->getDoctrine()->getManager()->persist($config);
		        $this->getDoctrine()->getManager()->flush();

	        }

        }

        return $response;
    }
}
