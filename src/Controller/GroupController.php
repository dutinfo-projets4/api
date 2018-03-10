<?php

namespace App\Controller;

use App\Entity\Element;
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

		if (RequestUtils::checkPOST($request, array('parent_grp', 'content', 'name'))){

			$group = new Group();

			if(!is_null($this->getDoctrine()->getRepository(Group::class)->find($request->query->get('parent_grp')))
			&& !is_null($this->getDoctrine()->getRepository(User::class)->find($request->headers->get('token')))){

				$parent = $this->getDoctrine()->getRepository(Group::class)->find($request->query->get('parent_grp'));
				$user = $this->getDoctrine()->getRepository(User::class)->find($request->headers->get('token'));

				$group->setContent($request->query->get('content'));
				$group->setParentGroup($parent);
				$group->setName($request->query->get('name'));
				$group->setUser($user);

				$this->getDoctrine()->getManager()->persist($group);
				$this->getDoctrine()->getManager()->flush();

				$response->setStatusCode(Response::HTTP_CREATED);
				$response->setData([
					'id' => $group->getID(),
				]);

			}

		} else if (RequestUtils::checkPUT($request, array('parent_grp', 'id', 'name', 'content'))){

			$response->setStatusCode(Response::HTTP_NOT_FOUND);

			if(!is_null($this->getDoctrine()->getRepository(Group::class)->find($request->query->get('parent_grp')))
				&& !is_null($this->getDoctrine()->getRepository(User::class)->find($request->headers->get('token')))){


				$parent = $this->getDoctrine()->getRepository(Group::class)->find($request->query->get('parent_grp'));
				$user = $this->getDoctrine()->getRepository(User::class)->find($request->headers->get('token'));

				$group = $this->getDoctrine()->getRepository(Group::class)->findOneBy([
					'user' => $user,
					'parent' => $parent,
					'id' => $request->headers->get('id'),
				]);

				$group->setContent($request->query->get('content'));
				$group->setParentGroup($parent);
				$group->setName($request->query->get('name'));
				$group->setUser($user);

				$this->getDoctrine()->getManager()->persist($group);
				$this->getDoctrine()->getManager()->flush();

				$response->setStatusCode(Response::HTTP_OK);

			}

		} else if (RequestUtils::checkDELETE($request, array('id'))){

			$response->setStatusCode(Response::HTTP_NOT_FOUND);

			if(!empty($this->getDoctrine()->getRepository(Group::class)->find($request->query->get('id')))){

				$group = $this->getDoctrine()->getRepository(Group::class)->find($request->query->get('id'));

				$elements = $this->getDoctrine()->getRepository(Element::class)->findByGroup($group);

				$this->getDoctrine()->getManager()->remove($group);
				$this->getDoctrine()->getManager()->flush();

				$response->setStatusCode(Response::HTTP_GONE);

				$response->setData([
					'deleted' => $elements,
				]);

			}

		}

		return $response;
	}
}
