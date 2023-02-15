<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('metier')
            ->add('sexe',ChoiceType::class, [
                'choices'  => [
                    'Homme' => "M",
                    'Femme' => "F",
                ],
                'label' => 'Je suis'
            ])
            ->add('imageProfil', FileType::class, [
                'label' => "Ajouter une photo de profil",
                'mapped' => false,
                'required' => false
            ])
            ->add('password',PasswordType::class,[
                'label' => "Mot de passe",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
