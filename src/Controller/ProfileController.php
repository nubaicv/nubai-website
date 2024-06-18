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

    #[Route('/{_locale}/my-profile/upload-profile-image', name: 'upload-image-profile_nubai')]
    public function uploadImage(Request $request, ProfileHelper $profileHelper, TranslatorInterface $translator) {


//        if (!$this->isGranted('ROLE_USER')) {
//
//            return $this->redirectToRoute('login_nubai');
//        }

        $method = $request->getMethod();
        switch ($method) {
            
            case 'GET':
                
                return new Response($translator->trans('no.image.file.data'), 200);
            case 'POST':
                
                $file = $request->files->get('profileimage');
                if ($file) {
                    
                    $email = $request->getSession()->get('_security.last_username');
                    $user = $this->em->getRepository(Customer::class)->findOneBy([
                        'email' => $email,
                    ]);
                    $profileHelper->saveImage($file, $user);
                    return new Response($translator->trans('profile.image.saved'), 200);
                } else {

                    return new Response($translator->trans('no.image.file.data'), 200);
                }
                break;
            default:
                
                return new Response($translator->trans('no.image.file.data'), 200);
        }
    }
}
