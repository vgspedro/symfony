<?php

namespace App\Form;

use App\Entity\Faqs;
use App\Entity\Locales;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
//use FOS\CKEditorBundle\Form\Type\CKEditorType;


class FaqsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('html', HiddenType::class,
            array(
                'required' => false
            ))
            ->add('locales', EntityType::class, array(
                    'class' => Locales::class,
                    'choice_label' => 'name',
                    'placeholder' => 'Local',
                    'label' => 'Local',
                    'attr' => ['class' => 'w3-input w3-border w3-white']

            ))
            ->add('name', TextType::class,
                array(
                'label'=> 'Titulo *',
                'required' => false,
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Titulo *']
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Faqs::class,
        ));
    }
}