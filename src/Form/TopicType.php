<?php

namespace App\Form;

use App\Entity\ForumTopic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TopicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                "attr" => [
                    "class" => "input",
                    "minlength" => '4',
                ],
                "label" => "Titre du sujet",
                "label_attr" => [
                    "class" => "label"
                ],
                "required" => true
            ])
            ->add('post', PostType::class, [
                'label' => false,
                'data_class' => null
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ForumTopic::class,
        ]);
    }
}
