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
        if($request->getMethod() == 'POST'){

			if(!empty($request->get('parent_grp')) && !empty($request->get('content'))){
				$response->headers->set('Content-Type', 'application/json');

				$element = new Element();
				$group = $this->getDoctrine()->getRepository(Group::class)->find($request->get('parent_grp'));

				$element->setContent($request->get('content'));
				$element->setGroup($group);

				$this->getDoctrine()->getManager()->persist($element);
				$this->getDoctrine()->getManager()->flush();

				$response->setStatusCode(Response::HTTP_CREATED);
				$response->setContent(json_encode([
					'id' => $element->getID(),
				]));

			}

        }
        elseif ($request->getMethod() == 'PUT'){

        	$response->setStatusCode(Response::HTTP_NOT_FOUND);

        	if(!empty($request->get('parent_grp')) && !empty($request->get('content')) && !empty($request->get('id'))){

		        if(!is_null($this->getDoctrine()->getRepository(Element::class)->find($request->get('id')))){
					$element = $this->getDoctrine()->getRepository(Element::class)->find($request->get('id'));

			        $element->setContent($request->get('content'));
			        $element->setGroup($request->get('parent_grp'));

			        $this->getDoctrine()->getManager()->persist($element);
			        $this->getDoctrine()->getManager()->flush();

			        $response->setStatusCode(Response::HTTP_OK);

		        }

	        }

        }
        elseif($request->getMethod() == 'DELETE'){

        	if(!empty($request->get('id'))){

		        $response->setStatusCode(Response::HTTP_NOT_FOUND);

        		if(!is_null($this->getDoctrine()->getRepository(Element::class)->find($request->get('id')))){

        			$element = $this->getDoctrine()->getRepository(Element::class)->find($request->get('id'));
			        $this->getDoctrine()->getManager()->remove($element);
			        $this->getDoctrine()->getManager()->flush();

        			$response->setStatusCode(Response::HTTP_GONE);

		        }

	        }

        }

	    return $response;
    }
}
