<?php

namespace App\Form;

use App\Entity\Filter;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categories', EntityType::class, [
                'required' => false,
                'class' => Category::class,
                'expanded' => true,
                'multiple' => true,
                'attr' => [
                    'placeholder' => 'Rechercher'
                ]
            ])
            ->add('min', NumberType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prix minimum',
                    'value' => $options['default_min'], 

                ]
            ])
            ->add('max', NumberType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prix max',
                    'value' => $options['default_max'],
                ]
            ])
            ->add('ray', RangeType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prix max',
                    'min' => 0,
                    'max' => 100,
                ]
            ])
            ->add('places', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'lieu',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Filter::class,
            'method' => 'GET',
            'default_min' => null,
            'default_max' => null,
            'csrf_protection' => false,
        ]);
    }
    public function getBlockPrefix()
    {
        return '';
    }
}
