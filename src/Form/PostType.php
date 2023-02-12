<?php

namespace App\Form;

use App\Entity\ForumPost;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                "attr" => [
                    "class" => "textarea",
                    "minlength" => '4',
                ],
                "label" => "Contenu",
                "label_attr" => [
                    "class" => "label"
                ]
            ])
            ->add('submit', SubmitType::class, [
                "attr" => [
                    "class" => "button is-success mt-4"
                ],
                "label" => "Envoyer"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ForumPost::class,
        ]);
    }
}
