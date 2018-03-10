<?php

namespace App\Controller;

use App\Entity\Element;
use App\Entity\Group;
use App\Entity\Token;
use App\Entity\User;
use App\Utils\RequestUtils;
use phpDocumentor\Reflection\Types\Array_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
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
		$response = new JsonResponse();
		$response->setStatusCode(Response::HTTP_BAD_REQUEST);
		$token = new Token();

		if (RequestUtils::checkPUT($request, array('username', 'email', 'password', 'machine_name', 'publickey'))){

			if($this->getDoctrine()->getRepository(User::class)->findByEmail($request->query->get('email'))->findAll() == null
				&& $this->getDoctrine()->getRepository(User::class)->findByUsername($request->query->get('username'))->findAll() == null){

				$user = new User();

				$token->setIP($request->query->getClientIp());
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
			} else {
				$response->setStatusCode(Response::HTTP_CONFLICT);
			}
		} else if (RequestUtils::checkPOST($request, array('passcode', 'challenge', 'machine_name', 'publickey'))) {

			$response->setStatusCode(Response::HTTP_FORBIDDEN);

			if(!is_null($this->getDoctrine()->getRepository(User::class)->log_with_challenge($request->query->get('passcode'), $request->query->get('challenge'))->findAll())) {
				$user = $this->getDoctrine()->getRepository(User::class)
					->log_with_challenge($request->query->get('passcode'))
					->findAll();

				$token->setIP($request->query->getClientIp());
				$token->setMachineName($request->query->get('machine_name'));
				$token->setLastUpdateTS(new \DateTime(date('Y-m-d H:i:s')));

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
					"id" => $user->getID(),
					"username" => $user->getUsername(),
					"email" => $user->getEmail(),
					"isAdmin" => $user->isAdmin(),
					"token" => $token->getToken(),
					"data" => [
						"groups" => $groupArray,
						"elements" => $elementArray,
					],
				]);
			}
		} else if (RequestUtils::checkGET($request, ['limit', 'offset'])){

			$response->setStatusCode(Response::HTTP_FORBIDDEN);
			$current_token = $this->getDoctrine()->getRepository(Token::class)->findOneBy([
				'token' => $request->headers->get('token')
			]);

			if($current_token->getUser()->isAdmin()){

				$users = $this->getDoctrine()->getRepository(User::class)->findBy([

				], null, $request->query->get('limit'), $request->query->get('offset'));

				$response->setStatusCode(Response::HTTP_OK);
				$response->setData([
					'users' => $users,
				]);

			}

		} else if (RequestUtils::checkPATCH($request, ['id', 'username', 'email', 'isAdmin', 'password'])){

			$response->setStatusCode(Response::HTTP_FORBIDDEN);
			$current_token = $this->getDoctrine()->getRepository(Token::class)->findOneBy([
				'token' => $request->headers->get('token')
			]);

			if($current_token->getUser()->isAdmin()){

				$user = $this->getDoctrine()->getRepository(User::class)->find($request->query->get('id'));

				$user->setUsername($request->query->get('username'))
					->setEmail($request->query->get('email'))
					->setAdmin($request->query->get('isAdmin'))
					->setPassword($request->query->get('password'));

				$response->setStatusCode(Response::HTTP_OK);

			}

		} else if (RequestUtils::checkDELETE($request, ['id'])){

			$response->setStatusCode(Response::HTTP_FORBIDDEN);
			$current_token = $this->getDoctrine()->getRepository(Token::class)->findOneBy([
				'token' => $request->headers->get('token')
			]);

			if($current_token->getUser()->isAdmin() || $current_token->getUser()->getID() == $request->query->get('id')){

				$user = $this->getDoctrine()->getRepository(User::class)->find($request->query->get('id'));

				$this->getDoctrine()->getManager()->remove($user);
				$this->getDoctrine()->getManager()->flush();

				$response->setStatusCode(Response::HTTP_OK);

			}

		}

		return $response;
	}
}
