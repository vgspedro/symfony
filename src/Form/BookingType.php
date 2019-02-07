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