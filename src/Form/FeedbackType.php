<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
      /*  ->add('name', TextType::class, 
            array(
                'label'=> 'part_seven.name',
                'attr' => array(
                    'class' => 'form-control', 
                    'placeholder' => 'part_seven.name')))*/
        ->add('email', TextType::class,
            array('label'=> 'part_seven.email',
                'attr' => array(
                'class' => 'form-control',
                'placeholder' => 'part_seven.email')))
        ->add('booking', IntegerType::class, 
            array(
                'label'=> 'part_seven.booking_nr',
                'attr' => array(
                    'class' => 'form-control', 
                    'placeholder' => 'part_seven.booking_nr')))
        ->add('rate', HiddenType::class)
        ->add('observations', TextareaType::class,
            array('label' => 'part_seven.observations',
                'attr' => array(
                'class' => 'form-control',
                'rows' => 5,
                'placeholder' => 'feedback_input')))
        ->add('submit', SubmitType::class,
            array('label'=> false, 'attr' => array(
                'class' => 'd-none')))
        ;
    }

}