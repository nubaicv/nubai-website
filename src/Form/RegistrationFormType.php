<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Gregwar\CaptchaBundle\Type\CaptchaType;

class RegistrationFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
                ->add('email', EmailType::class, [
                    'constraints' => [
                        new Email([
                            'message' => 'not.valid.email.address',
                        ]),
                    ],
                ])
                ->add('password', PasswordType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'please.enter.a.password',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'your.password.should.be.at.least.{{ limit }}.characters',
                            'max' => 255,
                        ]),
                    ],
                ])
                ->add('agreeTerms', CheckboxType::class, [
                    'mapped' => false,
                    'constraints' => [
                        new IsTrue([
                            'message' => 'you.should.agree.to.our.terms',
                        ]),
                    ],
                ])
                ->add('captcha', CaptchaType::class, [])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
