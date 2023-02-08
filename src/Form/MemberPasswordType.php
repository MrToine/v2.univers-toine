<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

use Symfony\Component\Validator\Constraints as Assert;

use App\Entity\Member;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('new_password', RepeatedType::class, [
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
            ->add('plainPassword', PasswordType::class, [
                'attr' => [
                    'class' => 'input'
                ],
                'label' => 'Votre mot de passe actuel',
                'label_attr' => [
                    'class' => 'label'
                ],
                'constraints' => [New Assert\NotBlank()]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'button is-success mt-4'
                ],
                'label' => 'Valider'
            ])
        ;
    }
}
