<?php

namespace App\Form;

use App\Entity\Blockdates;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class BlockdatesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, array(
            'class' => Category::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('b')
               // ->andWhere('b.isActive = :active')
                //->setParameter('active', 1)
                ->orderBy('b.namePt', 'ASC');
            },
            'required' => false,
            'choice_label' => 'namept',
            'placeholder' => 'Alterar Todos',
            'choice_attr' => function($val, $key, $index) {
                return ['class' =>'TOUR_TXT'];
            },
            'attr' => ['class' => 'w3-input w3-border w3-white', 'onchange'=>"getBlockedDates(this.value)", 'style' => "height:50px"]           
            ))
            ->add('date', TextareaType::class, array(
                'label' => true,
                'required' => false,
                'attr' => ['readonly'=>true, 'id'=>'display', 'rows'=>'3', 'class'=>'w3-input w3-border w3-margin-bottom','placeholder'=>'Escolha, no Calendário as Datas']
            ))

            ->add('onlydates', ChoiceType::class, array(
                'choices' => array(
                    'Bloquear estas Datas' => 0,
                    'Desbloquear só estas Datas' => 1
                ),
                    'attr' => ['class' => 'w3-input w3-border w3-white' , 'style' => "height:50px"]
            ))
            ->add('submit', SubmitType::class,
            array(
                'label' => 'part_seven.submit',
                'attr' => ['class' => 'w3-button w3-green w3-block w3-padding w3-section SAVE']
            ));
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Blockdates::class,
        ));
    }
}