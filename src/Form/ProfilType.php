<?php

namespace App\Form;

use App\Entity\Profil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, array(
            'required'=>true,
            'label'=> "code du profil",
            'attr'=>array('class'=>'form-control','placeholder'=>' ROLE_NOMPROFIL')
            ))
            
            ->add('libelle', TextType::class, array(
                'required'=>true,
                'label'=> "libelle du profil",
                'attr'=>array('class'=>'form-control','placeholder'=>'type usager')
            ))
           
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profil::class,
        ]);
    }
}
