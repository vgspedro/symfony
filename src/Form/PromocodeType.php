<?php

namespace App\Form;

use App\Entity\Promocode;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class PromocodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('discount_type', ChoiceType::class, [
                'label' => 'discount_type', 
                'choices'  => [
                    'value' => 'value',
                    'percent' => 'percent',
                ],
                'attr' => ['class' => 'w3-input w3-border w3-white w3-margin-bottom']
            ])
            ->add('code', TextType::class,
                [
                'label' => 'Codigo *', 
                'required' => false,
                'attr' => ['class' => 'w3-input w3-border w3-white w3-margin-bottom', 'placeholder' => 'Código *']
             ])
            ->add('start', TextType::class,
                [
                'label' => 'Incio *', 
                'required' => false,
                'attr' => ['class' => 'w3-input w3-border w3-white w3-margin-bottom', 'placeholder' => 'Inicio *']
             ])
            ->add('end', TextType::class,
                [
                'label' => 'Fim *', 
                'required' => false,
                'attr' => ['class' => 'w3-input w3-border w3-white w3-margin-bottom', 'placeholder' => 'Fim *']
             ])
            ->add('discount', IntegerType::class,
                [
                'label' => 'Desconto * (Min:1, Máx:100)', 
                'required' => false,
                'attr' => ['class' => 'w3-input w3-border w3-white w3-margin-bottom', 'placeholder' => 'Desconto *']
             ])
            ->add('category', EntityType::class, [
                'label' => 'Category', 
                'class' => Category::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.namePt', 'ASC');
                },
                'choice_label' => 'namept',
                'required' => true,
                'attr' => ['class' => 'w3-input w3-border w3-white w3-margin-bottom']
            ])

            ->add('category', EntityType::class, [
                'class' => Category::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.namePt', 'ASC');
                },
                'choice_label' => 'namept',
                'required' => true,
                'attr' => ['class' => 'w3-input w3-border w3-white w3-margin-bottom']
            ])
            ->add('isActive', CheckboxType::class, array(
                'label' => 'Activo',
                'required' => false,
                'attr' => ['class' => 'w3-check']
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Promocode::class,
        ));
    }
}