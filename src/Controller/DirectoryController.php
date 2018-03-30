<?php

namespace App\Controller;

use App\Entity\Directory;
use App\Entity\Element;
use App\Entity\User;
use App\Utils\LoginUtils;
use App\Utils\RequestUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DirectoryController extends Controller {

	/**
	 * @Route("/directory", name="directory")
	 */
	public function index() {
		$request  = Request::createFromGlobals();
		$response = new JsonResponse();
		$response->setStatusCode(Response::HTTP_BAD_REQUEST);

		$doctrine = $this->getDoctrine();

		$token = LoginUtils::getToken($doctrine->getManager(), $request);

		if ($token != null) {
			if (RequestUtils::checkPOST($request, array('parent_grp', 'content'))) {
				$parent = $doctrine->getRepository(Directory::class)->find($request->get('parent_grp'));

				if ($parent != null) {
					$user = $token->getUser();

					$group = new Directory($user, $parent != null ? $parent : null, $request);

					$this->getDoctrine()->getManager()->persist($group);
					$this->getDoctrine()->getManager()->flush();

					$response->setStatusCode(Response::HTTP_CREATED);
					$response->setData([
						'id' => $group->getID(),
					]);
				} else {
					$response->setStatusCode(Response::HTTP_NOT_FOUND);
				}

			} else if (RequestUtils::checkPUT($request, array('id', 'content'))) {
				$response->setStatusCode(Response::HTTP_NOT_FOUND);

				$id = $request->get('id');
				$content = $request->get('content');
				$parent = $request->get('parent_grp');

				$group = null;

				if ($id == -1){
					$group = $doctrine->getRepository(Directory::class)->findOneBy([
						'user' => $token->getUser(),
						'id'   => null
					]);
				} else {
					$group = $doctrine->getRepository(Directory::class)->findOneBy([
						'user' => $token->getUser(),
						'id' => $id,
					]);
				}

				if ($group != null) {

					if (!empty($parent) && $group->getParentGroup()->getID() != $parent) {

						$newParent = $doctrine->getRepository(Directory::class)->findOneBy(['id' => $parent]);
						if ($newParent != null) {
							$group->setParentGroup($newParent);
						} else {
							$response->setStatusCode(Response::HTTP_NOT_FOUND);
							return $response;
						}

					}

					$group->setLastUpdateTS(new \DateTime());

					$group->setContent($request->get('content'));
					$group->setUser($token->getUser());

					$this->getDoctrine()->getManager()->persist($group);
					$this->getDoctrine()->getManager()->flush();

					$response->setData([
						'id' => $group->getID()
					]);

					$response->setStatusCode(Response::HTTP_OK);
				} else {
					$response->setStatusCode(Response::HTTP_NOT_FOUND);
				}


			} else if (RequestUtils::checkDELETE($request, array('id'))) {
				$response->setStatusCode(Response::HTTP_NOT_FOUND);

				$group = $doctrine->getRepository(Directory::class)->findOneBy(['id' => $request->get('id')]);

				if ($group != null) {
					if ($group->getUser() == $token->getUser()) {
						$this->getDoctrine()->getManager()->remove($group);
						$this->getDoctrine()->getManager()->flush();

						$response->setStatusCode(Response::HTTP_GONE);
					} else {
						$response->setStatusCode(Response::HTTP_FORBIDDEN);
					}
				}


			}
		}
		return $response;
	}
}
