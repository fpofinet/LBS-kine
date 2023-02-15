<?php

namespace App\Form;

use App\Entity\Projet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('domaine')
            ->add('resume',FileType::class, [
                'label' => "Ajouter le document du projet",
                'mapped' => false,
                'required' => false
            ])

            ->add('brandImage',FileType::class, [
                'label' => "Ajouter votre logo",
                'mapped' => false,
                'required' => false
            ])
            ->add('financement',TextType::class,[
                'label' =>"Quel montant souhaitez vous obtenir"
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projet::class,
        ]);
    }
}
