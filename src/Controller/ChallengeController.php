<?php

namespace App\Controller;

use App\Entity\Challenge;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ChallengeController extends Controller
{
    /**
     * @Route("/challenge", name="challenge")
     */
    public function index()
    {

        $string_alphanumeric = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $random_string = "";

        for($i = 0; $i < 255; $i++){
            $random_string .= $string_alphanumeric[random_int(0, strlen($string_alphanumeric))];
        }

        $challenge = new Challenge();
        $challenge->setChallenge($random_string);

        $this->getDoctrine()->getManager()->persist($challenge);
        $this->getDoctrine()->getManager()->flush();

        $response = new JsonResponse();
        $response->setData([
            "id" => $challenge->getId(),
            "challenge" => $challenge->getChallenge(),
        ]);

        return $response;

    }
}
