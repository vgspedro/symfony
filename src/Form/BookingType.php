<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class BookingType extends AbstractType
{

   private function color(){
        return array(
        'w3-text-black',
        'w3-t-tour',
        'w3-text-blue',
        'w3-text-indigo',
        'w3-text-teal',
        'w3-text-blue-gray',
        'w3-text-deep-purple',
        'w3-text-cyan',
        'w3-text-aqua',
        'w3-text-brown',
        'w3-text-deep-orange',
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', TextType::class, 
            array(
                'label'=>false, 
                'required' => false,
                'attr' => ['class' => 'w3-input w3-padding-16 NAME','placeholder'=>'part_seven.name']
            ))
        ->add('email', EmailType::class,
            array(
                'label'=>false,
                'required' => false,
                'attr' => ['class' => 'w3-input w3-padding-16 EMAIL','placeholder'=>'part_seven.email']
            ))
        ->add('telephone', IntegerType::class,
            array(
                'label'=>false,
                'required' => false,
                'attr' => ['class' => 'w3-input w3-padding-16 TELEPHONE','placeholder'=>'part_seven.telephone']
            ))
        ->add('address', TextType::class,
            array(
                'label'=>false,
                'required' => false,
                'attr' => ['class' => 'w3-input w3-padding-16 ADDRESS','placeholder'=>'part_seven.address']
            ))
        ->add('tourtype', EntityType::class, array(
            'class' => Category::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('b')
                ->andWhere('b.isActive = :active')
                ->setParameter('active', 1)
                ->orderBy('b.namePt', 'ASC');
            },
            'required' => false,
            'choice_label' => 'namePt',
            'placeholder' => 'part_seven.tour',
            'choice_attr' => function($val, $key, $index) {
                return ['class' =>$val->getNameEn().'/'.$val->getNamePt()];
                },
                'attr' => ['class' => 'w3-input w3-select w3-padding-16', 'onchange'=>'getAvailability(this.value)']
            ))
        ->add('date', TextType::class,
            array(
                'label'=>false,
                'required' => false,
                'attr' => ['class' => 'w3-input w3-padding-16 DATE','placeholder'=>'part_seven.date','readonly'=>true]
            ))
         ->add('hour', TextType::class, 
            array(
                'label'=>false, 
                'required' => false,
                'attr' => ['class' => 'w3-input w3-padding-16 HOUR','placeholder'=>'part_seven.hour','readonly'=>true]
            ))
        ->add('adult', IntegerType::class,
            array(
                'label'=>false,
                'required' => false,
                'attr' => ['class' => 'w3-input w3-padding-16 ADULT','placeholder'=>'part_seven.adult', 'min' => '0']
            ))
        ->add('children', IntegerType::class,
            array(
                'label'=>false,
                'required' => false,
                'attr' => ['class' => 'w3-input w3-padding-16 CHILDREN','placeholder'=>'part_seven.children', 'min' => '0']
            ))
        ->add('baby', IntegerType::class,
            array(
                'label'=>false,
                'required' => false,
                'attr' => ['class' => 'w3-input w3-padding-16 BABY','placeholder'=>'part_seven.baby', 'min' => '0']
            ))
        ->add('notes', TextareaType::class,
            array(
                'label'=>false,
                'required' => false,
                'attr' => ['class' => 'w3-input w3-padding-16 NOTES','placeholder'=>'Notas']
            ))
        ->add('rgpd',CheckboxType::class, 
            array(
                'label'=>false, 
                'required' => false,
                'attr' => ['class' => 'w3-check']
            ))
        ->add('submit', SubmitType::class,
            array(
                'label' => 'part_seven.submit',
                'attr' => ['class' => 'w3-btn w3-padding w3-section my-btn SUBMIT']
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Booking::class,
        ));
    }
}