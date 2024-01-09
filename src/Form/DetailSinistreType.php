<?php

namespace App\Form;

use App\Form\ImageType;
use App\Entity\Sinistre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class DetailSinistreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        ->add('description', TextareaType::class, array(
            'required'=>false,
            'label'=> "description du sinistre",
            'attr'=>array('class'=>'form-control','placeholder'=>'description')
        ))
        ->add('lieu', TextType::class, array(
            'required'=>true,
            'label'=> "lieu du sinistre",
            'attr'=>array('class'=>'form-control','placeholder'=>'cliquez sur la carte')
        ))
        ->add('typeSinistre', EntityType::class, array(
            'class'=>'App\Entity\TypeSinistre',
            'choice_label'=>'libelle',
            'label'=> "type du sinistre",
            'expanded'=>false,
            'multiple'=>false,
            'required'=>true,
            'attr'=>array('class'=>'form-control','placeholder'=>'type du sinistre')
        ))

        ->add('images', FileType::class, array(
            'required'=>false,
            'label'=> "photos",
            'multiple'=>true,
            'mapped'=>false,
            'attr'=>array('class'=>'form-control','placeholder'=>'joindres des images')
        ))

        ->add('latitude', NumberType::class, array(
            'required'=>true,
            'mapped'=>false,

            'attr'=>array('class'=>'form-control','placeholder'=>'cliquez sur la carte')
        ))

        ->add('longitude', NumberType::class, array(
            'required'=>true,
            'mapped'=>false,

            'attr'=>array('class'=>'form-control','placeholder'=>'cliquez sur la carte')
        ))


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sinistre::class,
        ]);
    }
}
