<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\RegistrationFormType;
use App\Repository\CustomerRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Bolt\Controller\TwigAwareController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends TwigAwareController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/{_locale}/register', name: 'register_nubai', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        
        if ($this->isGranted('ROLE_USER')) {
            
            return $this->redirectToRoute('homepage_locale');
        }
        
        $user = new Customer();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('verify_email_nubai', $user,
                (new TemplatedEmail())
                    ->from(new Address('website@nubai.com.cv', 'Nubai Website Bot'))
                    ->to($user->getEmail())
                    ->subject($translator->trans('please.confirm.your.email'))
                    ->htmlTemplate('@theme/security/email/confirmation_email.twig')
            );
            // do anything else you need here, like send an email
            
            $this->addFlash('success', 'resgistered.confirmation.email.sent');

            return $this->redirectToRoute('login_nubai');
        }

        return $this->render('security/register.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/{_locale}/verify/email', name: 'verify_email_nubai', methods: ['GET'])]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, CustomerRepository $customerRepository): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute('register_nubai');
        }

        $user = $customerRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('register_nubai');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('register_nubai');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'your.email.address.has.been.verified');

        return $this->redirectToRoute('login_nubai');
    }
}
