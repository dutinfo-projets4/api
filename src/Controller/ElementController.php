<?php

namespace App\Controller;

use App\Entity\Directory;
use App\Entity\Element;
use App\Entity\Token;
use App\Entity\User;
use App\Utils\LoginUtils;
use App\Utils\RequestUtils;
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
		$doctrine = $this->getDoctrine();
		$response = new Response();
		$response->setStatusCode(Response::HTTP_BAD_REQUEST);

		$token = LoginUtils::getToken($doctrine->getManager(), $request);

		if ($token) {
			if (RequestUtils::checkPOST($request, array('content'))) {
				$element = new Element();
				$parent = $request->get('parent_grp');
				$parent = !empty($parent) ? $doctrine->getRepository(Directory::class)->findOneBy(['id' => $parent]) : null;

				if ($parent != null)
					$element->setGroup($parent);

				$element->setContent($request->get('content'));
				$element->setLastUpdateTS(new \DateTime());

				$element->setUser($token->getUser());

				$this->getDoctrine()->getManager()->persist($element);
				$this->getDoctrine()->getManager()->flush();

				$response->setStatusCode(Response::HTTP_CREATED);
				$response->setContent(json_encode([
					'id' => $element->getID(),
				]));

			} elseif (RequestUtils::checkPUT($request, array('id', 'content'))) {

				$response->setStatusCode(Response::HTTP_NOT_FOUND);
				$elt = $doctrine->getRepository(Element::class)->findOneBy([
					'id' => $request->get('id'),
					'user' => $token->getUser()
				]);

				if ($elt != null) {

					$elt->setContent($request->get('content'));

					$parent = $request->get('parent_grp');
					$parent = !empty($parent) ? $doctrine->getRepository(Directory::class)->findOneBy([ 'id' => $parent ]) : null;
					if ($parent != null) {
						$elt->setGroup($parent);
					}

					$this->getDoctrine()->getManager()->persist($elt);
					$this->getDoctrine()->getManager()->flush();

					$response->setStatusCode(Response::HTTP_OK);
				}

			} elseif ($request->getMethod() == 'DELETE') {

				if (!empty($request->query->get('id'))) {

					$response->setStatusCode(Response::HTTP_NOT_FOUND);

					if (!is_null($this->getDoctrine()->getRepository(Element::class)->findBy([
						'id' => $request->query->get('id'),
						'user' => $token->getUser(),
					]))) {
						$element = $this->getDoctrine()->getRepository(Element::class)->findOneBy([
							'id' => $request->query->get('id'),
							'user' => $token->getUser(),
						]);
						$this->getDoctrine()->getManager()->remove($element);
						$this->getDoctrine()->getManager()->flush();

						$response->setStatusCode(Response::HTTP_GONE);

					}

				}

			}
		}

		return $response;
	}
}
