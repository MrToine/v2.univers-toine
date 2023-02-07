<?php

namespace App\Form;

use App\Entity\News;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('author_user_id', HiddenType::class, [
                "data" => 1,
                "constraints" => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('published', HiddenType::class, [
                "data" => 1,
                "constraints" => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('id_category', IntegerType::class, [
                "attr" => [
                    "class" => "input",
                ],
                "label" => "Id category",
                "label_attr" => [
                    "class" => "label"
                ],
                "constraints" => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('title', TextType::class, [
                "attr" => [
                    "class" => "input",
                    "minlength" => '4',
                ],
                "label" => "Titre",
                "label_attr" => [
                    "class" => "label"
                ],
                "constraints" => [
                    new Assert\Length(["min" => 4]),
                    new Assert\NotBlank()
                ]
            ])
            ->add('rewrited_title', TextType::class, [
                "attr" => [
                    "class" => "input",
                ],
                "label" => "Slug",
                "label_attr" => [
                    "class" => "label"
                ]
            ])
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
                "label" => "Créer !"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
