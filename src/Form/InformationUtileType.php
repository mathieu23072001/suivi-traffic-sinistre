<?php

namespace App\Form;

use App\Entity\InformationUtile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class InformationUtileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
        ->add('description', TextareaType::class, array(
            'required'=>false,
            'label'=> "description ",
            'attr'=>array('class'=>'form-control','placeholder'=>'description')
        ))
        ->add('lieu', TextType::class, array(
            'required'=>true,
            'label'=> "lieu ",
            'attr'=>array('class'=>'form-control','placeholder'=>'cliquez sur la carte')
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
            'data_class' => InformationUtile::class,
        ]);
    }
}
