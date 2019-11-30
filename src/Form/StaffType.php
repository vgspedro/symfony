<?php

namespace App\Form;

use App\Entity\Staff;
use App\Entity\Job;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class StaffType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', FileType::class, array(
                'data_class' => null,
                'label' => false,
                'required' => false,
                'attr' => ['class' => 'w3-hide set-image','onchange' => 'loadFile(event)']
            ))
            ->add('first_name', TextType::class,
            array(
                'required' => false,
                'label' => 'Nome *',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Nome *',]
            ))
            ->add('last_name', TextType::class, array(
                'required' => false,
                'label' => 'Sobrenome *',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Sobrenome*']
            ))
            ->add('job', EntityType::class, array(
                'class' => Job::class,
                'choice_label' => 'name',
                'placeholder' => 'Posto/Profissão',
                'label' => 'Posto/Profissão',
                'attr' => ['class' => 'w3-select w3-border w3-white']   
            ))
            ->add('is_active', CheckboxType::class, array(
                'label'    => 'Ativo?',
                'required' => false,
                'attr' => ['class' => 'w3-check']
            ))
            ->add('submit', SubmitType::class,
            array(
                'label' => 'part_seven.submit',
                'attr' => ['class' => 'w3-btn w3-block w3-border w3-green w3-margin-top SAVE']
            ))
        ;
    }
    /*
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Gallery::class,
        ));
    }*/
}