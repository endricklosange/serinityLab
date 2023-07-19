<?php

namespace App\Form;

use App\Entity\Score;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RangeType;

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
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'max' => 5,
                        'notInRangeMessage' => 'Le champ doit être compris entre {{ min }} et {{ max }}.',
                    ]),
                ],
            ])
            ->add('cleanliness', RangeType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 5,
                ],
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'max' => 5,
                        'notInRangeMessage' => 'Le champ doit être compris entre {{ min }} et {{ max }}.',
                    ]),
                ],
            ])
            ->add('location', RangeType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 5,
                ],
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'max' => 5,
                        'notInRangeMessage' => 'Le champ doit être compris entre {{ min }} et {{ max }}.',
                    ]),
                ],
            ])
            ->add('product', RangeType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 5,
                ],
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'max' => 5,
                        'notInRangeMessage' => 'Le champ doit être compris entre {{ min }} et {{ max }}.',
                    ]),
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
