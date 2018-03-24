<?php

namespace App\Controller;

use App\Entity\Directory;
use App\Entity\Element;
use App\Entity\User;
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

		$current_token = $this->getDoctrine()->getRepository(User::class)->findBy([
			'token' => $request->headers->get('X-ALOHOMORA-TOKEN'),
		]);

		if (RequestUtils::checkPOST($request, array('parent_grp', 'content'))){

			$parent = $doctrine->getRepository(Directory::class)->find($request->get('parent_grp'));
			$user = $current_token->getUser();

			if($user != null){

				$group = new Directory($user, count($parent) > 0 ? $parent[0] : null, $request);

				$this->getDoctrine()->getManager()->persist($group);
				$this->getDoctrine()->getManager()->flush();

				$response->setStatusCode(Response::HTTP_CREATED);
				$response->setData([
					'id' => $group->getID(),
				]);

			}

		} else if ($request->getMethod() == 'PUT' && !empty($request->get('parent_grp')) && !empty($request->get('id'))
			&& !empty($request->get('name')) && !empty($request->get('content'))){

			$response->setStatusCode(Response::HTTP_NOT_FOUND);

			if(!is_null($this->getDoctrine()->getRepository(Directory::class)->find($request->query->get('parent_grp')))
				&& !is_null($current_token->getUser())){


				$parent = $this->getDoctrine()->getRepository(Directory::class)->find($request->query->get('parent_grp'));
				$user = $current_token->getUser();

				$group = $this->getDoctrine()->getRepository(Directory::class)->findOneBy([
					'user' => $user,
					'parent' => $parent,
					'id' => $request->headers->get('id'),
				]);

				$group->setContent($request->query->get('content'));
				$group->setParentDirectory($parent);
				$group->setName($request->query->get('name'));
				$group->setUser($user);

				$this->getDoctrine()->getManager()->persist($group);
				$this->getDoctrine()->getManager()->flush();

				$response->setStatusCode(Response::HTTP_OK);

			}

		} else if ($request->getMethod() == 'DELETE' && !empty($request->get('id'))){

			$response->setStatusCode(Response::HTTP_NOT_FOUND);

			if(!empty($this->getDoctrine()->getRepository(Directory::class)->find($request->query->get('id')))){

				$group = $this->getDoctrine()->getRepository(Directory::class)->find($request->query->get('id'));

				$elements = $this->getDoctrine()->getRepository(Element::class)->findBy([
					'group' => $group,
				]);

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
