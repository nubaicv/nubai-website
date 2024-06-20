<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Helper\ProfileHelper;
use Bolt\Controller\TwigAwareController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
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

    #[Route('/{_locale}/my-profile/image', name: 'image-profile_nubai')]
    public function uploadImage(Request $request, ProfileHelper $profileHelper, TranslatorInterface $translator): Response {


        if (!$this->isGranted('ROLE_USER')) {

            return new Response($translator->trans('authentication.error'), 401);
        }

        $method = $request->getMethod();
        switch ($method) {
            
            case 'POST':
                
                // Tomamos o email do user desde a sessao para determinar o user (Customer)
                $email = $request->getSession()->get('_security.last_username');
                $user = $this->em->getRepository(Customer::class)->findOneBy([
                    'email' => $email,
                ]);
                // --------------------------------------------
                if ($user === null) {
                    return new Response($translator->trans('authentication.error'), 401);
                }

                $file = $request->files->get('profileimage');
                if (!$file) {
                    return new Response($translator->trans('no.image.file.data'), 400);
                }
                
                if (!$profileHelper->isValidImage($file)) {
                    return new Response($translator->trans('not.valid.image.file.data'), 400);
                }

                try {
                    
                    $profileHelper->saveImage($file, $user);
                    return new Response('profile.image.saved ' . $email, 200);
                } catch (\Exception $ex) {

                    return new Response($translator->trans($ex->getMessage()), 500);
                }
                break;
            default:

                return new Response($translator->trans('no.image.file.data'), 400);
        }
    }
}
