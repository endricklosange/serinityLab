<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\Activity;
use App\Form\RegistrationFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('companyName', TextType::class,[
                'label' => 'companyName',
                'attr' => [
                    'placeholder' => 'Nom d\'entreprise',
                ]
            ])
            ->add('activity', EntityType::class, [
                'class' => Activity::class,
                'choice_label' => 'name',
                'label' => 'Activity',
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('user', RegistrationFormType::class, [
                'label' => 'Score',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
