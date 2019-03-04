<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class,
            array(
                'label'=>'part_seven.email',
                'required' => true,
                'attr' => ['class' => 'w3-input w3-border w3-white EMAIL','placeholder'=>'part_seven.email',]
            ))
            ->add('username', TextType::class, array(
                'label'=>'part_seven.name',
                'required' => true,
                'attr' => ['class' => 'w3-input w3-border w3-white NAME','placeholder'=>'part_seven.name','autofocus'=>'autofocus']
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Password *', 'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Password *']),
                'second_options' => array('label' => 'Repetir Password *', 'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Repetir Password *'])
            ))
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}