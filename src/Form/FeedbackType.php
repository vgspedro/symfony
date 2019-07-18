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

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', TextType::class, 
            array(
                'label'=> 'part_seven.name',
                'attr' => array(
                    'class' => 'w3-input', 
                    'placeholder' => 'part_seven.name')))
        ->add('email', TextType::class,
            array('label'=> 'part_seven.email',
                'attr' => array(
                'class' => 'w3-input',
                'placeholder' => 'part_seven.email')))
        ->add('booking', IntegerType::class, 
            array(
                'label'=> 'part_seven.booking_nr',
                'attr' => array(
                    'class' => 'w3-input', 
                    'placeholder' => 'part_seven.booking_nr')))
        ->add('rate',IntegerType::class, 
            array(
                'label'=> 'part_seven.rating',
                'attr' => array(
                    'class' => 'w3-input', 
                    'placeholder' => 'part_seven.rating')))
        ->add('observations', TextareaType::class,
            array('label' => 'part_seven.observations',
                'attr' => array(
                'class' => 'w3-input w3-light-grey',
                'rows' => 5,
                'placeholder' =>'part_seven.observations')))
        ->add('submit', SubmitType::class,
            array('label'=> false, 'attr' => array(
                'class' => 'w3-hide')))
        ;
    }

}