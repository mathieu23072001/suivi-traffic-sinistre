<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, array(
            'required'=>true,
            'attr'=>array('class'=>'form-control','placeholder'=>'votre email')
            ))
            
            ->add('telephone', TextType::class, array(
                'required'=>true,
                'attr'=>array('class'=>'form-control','placeholder'=>'votre numero')
            ))
            ->add('nom', TextType::class, array(
                'required'=>true,
                'attr'=>array('class'=>'form-control','placeholder'=>'votre nom')
            ))
            ->add('prenoms', TextType::class, array(
                'required'=>true,
                'attr'=>array('class'=>'form-control','placeholder'=>'votre prenoms')
            ))
            ->add('password', PasswordType::class, array(
                'required'=>true,
                'label'=> "Mot de passe",
                'attr'=>array('class'=>'form-control','placeholder'=>'votre mot de passe (minimum 6 caractères)')
            ))
            ->add('repeatPassword', PasswordType::class, array(
                'required'=>true,
                'mapped'=> false,
                'label'=> "Confirmer le mot de passe",
                'attr'=>array('class'=>'form-control','placeholder'=>'confirmez le mot de passe')
            ))
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
