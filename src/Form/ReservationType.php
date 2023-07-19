<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Constraints as Assert;


class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('reservation_start', DateTimeType::class, [
            'date_widget' => 'single_text',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\GreaterThanOrEqual([
                    'value' => 'today',
                    'message' => 'La date de début doit être ultérieure ou égale à la date actuelle.',
                ]),
            ],
        ])
        ->add('reservation_end', DateTimeType::class, [
            'date_widget' => 'single_text',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\GreaterThanOrEqual([
                    'propertyPath' => 'parent.all[reservation_start].data',
                    'message' => 'La date de fin doit être ultérieure à la date de début.',
                ]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
