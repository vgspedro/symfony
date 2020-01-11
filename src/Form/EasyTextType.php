<?php

namespace App\Form;

use App\Entity\EasyText;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityRepository;

class EasyTextType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('easytexthtml', HiddenType::class,
            [
                'required' => false
            ])
            ->add('name', TextType::class,
                [
                'label' => 'Titulo *', 
                'required' => false,
                'attr' => ['class' => 'w3-input w3-border w3-white w3-margin-bottom', 'placeholder' => 'Titulo *']
             ])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => EasyText::class,
        ));
    }
}