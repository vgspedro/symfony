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
use Symfony\Component\Form\Extension\Core\Type\PercentType;
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
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Nome (PT)*',]
            ))
            ->add('availability', IntegerType::class,
            array(
                'required' => true,
                'label' => 'lotation',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'lotation',]
            ))
            ->add('deposit', PercentType::class,
            array(
                'scale' => 0,
                'required' => true,
                'symbol' => false,
                'type' => 'fractional',
                'label' => 'Depósito (% sobre montante a pagar no ato compra, se 0 (zero) é 100% ) ',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Depósito']
            ))
            ->add('duration', TextType::class,
            array(
                'required' => true,
                'label' => 'duration',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'dutation', 'readonly' => true]
            ))
            ->add('name_en', TextType::class, array(
                'required' => false,
                'label' => 'Nome (EN)*',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Nome (EN)*']
            ))
            ->add('description_pt', TextareaType::class,
            array(
                'required' => false,
                'label' => 'Descrição (PT)*',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Descrição (PT)*', 'rows' => 5 ]
            ))
            ->add('description_en', TextareaType::class, array(
                'required' => false,
                'label' => 'Descrição (EN)*',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Descrição (EN)*', 'rows' => 5 ]
            ))
            ->add('children_price', MoneyPhpType::class, array(
                'label' => 'Preço Criança (€)*',
                'required'  => false,
                'attr' => ['placeholder'=>'Preço Criança (€)*', 'min'=>'0', 'step'=>'any', 'type'=>'number']
            ))
            ->add('adult_price', MoneyPhpType::class, array(
                'label' =>'Preço Adulto (€)*',
                'required' => false,
                'attr' => ['placeholder'=>'Preço Adulto (€)*', 'min'=>'0','step'=>'any', 'type'=>'number']
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

            ->add('shared', CheckboxType::class, array(
                'label'    => 'Partilhado',
                'required' => false,
                'attr' => ['class' => 'w3-check', 'onclick' => 'setShared()']
            ))

            ->add('highlight', CheckboxType::class, array(
                'label'    => 'Destaque',
                'required' => false,
                'attr' => ['class' => 'w3-check']
            ))
            ->add('warranty_payment', CheckboxType::class, array(
                'label'    => 'Pagamento Online',
                'required' => false,
                'attr' => ['class' => 'w3-check']
            ))
            ->add('warranty_payment_pt', TextareaType::class,
            array(
                'required' => false,
                'label' => 'Texto Pagamento (PT)',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Garantia Pagamento (PT)', 'rows' => 5 ]
            ))
            ->add('warranty_payment_en', TextareaType::class, array(
                'required' => false,
                'label' => 'Texto Pagamento (EN)',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Garantia Pagamento (EN)', 'rows' => 5 ]
            ))
            ->add('submit', SubmitType::class,
            array(
                'label' => 'part_seven.submit',
                'attr' => ['class' => 'w3-btn w3-block w3-border w3-green w3-margin-top SAVE']
            ))
        ;
    }
}