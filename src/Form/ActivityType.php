<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Category;
use App\Form\ServiceType;
use App\Form\ActivityImageType;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un nom.']),
                    new Length(['max' => 255, 'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.']),
                ],
            ])
            ->add('description', CKEditorType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une description.']),
                ],
            ])
            ->add('category_id', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Categorie',
                'multiple' => false,
                'expanded' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner une catégorie.']),
                ],
            ])
            ->add('services', CollectionType::class, [
                'entry_type' => ServiceType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('activityImages', CollectionType::class, [
                'entry_type' => ActivityImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'scale' => 2,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un prix.']),
                ],
            ])
            ->add('address', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une adresse.']),
                ],
            ])
            ->add('latitude', NumberType::class, [
                'scale' => 14,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une latitude.']),
                ],
            ])
            ->add('longitude', NumberType::class, [
                'scale' => 14,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une longitude.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}
