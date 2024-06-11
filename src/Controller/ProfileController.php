<?php

namespace App\Controller;

use Bolt\Controller\TwigAwareController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends TwigAwareController {
    
    #[Route('/{_locale}/my-profile', name: 'profile_nubai', methods: ['GET', 'POST'])]
    public function index(): Response {
        
        if (!$this->isGranted('ROLE_USER')) {

            return $this->redirectToRoute('login_nubai');
        }
        
        return $this->render('@theme/profile.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }
}
