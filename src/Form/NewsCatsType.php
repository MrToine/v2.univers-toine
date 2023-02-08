<?php

namespace App\Form;

use App\Entity\NewsCats;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;

class NewsCatsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                "attr" => [
                    "class" => "input",
                    "minlength" => '4',
                ],
                "label" => "Nom de la catégorie",
                "label_attr" => [
                    "class" => "label"
                ],
                "constraints" => [
                    new Assert\Length(["min" => 4]),
                    new Assert\NotBlank()
                ]
            ])
            ->add('rewrited_name', TextType::class, [
                "attr" => [
                    "class" => "input",
                ],
                "label" => "Slug",
                "label_attr" => [
                    "class" => "label"
                ]
            ])
            ->add('thumbnail', TextType::class, [
                "attr" => [
                    "class" => "input",
                ],
                "label" => "Thumbnail",
                "label_attr" => [
                    "class" => "label"
                ]
            ])
            ->add('description', TextareaType::class, [
                "attr" => [
                    "class" => "textarea",
                    "minlength" => '4',
                ],
                "label" => "Description de la catégorie",
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
            'data_class' => NewsCats::class,
        ]);
    }
}
