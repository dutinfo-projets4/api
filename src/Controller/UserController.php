<?php

namespace App\Controller;

use App\Entity\Token;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    /**
     * @Route("/users", name="user")
     */
    public function index()
    {
        $request = Request::createFromGlobals();
        $response = new Response();
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);

		if($request->getMethod() == 'PUT' && !empty($request->query->get('username')) && !empty($request->query->get('email')) && !empty($request->query->get('password')) && !empty($request->query->get('machine_name')) && !empty($request->query->get('publickey'))) {

            if($this->getDoctrine()->getRepository(User::class)->findByEmail($request->query->get('email'))->findAll() == null
                && $this->getDoctrine()->getRepository(User::class)->findByUsername($request->query->get('username'))->findAll() == null){

				$user = new User();
				$token = new Token();

				$token->setIp($request->getClientIp());
				$token->setMachineName($request->query->get('machine_name'));
				$token->setLastUpdateTS(new \DateTime(date('Y-m-d H:i:s')));

				$this->getDoctrine()->getManager()->persist($token);
				$this->getDoctrine()->getManager()->flush();

				$user->setEmail($request->query->get('email'));
				$user->setPassword($request->query->get('password'));
				$user->setTokens($request->query->get($token));
				$user->setUsername($request->query->get('username'));

				$this->getDoctrine()->getManager()->persist($user);
				$this->getDoctrine()->getManager()->flush();
                $response->setStatusCode(Response::HTTP_CREATED);
                $response->setContent(json_encode([
                    "id" => $user->getId(),
                    "token" => $token->getToken(),
                ]));
                $response->headers->set('Content-Type', 'application/json');

			}
			else{
				$response->setStatusCode(Response::HTTP_CONFLICT);
			}

		}
        else if($request->getMethod() == 'POST' && !empty($request->query->get('passcode')) && !empty($request->query->get('challenge'))
            && !empty($request->query->get('machine_name')) && !empty($request->query->get('publickey'))){

            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            if(!is_null($this->getDoctrine()->getRepository(User::class)->log_with_challenge($request->get('passcode'), $request->get('challenge'))->findAll())){
                $user = $this->getDoctrine()->getRepository(User::class)
                    ->log_with_challenge($request->get('passcode'))
                    ->findAll();

                $response->setStatusCode(Response::HTTP_OK);

            }

        }

        $response->send();

    }
}
