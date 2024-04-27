<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\ResetPasswordRequestFormType;
use Bolt\Controller\TwigAwareController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomerPasswordResetController extends TwigAwareController {

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {

        $this->em = $em;
    }

    #[Route('{_locale}/reset-password', name: 'reset_password_nubai', methods: ['GET', 'POST'])]
    public function request(Request $request, MailerInterface $mailer, TranslatorInterface $translator): Response {

        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                            $form->get('email')->getData(),
                            $mailer,
                            $translator
            );
        }


        return $this->render('@theme/security/reset_password_request.twig', [
                    'controller_name' => 'CustomerPasswordResetController',
                    'requestForm' => $form->createView(),
        ]);
    }

    #[Route('{_locale}/check-reset-password-email', name: 'check_reset_email_nubai', methods: ['GET'])]
    public function checkResetEmail(): Response {

        return $this->render('@theme/security/check_reset_email.twig', [
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer, TranslatorInterface $translator): RedirectResponse {

        $user = $this->em->getRepository(Customer::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->redirectToRoute('check_reset_email_nubai');
        }

        $email = (new TemplatedEmail())
                ->from(new Address('website@nubai.com.cv', 'Nubai Mail Bot'))
                ->to($user->getEmail())
                ->subject('Your password reset request')
                ->htmlTemplate('@theme/security/email/confirmation_reset.twig')
                ->context([
                    'number' => 14,
                ])
        ;
        
        $mailer->send($email);

        //redirect when user found
        return $this->redirectToRoute('check_reset_email_nubai');
    }
}
