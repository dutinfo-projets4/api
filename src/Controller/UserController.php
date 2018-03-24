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

class UserController extends Controller {

	/**
	 * @Route("/users", name="user")
	 */
	public function index() {
		$request = Request::createFromGlobals();
		$doctrine = $this->getDoctrine();
		$response = new JsonResponse();
		$response->setStatusCode(Response::HTTP_BAD_REQUEST);

		// If the method is PUT, Registering the user
		if (RequestUtils::checkPUT($request, array('username', 'email', 'password', 'machine_name', 'public_key'))) {
			// Checks if the email or the username is already in the database
			$isRegistredEmail = count($doctrine->getRepository(User::class)->findByEmail($request->get('email')));
			$isRegistredUname = count($doctrine->getRepository(User::class)->findByUsername($request->get('username')));

			// If not, the used is allowed to register
			if($isRegistredEmail == 0 && $isRegistredUname == 0){
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
				// If so, the user gets a 409 error
				$response->setStatusCode(Response::HTTP_CONFLICT);
			}
		// If the method is POST, Login-in the user
		} else if (RequestUtils::checkPOST($request, array('passcode', 'challenge', 'machine_name', 'public_key'))) {

			// Getting the challenge that the user is using
			$challenge = $doctrine->getRepository(Challenge::class)->find($request->get('challenge'));
			if ($challenge == null) return $response;

			$response->setStatusCode(Response::HTTP_FORBIDDEN);
			$userRepository = $doctrine->getRepository(User::class);
			$user = $userRepository->findAllByPass($request->get('passcode'), $challenge->getChallenge());

			// The user is found, processing it
			if (count($user) > 0) {
				$user              = $user[0];
				$token             = new Token($user, $request);
				$groupsFormatted   = array();
				$elementsFormatted = array();

				// Getting all his groups and elements
				$groups = $doctrine->getRepository(Directory::class)->findBy([
					'user' => $user,
				]);

				$elements = $doctrine->getRepository(Element::class)->findBy([
					'user' => $user,
				]);

				// Parsing them as array to be send through the JsonResponse
				foreach($groups as $grp) {
					array_push($groupsFormatted, $grp->asArray());
				}

				foreach($elements as $elt) {
					array_push($elementsFormatted, $elt->asArray());
				}

				$doctrine->getManager()->persist($user);
				$doctrine->getManager()->persist($token);
				$doctrine->getManager()->flush();

				// Sending the request
				$response->setStatusCode(Response::HTTP_OK);
				$response->setData([
					"id" => $user->getID(),
					"username" => $user->getUsername(),
					"email" => $user->getEmail(),
					"isAdmin" => $user->isAdmin(),
					"token" => $token->getToken(),
					"data" => [
						"groups" => $groupsFormatted,
						"elements" => $elementsFormatted,
					],
				]);
			}
		} else if (RequestUtils::checkGET($request, array("limit", "offset"))){

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

		} else if (RequestUtils::checkPATCH($request, array('id', 'username', 'email'))) {
			$response->setStatusCode(Response::HTTP_FORBIDDEN);

			$user = $this->getDoctrine()->getRepository(User::class)->find($request->get('id'));

			$user->setUsername($request->get('username'))
			     ->setEmail($request->get('email'))
			     ->setAdmin($request->get('isAdmin'))
			     ->setPassword($request->get('password'));

			$response->setStatusCode(Response::HTTP_OK);

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
