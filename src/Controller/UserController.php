<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use App\Repository\UserRepository;

class UserController extends AbstractController
{
    /**
     * @Route("/api/user/current", name="user")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
        $user = $this->getUser()->toJson();
        $res = [
            "user" => $user
        ];
        return new JsonResponse($res);
    }

    /**
    * @Route("/user/login", name="login_user", methods={"POST"})
     */
    public function login(Request $req, UserRepository $userRep, UserPasswordEncoderInterface $encoder): Response
    {

        $data = $req->toArray();

        $user = $userRep->findOneBy(["email" => $data["email"]]);

        $passwordValid = $encoder->isPasswordValid($user, $data["password"]);

        if(!$passwordValid) {
          $res = ["message" => "Wrong password"];
          $jsonRes = new JsonResponse($res);
          $jsonRed->setStatusCode(401);
          return $jsonRes;
        }

        $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
        $this->get("security.token_storage")->setToken($token);
        $this->get("session")->set("_security_main", serialize($token));

        $userJson = $user->toJson();
        $userJson["apikey"] = $user->getApikey();
        $res = [
            "user" => $userJson
        ];

        return new JsonResponse($res);

    }
}
