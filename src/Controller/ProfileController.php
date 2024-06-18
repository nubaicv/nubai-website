<?php

namespace App\Controller;

use App\Entity\Customer;
use Bolt\Controller\TwigAwareController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

    #[Route('/{_locale}/my-profile/upload-profile-image', name: 'upload-image-profile_nubai', methods: ['GET', 'POST', 'DELETE'])]
    public function uploadImage(Request $request): Response {


//        if (!$this->isGranted('ROLE_USER')) {
//
//            return $this->redirectToRoute('login_nubai');
//        }

        $method = $request->getMethod();
        switch ($method) {

            case 'GET':
                $data = $request->query->all();
                break;
            case 'POST':
//                return new Response('Ok found', 200);
                $file = $request->files->get('profileimage');
                if (!$file) {
                    
                    return new Response('No file data', 200);
                } else {
                    
                    $destination = $this->getParameter('kernel.project_dir') . '/public/profile_images';
                    $filename = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
                    
                    $file->move($destination, $filename);
                    return new Response('Image saved!', 200);
                }
                break;
            case 'DELETE':
                $data = 'Temos DELETE';
                break;
        }

//        $email = $request->getSession()->get('_security.last_username');
//        $user = $this->em->getRepository(Customer::class)->findOneBy([
//            'email' => $email,
//        ]);

        return $this->render('@theme/profilee.twig', [
                    'data' => $data,
        ]);
    }
}
