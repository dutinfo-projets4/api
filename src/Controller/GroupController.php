<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use App\Utils\RequestUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

		if (RequestUtils::checkPOST($request, array())){

			if(!empty($request->get('parent_grp')) && !empty($request->get('content')) && !empty($request->headers->get('Token'))){

				$group = new Group();

				if(!is_null($this->getDoctrine()->getRepository(Group::class)->find($request->get('parent_grp')))
				&& !is_null($this->getDoctrine()->getRepository(User::class)->find($request->headers->get('Token')))){

					$parent = $this->getDoctrine()->getRepository(Group::class)->find($request->get('parent_grp'));
					$user = $this->getDoctrine()->getRepository(User::class)->find($request->headers->get('Token'));

					$group->setContent($request->get('content'));
					$group->setParentGroup($parent);
					$group->setName($request->get('name'));
					$group->setUser($user);

					$this->getDoctrine()->getManager()->persist($group);
					$this->getDoctrine()->getManager()->flush();

					$response->setStatusCode(Response::HTTP_CREATED);
					$response->setData([
						'id' => $group->getID(),
					]);

				}

			}

		} else if (RequestUtils::checkPUT($request, array())){

			if(!empty($request->get('parent_grp')) && !empty($request->get('id'))
				&& !empty($request->get('content')) && !empty($request->get('name')) && !empty($request->headers->get('Token'))){
				$response->setStatusCode(Response::HTTP_NOT_FOUND);

				if(!is_null($this->getDoctrine()->getRepository(Group::class)->find($request->get('parent_grp')))
					&& !is_null($this->getDoctrine()->getRepository(User::class)->find($request->headers->get('Token')))){


					$parent = $this->getDoctrine()->getRepository(Group::class)->find($request->get('parent_grp'));
					$user = $this->getDoctrine()->getRepository(User::class)->find($request->headers->get('Token'));

					$group = $this->getDoctrine()->getRepository(Group::class)->findOneBy([
						'user' => $user,
						'parent' => $parent,
						'id' => $request->headers->get('id'),
					]);

					$group->setContent($request->get('content'));
					$group->setParentGroup($parent);
					$group->setName($request->get('name'));
					$group->setUser($user);

					$this->getDoctrine()->getManager()->persist($group);
					$this->getDoctrine()->getManager()->flush();

					$response->setStatusCode(Response::HTTP_OK);

				}

			}

		} else if (RequestUtils::checkDELETE($request, array())){

			if(!empty($request->get('id'))){

				$response->setStatusCode(Response::HTTP_NOT_FOUND);

				if(!empty($this->getDoctrine()->getRepository(Group::class)->find($request->get('id')))){

					$group = $this->getDoctrine()->getRepository(Group::class)->find($request->get('id'));

					$this->getDoctrine()->getManager()->remove($group);
					$this->getDoctrine()->getManager()->flush();

					$response->setStatusCode(Response::HTTP_GONE);

				}

			}

		}

		return $response;
	}
}
