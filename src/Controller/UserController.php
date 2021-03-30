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
     * @Route("/user/current", name="user")
     */
    public function index(Request $request, UserRepository $ur): Response
    {
        $user = $this->getUser();
        $hasToken = $request->headers->has('X-AUTH-TOKEN');
        if (!$user && $hasToken) {
            $token = $request->headers->get('X-AUTH-TOKEN');
            $user = $ur->loadUserByApiKey($token);
        }
        if (!$user) {
          $body = [
              "error" => "No user found."
          ];
          $res = new JsonResponse($body);
          $res->setStatusCode(401);
          return $res;
        }
        $userJson = $user->toJson();
        $userJson["apikey"] = $user->getApikey();
        $res = [
            "user" => $userJson
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
        if(!$user) {
          $res = ["message" => "User ". $data["email"] . " not found"];
          $jsonRes = new JsonResponse($res);
          $jsonRes->setStatusCode(404);
          return $jsonRes;
        }

        $passwordValid = $encoder->isPasswordValid($user, $data["password"]);

        if(!$passwordValid) {
          $res = ["message" => "Wrong password"];
          $jsonRes = new JsonResponse($res);
          $jsonRes->setStatusCode(401);
          return $jsonRes;
        }

        $isVerified = $user->getIsVerified();
        if (!$isVerified) {
          $res = ["message" => "Account is not verified, check your emails."];
          $jsonRes = new JsonResponse($res);
          $jsonRes->setStatusCode(401);
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
