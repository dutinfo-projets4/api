<?php

namespace App\Controller;

use App\Entity\Challenge;
use App\Entity\Directory;
use App\Entity\Element;
use App\Entity\Token;
use App\Entity\User;
use App\Utils\RequestUtils;
use phpDocumentor\Reflection\Types\Array_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Constraints\Uuid;

class UserController extends Controller
{
	/**
	 * @Route("/users", name="user")
	 */
	public function index()
	{
		$request = Request::createFromGlobals();
		$doctrine = $this->getDoctrine();
		$response = new JsonResponse();
		$response->setStatusCode(Response::HTTP_BAD_REQUEST);

		if ($request->getMethod() == 'PUT' && !empty($request->get('email'))
			&& !empty($request->get('username')) && !empty($request->get('password'))
			&& !empty($request->get('machine_name')) && !empty($request->get('publickey'))){

			if($doctrine->getRepository(User::class)->findBy(['email' => $request->get('email'),]) == null
				&& $doctrine->getRepository(User::class)->findBy(['username' => $request->get('username')]) == null){

				$user  = new User($request);
				$token = new Token($user, $request);

				$doctrine->getManager()->persist($user);
				$doctrine->getManager()->persist($token);
				$doctrine->getManager()->flush();


				$response->setStatusCode(Response::HTTP_CREATED);
				$response->setData([
					"id" => $user->getId(),
					"token" => $token->getToken(),
				]);
			} else {
				$response->setStatusCode(Response::HTTP_CONFLICT);
			}
		} else if ($request->getMethod() == 'POST' && !empty($request->get('passcode'))
			&& !empty($request->get('machine_name')) && !empty($request->get('challenge'))) {

			$challenge = $doctrine->getRepository(Challenge::class)->find($request->get('challenge'));
			if ($challenge == null) return $response;

			$response->setStatusCode(Response::HTTP_FORBIDDEN);

			$userRepository = $doctrine->getRepository(User::class);

			$user = $userRepository->findAllByPass($request->get('passcode'), $challenge->getChallenge());
			if (count($user) > 0) {
				$user = $user[0];
				$token = new Token($user, $request);

				$groups = $doctrine->getRepository(Directory::class)->findBy([
					'user' => $user,
				]);

				$elements = $doctrine->getRepository(Element::class)->findBy([
					'user' => $user,
				]);

				$response->setStatusCode(Response::HTTP_OK);
				$response->setData([
					"id" => $user->getID(),
					"username" => $user->getUsername(),
					"email" => $user->getEmail(),
					"isAdmin" => $user->isAdmin(),
					"token" => $token->getToken(),
					"data" => [
						"groups" => $groups,
						"elements" => $elements,
					],
				]);
			}
		} else if ($request->getMethod() == 'GET' && !empty($request->get('limit')) && !empty($request->get('offset'))){

			$response->setStatusCode(Response::HTTP_FORBIDDEN);
			$current_token = $this->getDoctrine()->getRepository(Token::class)->findOneBy([
				'token' => $request->headers->get('X-ALOHOMORA-TOKEN')
			]);


			if($current_token->getUser()->isAdmin()){

				$users = $this->getDoctrine()->getRepository(User::class)->findBy([

				], null, $request->get('limit'), $request->get('offset'));

				$response->setStatusCode(Response::HTTP_OK);
				$response->setData([
					'users' => $users,
				]);

			}

		} else if ($request->getMethod() == 'PATCH'
			&& !empty($request->get('id')) && !empty($request->get('username'))
			&& !empty($request->get('email')) && !empty($request->get('isAdmin'))
			&& !empty($request->get('password'))){

			$response->setStatusCode(Response::HTTP_FORBIDDEN);
			$current_token = $this->getDoctrine()->getRepository(Token::class)->findOneBy([
				'token' => $request->headers->get('X-ALOHOMORA-TOKEN')
			]);

			if($current_token->getUser()->isAdmin()){

				$user = $this->getDoctrine()->getRepository(User::class)->find($request->get('id'));

				$user->setUsername($request->get('username'))
					->setEmail($request->get('email'))
					->setAdmin($request->get('isAdmin'))
					->setPassword($request->get('password'));

				$response->setStatusCode(Response::HTTP_OK);

			}

		} else if ($request->getMethod() == 'DELETE' && !empty($request->get('id'))){

			$response->setStatusCode(Response::HTTP_FORBIDDEN);
			$current_token = $this->getDoctrine()->getRepository(Token::class)->findOneBy([
				'token' => $request->headers->get('X-ALOHOMORA-TOKEN')
			]);

			if($current_token->getUser()->isAdmin() || $current_token->getUser()->getID() == $request->get('id')){

				$user = $this->getDoctrine()->getRepository(User::class)->find($request->get('id'));

				$this->getDoctrine()->getManager()->remove($user);
				$this->getDoctrine()->getManager()->flush();

				$response->setStatusCode(Response::HTTP_OK);

			}

		}

		return $response;
	}
}
