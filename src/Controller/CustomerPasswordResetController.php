<?php

namespace App\Controller;

use Bolt\Controller\TwigAwareController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ResetPasswordRequestFormType;

class CustomerPasswordResetController extends TwigAwareController
{
    #[Route('{_locale}/reset-password', name: 'reset_password_nubai')]
    public function request(Request $request): Response
    {
        
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        }
        
        
        return $this->render('@theme/security/reset_password_request.twig', [
            'controller_name' => 'CustomerPasswordResetController',
            'requestForm' => $form->createView(),
        ]);
    }
}
