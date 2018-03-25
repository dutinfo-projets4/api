<?php

namespace App\Event;


use App\Utils\LoginUtils;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Token;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class EventSignature {

	/** @var EntityManager $em */
	private $em;

	public function __construct(ContainerInterface $container) {
		$doc = $container->get('doctrine');
		$this->em = $doc->getEntityManager();
	}

	/**
	 * List of routes that do not need signature checking
	 */
	public static $IGNORE_REQUEST = array(
		"challenge" => [ 'GET' ],
		"users"     => [ 'PUT', 'POST' ]
	);

	/**
	 * This event hook will check every request
	 * The goal of this method is to check the signature that the client sent
	 */
	public function onKernelRequest(GetResponseEvent $gre)
	{
		if (!$gre->isMasterRequest())
			return;

		// First we get some infos about the request
		$request = $gre->getRequest();
		$currRoute = $request->get('_route');
		$currMethod = $request->getMethod();

		// Then we check if the route is registered in the ignore list
		if (array_key_exists($currRoute, EventSignature::$IGNORE_REQUEST)) {
			// If so, we then check if the method used is also in the ignore list
			foreach (EventSignature::$IGNORE_REQUEST[$currRoute] as $ignMethod) {
				// If that's the case, we should not check the integrity of the request
				if ($currMethod == $ignMethod)
					return;
			}
		}

		$token     = $request->headers->get('x-alohomora-token');
		$signature = $request->headers->get('x-alohomora-signature');
		$reqID     = $request->get('req_id');

		// If the token or the signature is not set, do not awnser the request and simply send a bad request
		if (empty($token) || empty($signature) || empty($reqID)) {
			$gre->setResponse((new Response())->setStatusCode(Response::HTTP_BAD_REQUEST));
			return;
		}

		/** @var Token $token */
		$token = LoginUtils::getToken($this->em, $request);

		// If the user is auth'ed and is valid
		if ($token != null) {
			$pubkey = $token->getPublicKey();

			// Generate the data
			$data = "";

			if (true/*openssl_verify($data, $signature, $pubkey)*/) {

				// The request is allowed to be processed
				if ($reqID > $token->getRequestID()) {

					// Increasing the request id to the given value
					// If the client skip some number for any reason, that won't make a security issue where the id is still lower
					$token->setRequestID($reqID);
					$this->em->persist($token);
					$this->em->flush();

					/**
					 * @TODO: Passer le token au controlleur pour éviter d'aller le chercher deux fois tout le temps
					 */

					return;
				} else {
					// The request has already been processed or the ID used is an older one
					$gre->setResponse((new JsonResponse())->setStatusCode(Response::HTTP_ALREADY_REPORTED)->setData(['req_id' => $token->getRequestID()]));
				}

			} else {
				$gre->setResponse((new Response())->setStatusCode(Response::HTTP_EXPECTATION_FAILED));
			}

		} else {
			$gre->setResponse((new Response())->setStatusCode(Response::HTTP_FORBIDDEN));
		}

	}

}