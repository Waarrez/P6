<?php

namespace App\Form\Trick;

use App\Entity\Group;
use App\Entity\Trick;
use App\Form\ImageType;
use App\Form\VideoType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class TrickFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false
            ])
            ->add('content', TextareaType::class, [
                'label' => false
            ])
            ->add('groups', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name',
                'label' => false
            ])
            ->add('imageFile', FileType::class, [
                'label' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG).',
                    ]),
                ],
            ])
            ->add('images', FileType::class, [
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'label' => false,
            ])
            ->add('newVideo', TextType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ajouter une nouvelle vidéo'
                ]
            ])
            ->add('newVideo2', TextType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ajouter une nouvelle vidéo'
                ]
            ])
            ->add('newVideo3', TextType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ajouter une nouvelle vidéo'
                ]
            ])
            ->add('newImage', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
            ])
            ->add('newImage2', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
            ])
            ->add('newImage3', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
            ])
            ->add('secondaryImages', CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
                'label' => false,
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}