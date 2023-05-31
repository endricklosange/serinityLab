<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Category;
use App\Form\ServiceType;
use App\Form\ActivityImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('category_id', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Categorie',
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('services', CollectionType::class, [
                'entry_type' => ServiceType::class, // Créez un formulaire ServiceType pour l'entité Service
                'allow_add' => true, // Autoriser l'ajout de nouveaux services
                'allow_delete' => true, // Autoriser la suppression de services existants
                'by_reference' => false, // Assurez-vous que les services sont gérés par la méthode addService/removeService de l'entité Activity
            ])
            ->add('activityImages', CollectionType::class, [
                'entry_type' => ActivityImageType::class, // Créez un formulaire ActivityImageType pour l'entité Service
                'allow_add' => true, // Autoriser l'ajout de nouveaux services
                'allow_delete' => true, // Autoriser la suppression de services existants
                'by_reference' => false, // Assurez-vous que les services sont gérés par la méthode addActivityImage/removeActivityImage de l'entité Activity
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'scale' => 2,
            ])
            ->add('address', TextType::class)
            ->add('latitude', NumberType::class, [
                'scale' => 8,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('longitude', NumberType::class, [
                'scale' => 8,
                'attr' => ['class' => 'form-control'],
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}
