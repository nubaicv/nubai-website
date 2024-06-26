<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Gregwar\CaptchaBundle\Type\CaptchaType;

class ResetPasswordRequestFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
                ->add('email', EmailType::class, [
                    'constraints' => [
                        new Email([
                            'message' => 'not.valid.email.address',
                        ]),
                    ],
                ])
                ->add('captcha', CaptchaType::class, [])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([]);
    }
}
