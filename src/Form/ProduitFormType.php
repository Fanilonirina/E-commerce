<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Repository\CategorieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Positive;

class ProduitFormType extends AbstractType
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
            ->add('designation', TextType::class, [
                'label' => 'Designation',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix',
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new Positive(
                        message: 'Le prix ne peut pas etre négative'
                    )
                ]
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'attr' => [
                    'class' => 'form-control'
                ],
                'choice_label' => 'name_categorie',
                'label' => 'Categorie',
                'query_builder' => function (CategorieRepository $cr) {
                    return $cr->createQueryBuilder('c')->orderBy('c.name_categorie', 'ASC');
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
