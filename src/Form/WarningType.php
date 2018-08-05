<?php

namespace App\Form;

use App\Entity\Warning;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Doctrine\ORM\EntityRepository;

class WarningType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('info_pt', TextareaType::class,
            array(
                'required' => false,
                'attr' => ['class' => 'w3-input w3-border','placeholder'=>'Aviso (PT)', 'rows' => 3]
            ))
            ->add('info_en', TextareaType::class, array(
                'required' => false,
                'attr' => ['class' => 'w3-input w3-border','placeholder'=>'Aviso (EN)', 'rows' => 3]
            ))
            ->add('visible', CheckboxType::class, array(
                'label'    => 'Visivel ?',
                'required' => false,
                'attr' => ['class' => 'w3-check']
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