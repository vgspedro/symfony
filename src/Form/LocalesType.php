<?php

namespace App\Form;

use App\Entity\Locales;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocalesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null,
                    array(
                            'label' => 'Codigo iso (pt_PT)',
                            'attr' => ['class' => 'w3-input w3-border w3-round','placeholder'=>'Codigo iso (pt_PT)']
            ))
             ->add('filename', null,
                    array(
                            'label' => 'Nome Imagem (.jpg)',
                            'attr' => ['class' => 'w3-input w3-border w3-round','placeholder'=>'Nome Imagem (.jpg)']
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([            
            'data_class' => Locales::class,
        ]);
    }
}
