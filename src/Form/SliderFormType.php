<?php

namespace App\Form;

use App\Entity\Slider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SliderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'image',
                FileType::class,
                [
                    'data_class' => null,
                    'label' => 'Image : ',
                    'required' => false,
                    'constraints' => [
                        new Image([
                            'maxSize' => '2024k',
                            'maxSizeMessage' => 'Entrez un image inferieur à 2 Mo'
                        ])
                    ],
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add('description1', TextType::class, [
                'label' => '1er description :',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('description2', TextType::class, [
                'label' => '2em description :',
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Slider::class,
        ]);
    }
}
