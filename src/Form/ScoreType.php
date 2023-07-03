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
            ],
        ])
        ->add('cleanliness', RangeType::class, [
            'attr' => [
                'min' => 1,
                'max' => 5,
            ],
        ])
        ->add('location', RangeType::class, [
            'attr' => [
                'min' => 1,
                'max' => 5,
            ],
        ])
        ->add('product', RangeType::class, [
            'attr' => [
                'min' => 1,
                'max' => 5,
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
