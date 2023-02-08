<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

use Symfony\Component\Validator\Constraints as Assert;

use App\Entity\Member;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('display_name', TextType::class, [
                'attr' => [
                    'class' => 'input',
                    'minlenght' => 4
                ],
                'label' => 'Nom d\'utilisateur',
                'label_attr' => [
                    'class' => 'label'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 4])
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'input',
                    'minlenght' => 2
                ],
                'label' => 'Email',
                'label_attr' => [
                    'class' => 'label'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                    new Assert\Length(['min' => 2])
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => [
                        'class' => 'input'
                    ],
                    'label' => 'Mot de passe',
                    'label_attr' => [
                        'class' => 'label'
                    ],
                ],
                'second_options' => [
                    'attr' => [
                        'class' => 'input'
                    ],
                    'label' => 'Confirmation mot de passe',
                    'label_attr' => [
                        'class' => 'label'
                    ],
                ],
                'invalid_message' => 'Les mots de passe ne corespondent pas.'
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'button is-success mt-4'
                ],
                'label' => 'S\'inscrire !'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Member::class,
        ]);
    }
}
