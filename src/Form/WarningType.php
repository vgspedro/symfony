<?php

namespace App\Form;

use App\Entity\Warning;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityRepository;

class WarningType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('info_pt', TextareaType::class,
            array(
                'required' => false,
                'attr' => ['class' => 'w3-input w3-padding-16','placeholder'=>'Aviso (PT)*',]
            ))
            ->add('info_en', TextareaType::class, array(
                'required' => false,
                'attr' => ['class' => 'w3-input w3-padding-16 ','placeholder'=>'Aviso (EN)*']
            ))
            ->add('visible', ChoiceType::class, array( 
                'choices' => array(
                    'Activo Não' => 0,
                    'Activo Sim' => 1
                ),
                    'attr' => ['class' => 'w3-input w3-select']
            ))
            ->add('submit', SubmitType::class,
            array(
                'label' => 'part_seven.submit',
                'attr' => ['class' => 'w3-button w3-green w3-block w3-padding w3-section SAVE']
            ))
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Warning::class,

        ));
    }
}