<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\Currency;
use App\Entity\Countries;
use App\Entity\CompanyTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name', TextType::class,
            array(
                'required' => false,
                'label' => 'Empresa *',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Empresa *']
            ))
            ->add('address', TextType::class,
            array(
                'required' => true,
                'label' => 'Morada *',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Morada *']
            ))
            ->add('p_code', TextType::class,
            array(
                'required' => true,
                'label' => 'Cod.Postal *',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Cod.Postal *']
            ))
            ->add('city', TextType::class, array(
                'required' => true,
                'label' => 'Cidade *',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Cidade *']
            ))
            ->add('country', EntityType::class, array(
                'class' => Countries::class,
                'choice_label' => 'name',
                'placeholder' => 'Pais *',
                'label' => 'Pais *',
                'attr' => ['class' => 'w3-select w3-border w3-white']   
            ))
            ->add('email', TextType::class,
            array(
                'required' => true,
                'label' => 'Email *',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Email *']
            ))
            ->add('email_smtp', TextType::class,
            array(
                'required' => true,
                'label' => 'Email SMTP *',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Email SMTP*']
            ))
            ->add('email_pass', TextType::class,
            array(
                'required' => true,
                'label' => 'Email Pass *',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Email Pass *']
            ))
            ->add('email_port', IntegerType::class,
            array(
                'required' => true,
                'label' => 'Porta Email *',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'465, 143']
            ))
            ->add('email_certificade', TextType::class,
            array(
                'required' => true,
                'label' => 'Email Certificado *',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'ssl, tls']
            ))
            ->add('telephone', TextType::class,
            array(
                'required' => false,
                'label' => 'Telefone',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Telefone']
            ))
            ->add('fiscal_number', IntegerType::class,
            array(
                'required' => true,
                'label' => 'Nº Fiscal *',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Nº Fiscal *']
            ))
            ->add('coords_google_maps', TextType::class, array(
                'required' => true,
                'label' => 'Google Maps Coords *',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Google Maps Coords *']
            ))
            ->add('google_maps_api_key', TextType::class, array(
                'required' => true,
                'label' => 'Google Maps Api Key *',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'Google Maps Api Key *']
            ))
            ->add('logo', FileType::class, array(
                'label' => 'Logo',
                'required' => false,
                'attr' => ['class' => 'w3-hide set-image','onchange' => 'loadFile(event)']
            ))
            ->add('currency', EntityType::class, array(
                'class' => Currency::class,
                'choice_label' => 'entity',
                'placeholder' => 'Moeda *',
                'label' => 'Moeda *',
                'attr' => ['class' => 'w3-select w3-border w3-white']   
            ))
            ->add('link_facebook', TextType::class, array(
                'required' => false,
                'label' => 'Facebook Url',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'https://facebook']
            ))

            ->add('link_my_domain', TextType::class, array(
                'required' => false,
                'label' => 'O meu Dominio Url ',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'https://o meu dominio']
            ))

            ->add('link_youtube', TextType::class, array(
                'required' => false,
                'label' => 'YouTube',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'https://youtube']
            ))

            ->add('link_behance', TextType::class, array(
                'required' => false,
                'label' => 'Behance Url',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'https://behance']
            ))

            ->add('link_twitter', TextType::class, array(
                'required' => false,
                'label' => 'Twitter Url',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'https://twitter']
            ))
            ->add('link_instagram', TextType::class, array(
                'required' => false,
                'label' => 'Instagram Url',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'https://instagram']
            ))

            ->add('link_linken', TextType::class, array(
                'required' => false,
                'label' => 'Linkedin Url',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'https://linken']
            ))

            ->add('link_pinterest', TextType::class, array(
                'required' => false,
                'label' => 'Pinterest Url',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'https://pinterest']
            ))
            ->add('link_snapchat', TextType::class, array(
                'required' => false,
                'label' => 'Snapchat Url',
                'attr' => ['class' => 'w3-input w3-border w3-white','placeholder'=>'https://snapchat']
            ))
            ->add('link_facebook_active', CheckboxType::class, array(
                'label'    => 'Ativo',
                'required' => false,
                'attr' => ['class' => 'w3-check']
            ))
            ->add('link_twitter_active', CheckboxType::class, array(
                'label'    => 'Ativo',
                'required' => false,
                'attr' => ['class' => 'w3-check']
            ))
             ->add('link_youtube_active', CheckboxType::class, array(
                'label'    => 'Ativo',
                'required' => false,
                'attr' => ['class' => 'w3-check']
            ))
            ->add('link_behance_active', CheckboxType::class, array(
                'label'    => 'Ativo',
                'required' => false,
                'attr' => ['class' => 'w3-check']
            ))
            ->add('link_instagram_active', CheckboxType::class, array(
                'label'    => 'Ativo',
                'required' => false,
                'attr' => ['class' => 'w3-check']
            ))
            ->add('link_linken_active', CheckboxType::class, array(
                'label'    => 'Ativo',
                'required' => false,
                'attr' => ['class' => 'w3-check']
            ))
            ->add('link_pinterest_active', CheckboxType::class, array(
                'label'    => 'Ativo',
                'required' => false,
                'attr' => ['class' => 'w3-check']
            ))
            ->add('link_snapchat_active', CheckboxType::class, array(
                'label'    => 'Ativo',
                'required' => false,
                'attr' => ['class' => 'w3-check']
            ))
            ->add('submit', SubmitType::class,
            array(
                'label' => 'Gravar',
                'attr' => ['class' => 'w3-btn w3-block w3-border w3-green w3-margin-top']
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