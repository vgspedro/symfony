<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Money\Money;

class MoneyPhpType extends AbstractType implements DataMapperInterface
{	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('amount', MoneyType::class, array(
				'label' => false,
				'currency' => false,
				'divisor' => 100,
				'attr' => [												
					'style' => 'width:100%','class' => 'w3-input w3-border w3-white'
				],
			))
			->setDataMapper($this)
		;		
	}
	
    public function mapDataToForms($data, $forms)
    {
    	$forms = iterator_to_array($forms);    	
    	$forms['amount']->setData($data ? $data->getAmount() : 0);
    }
    
    public function mapFormsToData($forms, &$data)
    {
    	$forms = iterator_to_array($forms);    	
    	$data = Money::EUR($forms['amount']->getData());
    } 
    
    public function getBlockPrefix()
    {
    	return 'moneyphp';
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
    	$resolver->setDefaults(array(
    			'empty_data' => null
    	));
    }
}
