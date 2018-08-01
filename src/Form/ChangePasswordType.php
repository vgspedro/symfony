<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Entity\User;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('plainPassword', RepeatedType::class, array(
	        		'type' => PasswordType::class,
	        		'first_options' => array(),
	        		'second_options' => array(),
	        		'constraints' => array(	        				
	        				new NotBlank(),
	        				new Length(array('min' => 4, 'max' => 128))
	        		)
	        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        		'data_class' => User::class
        ]);
    }
}
