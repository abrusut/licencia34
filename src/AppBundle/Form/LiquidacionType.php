<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LiquidacionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nis')            
            ->add('fechaCobro','date', array(                        
                'constraints' => null,
                'data' => (isset($options['data']) && 
                                $options['data']->getFechaCobro() !== null) ? $options['data']->getFechaCobro() : null,
                 // render as a single text box
                 'widget' => 'single_text',
                 'format' => 'dd/MM/yyyy',
                 // do not render as type="date", to avoid HTML5 date pickers
                 'html5' => false,
                 // add a class that can be selected in JavaScript
                 'attr' => ['class' => 'js-datepicker'],
                 'label' => 'Fecha Cobro',   
                 'read_only' => TRUE              
                )
            )  
            ->add('codigoBarraCliente')
            ->add('numeroRendicion')
            ->add('importeCobrado')
            ->add('comision')                                              
            ->add('createdAt','date', array(                        
                'constraints' => null,
                'data' => (isset($options['data']) && 
                                $options['data']->getCreatedAt() !== null) ? $options['data']->getCreatedAt() : new \DateTime(),
                 // render as a single text box
                 'widget' => 'single_text',
                 'format' => 'dd/MM/yyyy',
                 // do not render as type="date", to avoid HTML5 date pickers
                 'html5' => false,
                 // add a class that can be selected in JavaScript
                 'attr' => ['class' => 'js-datepicker'],
                 'label' => 'Fecha Alta',   
                 'read_only' => TRUE,
                 'required' => false             
                )
            )  
            ->add('updatedAt','date', array(                        
                'constraints' => null,
                'data' => (isset($options['data']) && 
                                $options['data']->getUpdatedAt() !== null) ? $options['data']->getUpdatedAt() : null,
                 // render as a single text box
                 'widget' => 'single_text',
                 'format' => 'dd/MM/yyyy',
                 // do not render as type="date", to avoid HTML5 date pickers
                 'html5' => false,
                 // add a class that can be selected in JavaScript
                 'attr' => ['class' => 'js-datepicker'],
                 'label' => 'Fecha Actualizacion',   
                 'read_only' => TRUE,
                 'required' => false         
                )
            )   
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Liquidacion'
        ));
    }
}
