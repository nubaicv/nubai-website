<?php

namespace App\Controller;

use App\Entity\Customer;
use Bolt\Controller\TwigAwareController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ProfileController extends TwigAwareController {

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {

        $this->em = $em;
    }

    #[Route('/{_locale}/my-profile', name: 'profile_nubai', methods: ['GET', 'POST'])]
    public function index(): Response {

//        if (!$this->isGranted('ROLE_USER')) {
//
//            return $this->redirectToRoute('login_nubai');
//        }

        return $this->render('@theme/profile.twig', [
                    'controller_name' => 'ProfileController',
        ]);
    }

    #[Route('/{_locale}/my-profile/image', name: 'imageprofile_nubai', methods: ['GET', 'POST', 'DELETE'])]
    public function uploadImage(Request $request): Response {


//        if (!$this->isGranted('ROLE_USER')) {
//
//            return $this->redirectToRoute('login_nubai');
//        }

        $email = $request->getSession()->get('_security.last_username');
        $user = $this->em->getRepository(Customer::class)->findOneBy([
            'email' => $email,
        ]);

        return $this->render('@theme/profilee.twig', [
                    'data' => $user,
        ]);
    }
}
