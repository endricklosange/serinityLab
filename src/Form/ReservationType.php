<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('activity', EntityType::class, [
                'class' => Activity::class,
                'choice_label' => 'name',
                'label' => 'Activity',
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('reservation_start', DateTimeType::class,[
                'date_widget' => 'single_text'
            ])
            ->add('reservation_end', DateTimeType::class,[
                'date_widget' => 'single_text'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
