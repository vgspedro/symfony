<?php

namespace App\Form;


use App\Entity\Locales;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class CompanyTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('locale', EntityType::class, array(
                'class' => Locales::class,
                'choice_label' => 'name',
                'placeholder' => 'Local',
                'label' => 'Local',
                'attr' => ['class' => 'w3-col s6 w3-margin-bottom w3-input w3-border w3-white w3-select']))
            ->add('description', TextareaType::class, array(
                'required' => false,
                'label' => 'Descrição',
                'attr' => ['class' => 'w3-col s6 w3-margin-bottom w3-input w3-border w3-white','placeholder'=>'Descrição', 'rows' => 3]));
    }

   public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => CompanyTranslation::class
        ));
    }

}