<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NumeradorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numeroCorrelativo')            
            ->add('tipoNumerador', 'choice', 
                array(
                        'choices' => array('rendicion' => 'Rendicion', 'liquidacion' => 'Liquidacion'), 
                        'required' => FALSE,
                        'empty_value' => '-- Seleccione --',
                    ))        
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
                 'label' => 'Fecha Creacion',
                 'read_only' => TRUE,
                 'disabled' => TRUE,
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
                 'disabled' => TRUE,
                 'required' => false
                )
            )   
            ->add('fechaBaja','date', array(                        
                'constraints' => null,
                'data' => (isset($options['data']) && 
                                $options['data']->getFechaBaja() !== null) ? $options['data']->getFechaBaja() : null,
                 // render as a single text box
                 'widget' => 'single_text',
                 'format' => 'dd/MM/yyyy',
                 // do not render as type="date", to avoid HTML5 date pickers
                 'html5' => false,
                 // add a class that can be selected in JavaScript
                 'attr' => ['class' => 'js-datepicker'],
                 'label' => 'Fecha Baja', 
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
            'data_class' => 'AppBundle\Entity\Numerador'
        ));
    }
}
