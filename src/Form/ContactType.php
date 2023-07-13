<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'label' => 'Adresse e-mail',
            'required' => true,
            'constraints' => [
                new NotBlank(['message' => 'Veuillez entrer une adresse e-mail.']),
                new Email(['message' => 'Veuillez entrer une adresse e-mail valide.']),
            ],
            'attr' => [
                'placeholder' => 'Saisissez votre adresse e-mail'
            ]
        ])
        ->add('firstname', TextType::class, [
            'label' => 'Prénom',
            'required' => true,
            'constraints' => [
                new NotBlank(['message' => 'Veuillez entrer votre prénom']),
            ],
            'attr' => [
                'placeholder' => 'Saisissez votre prénom'
            ]
        ])
        ->add('lastname', TextType::class, [
            'label' => 'Nom',
            'required' => true,
            'constraints' => [
                new NotBlank(['message' => 'Veuillez entrer votre nom']),
            ],
            'attr' => [
                'placeholder' => 'Saisissez votre nom'
            ]
        ])
        ->add('message', TextareaType::class, [
            'label' => 'Que voulez-vous nous dire ?',
            'required' => true,
            'constraints' => [
                new NotBlank(['message' => 'Veuillez entrer une message.']),
            ],
            'attr' => [
                'placeholder' => 'Message'
            ]
        ])
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
