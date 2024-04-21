<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'autocomplete' => 'email',
                    'class' => 'w3-input w3-padding-16 w3-section w3-border'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'please.enter.your.email',
                    ]),
                ],
                'label' => 'label.email'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
