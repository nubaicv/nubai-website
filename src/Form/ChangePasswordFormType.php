<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options' => [
                    'attr' => ['class' => 'w3-input w3-padding-16 w3-section w3-border'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'please.enter.a.password',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'your.password.should.be.at.least.{{ limit }}.characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                    'label' => 'new.password',
                ],
                'second_options' => [
                    'label' => 'repeat.password',
                    'attr' => ['class' => 'w3-input w3-padding-16 w3-section w3-border'],
                ],
                'invalid_message' => 'the.password.fields.must.match.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
