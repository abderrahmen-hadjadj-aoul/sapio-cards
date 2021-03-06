<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
          'last_username' => $lastUsername,
          'error' => $error
        ]);
    }

    /**
     * @Route("/api-logout", name="app_api_logout")
     */
    public function apiLogout(Request $request)
    {
      // https://stackoverflow.com/questions/28827418/log-user-out-in-symfony-2-application-when-remember-me-is-enabled/28828377#28828377
      $this->get('security.token_storage')->setToken(null);

      // Invalidating the session.
      $session = $request->getSession();
      $session->invalidate();

      return new JsonResponse([ "message" => "You are logged out" ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

}
