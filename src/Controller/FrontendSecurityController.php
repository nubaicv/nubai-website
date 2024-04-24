<?php

namespace App\Controller;

use Bolt\Controller\TwigAwareController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class FrontendSecurityController extends TwigAwareController
{
    #[Route(path: '/{_locale}/login', name: 'login_nubai', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/{_locale}/logout', name: 'logout_nubai', methods: ['GET'])]
    public function logout(): void
    {
    }
}
