<?php

namespace App\Form;

use App\Entity\Category;
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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', FileType::class, array(
                'label' => false,
                'required' => false,
                'attr' => ['class' => 'w3-hide set-image','onchange' => 'loadFile(event)']
            ))
            ->add('name_pt', TextType::class,
            array(
                'required' => false,
                'label' => 'Nome (PT)*',
                'attr' => ['class' => 'w3-input w3-border','placeholder'=>'Nome (PT)*',]
            ))
            ->add('name_en', TextType::class, array(
                'required' => false,
                'label' => 'Nome (EN)*',
                'attr' => ['class' => 'w3-input w3-border','placeholder'=>'Nome (EN)*']
            ))
            ->add('description_pt', TextareaType::class,
            array(
                'required' => false,
                'label' => 'Descrição (PT)*',
                'attr' => ['class' => 'w3-input w3-border','placeholder'=>'Descrição (PT)*', 'rows' => 5 ]
            ))
            ->add('description_en', TextareaType::class, array(
                'required' => false,
                'label' => 'Descrição (EN)*',
                'attr' => ['class' => 'w3-input w3-border','placeholder'=>'Descrição (EN)*', 'rows' => 5 ]
            ))
            ->add('children_price', IntegerType::class, array(
                'label' => 'Preço Criança (€)*',
                'required'  => false,
                'attr' => ['class' => 'w3-input w3-border','placeholder'=>'Preço Criança (€)*', 'min'=>'0', 'step'=>'any', 'type'=>'number']
            ))
            ->add('adult_price', IntegerType::class, array(
                'label' =>'Preço Adulto (€)*',
                'required' => false,
                'attr' => ['class' => 'w3-input w3-border', 'placeholder'=>'Preço Adulto (€)*', 'min'=>'0','step'=>'any', 'type'=>'number']
            ))
            ->add('event', CollectionType::class, array(
                'entry_type' => EventType::class,
                'entry_options' => array('label' => false),
                'allow_add' => true,
                'allow_delete' => true,                 
                'by_reference' => false,
                'label' => false   
            ))
            ->add('blockdate', CollectionType::class, array(
                    'entry_type' => BlockdatesType::class,
                    'entry_options' => array('label' => false),
                    'allow_add' => true,
                    'allow_delete' => true,                 
                    'by_reference' => false,
                    'label' => false   
            ))
            ->add('is_active', CheckboxType::class, array(
                'label'    => 'Ativa?',
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
            'data_class' => Category::class,
        ));
    }*/
}