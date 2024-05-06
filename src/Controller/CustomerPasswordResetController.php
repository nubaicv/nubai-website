<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Helper\CustomerPasswordResetHelper;
use Bolt\Controller\TwigAwareController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomerPasswordResetController extends TwigAwareController {

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {

        $this->em = $em;
    }

    #[Route('{_locale}/reset-password', name: 'reset_password_nubai', methods: ['GET', 'POST'])]
    public function request(Request $request, MailerInterface $mailer, TranslatorInterface $translator, CustomerPasswordResetHelper $resetPasswordHelper): Response {
        
        if ($this->isGranted('ROLE_USER')) {
            
            return $this->redirectToRoute('homepage_locale');
        }

        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            return $this->processSendingPasswordResetEmail(
                            $form->get('email')->getData(),
                            $resetPasswordHelper,
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

    #[Route('{_locale}/reset/{token}', name: 'reset_token_nubai', methods: ['GET', 'POST'])]
    public function reset(Request $request, CustomerPasswordResetHelper $resetPasswordHelper, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator, string $token = null): Response {

        if ($token) {

            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $request->getSession()->set('resetToken', $token);
            return $this->redirectToRoute('reset_token_nubai');
        }

        $token = $request->getSession()->get('resetToken');

        if (null === $token) {

            $this->addFlash('error', 'no.reset.password.token.found');
            return $this->redirectToRoute('reset_password_nubai');
        }

        //Verificar aqui a igualdade dos dois tokens
        try {

            if (!$user = $resetPasswordHelper->validateTokenAndFetchUser($token)) {

                $this->addFlash('error', 'no.reset.password.token.found');
                return $this->redirectToRoute('reset_password_nubai');
            }
        } catch (\Exception $ex) {

            $this->addFlash('error', $ex->getMessage());
            return $this->redirectToRoute('reset_password_nubai');
        }


        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $resetPasswordHelper->removeResetRequest($token);

            // Encode(hash) the plain password, and set it.
            $encodedPassword = $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->em->flush();

            $request->getSession()->remove('resetToken');
            $this->addFlash('success', 'password.changed');
            return $this->redirectToRoute('login_nubai');
        }

        return $this->render('@theme/security/reset_password.twig', [
                    'resetForm' => $form->createView(),
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, CustomerPasswordResetHelper $resetPasswordHelper, MailerInterface $mailer, TranslatorInterface $translator): RedirectResponse {

        $user = $this->em->getRepository(Customer::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->redirectToRoute('check_reset_email_nubai');
        }

        try {

            $resetToken = $resetPasswordHelper->generateResetToken($user);
        } catch (\Exception $ex) {

            //isto aqui nao e ideal porque expoe se email existe no BD.
            //Em production enviamos um email para admin e reencaminhamos para
            //route 'check_reset_email_nubai' ou enviamos um email para customers cadastrados
            //silenciosamente explicando o erro.
            $this->addFlash('error', $ex->getMessage());
            return $this->redirectToRoute('reset_password_nubai');
        }

        $email = (new TemplatedEmail())
                ->from(new Address('website@nubai.com.cv', 'Nubai Mail Bot'))
                ->to($user->getEmail())
                ->subject($translator->trans('your.password.reset.request'))
                ->htmlTemplate('@theme/security/email/confirmation_reset.twig')
                ->context([
            'resetToken' => $resetToken
                ])
        ;

//        $mailer->send($email);
        //redirect when user found
        return $this->redirectToRoute('check_reset_email_nubai');
    }
}
