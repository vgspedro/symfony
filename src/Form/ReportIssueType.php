<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReportIssueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', TextType::class, 
            array(
                'label'=> 'part_seven.name',
                'attr' => array(
                    'class' => 'form-control', 
                    'placeholder' => 'part_seven.name')))
        ->add('email', TextType::class,
            array('label'=> 'part_seven.email',
                'attr' => array(
                'class' => 'form-control',
                'placeholder' => 'part_seven.email')))
        ->add('image', FileType::class,
            array('label'=> 'attach_image',
                'attr' => array(
                'class' => '',
                'placeholder' => 'part_seven.image')))
        ->add('observations', TextareaType::class,
            array('label' => 'part_seven.observations',
                'attr' => array(
                'class' => 'form-control',
                'rows' => 5,
                'placeholder' => 'part_seven.observations')))
        ->add('submit', SubmitType::class,
            array('label'=> false, 'attr' => array(
                'class' => 'd-none')))
        ;
    }
}