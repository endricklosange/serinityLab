<?php

namespace App\Form;

use App\Entity\Score;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScoreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('price_quality', RangeType::class, [
            'attr' => [
                'min' => 1,
                'max' => 5,
                'id' => 'price_quality_range',
            ],
        ])
        ->add('cleanliness', RangeType::class, [
            'attr' => [
                'min' => 1,
                'max' => 5,
                'id' => 'cleanliness_range',
            ],
        ])
        ->add('location', RangeType::class, [
            'attr' => [
                'min' => 1,
                'max' => 5,
                'id' => 'location_range',
            ],
        ])
        ->add('product', RangeType::class, [
            'attr' => [
                'min' => 1,
                'max' => 5,
                'id' => 'product_range',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Score::class,
        ]);
    }
}
