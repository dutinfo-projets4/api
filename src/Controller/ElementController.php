<?php

namespace App\Controller;

use App\Entity\Element;
use App\Entity\Group;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ElementController extends Controller
{
	/**
	 * @Route("/element", name="element")
	 */
	public function index()
	{
		$request = Request::createFromGlobals();
		$response = new Response();
		$response->setStatusCode(Response::HTTP_BAD_REQUEST);
		if($request->query->getMethod() == 'POST'){

			if(!empty($request->query->get('parent_grp')) && !empty($request->query->get('content'))){
				$response->headers->set('Content-Type', 'application/json');

				$element = new Element();
				$group = $this->getDoctrine()->getRepository(Group::class)->find($request->query->get('parent_grp'));

				$element->setContent($request->query->get('content'));
				$element->setGroup($group);

				$this->getDoctrine()->getManager()->persist($element);
				$this->getDoctrine()->getManager()->flush();

				$response->setStatusCode(Response::HTTP_CREATED);
				$response->setContent(json_encode([
					'id' => $element->getID(),
				]));

			}

		}
		elseif ($request->query->getMethod() == 'PUT'){

			$response->setStatusCode(Response::HTTP_NOT_FOUND);

			if(!empty($request->query->get('parent_grp')) && !empty($request->query->get('content')) && !empty($request->query->get('id'))){

				if(!is_null($this->getDoctrine()->getRepository(Element::class)->find($request->query->get('id')))){
					$element = $this->getDoctrine()->getRepository(Element::class)->find($request->query->get('id'));

					$element->setContent($request->query->get('content'));
					$element->setGroup($request->query->get('parent_grp'));

					$this->getDoctrine()->getManager()->persist($element);
					$this->getDoctrine()->getManager()->flush();

					$response->setStatusCode(Response::HTTP_OK);

				}

			}

		}
		elseif($request->query->getMethod() == 'DELETE'){

			if(!empty($request->query->get('id'))){

				$response->setStatusCode(Response::HTTP_NOT_FOUND);

				if(!is_null($this->getDoctrine()->getRepository(Element::class)->find($request->query->get('id')))){

					$element = $this->getDoctrine()->getRepository(Element::class)->find($request->query->get('id'));
					$this->getDoctrine()->getManager()->remove($element);
					$this->getDoctrine()->getManager()->flush();

					$response->setStatusCode(Response::HTTP_GONE);

				}

			}

		}

		return $response;
	}
}
