<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComprobanteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('monto')
            ->add('recargoSegundoVencimiento')
            ->add('recargoPrimerVencimiento')
            ->add('clienteSap')
            ->add('letraServicio')
            ->add('rendicionNumero')
        
            ->add('fechaCobro','date', array(   
                'required' => FALSE,                     
                'constraints' => null,
                'data' => (isset($options['data']) && 
                                $options['data']->getFechaCobro() !== null) ? $options['data']->getFechaCobro() : null,
                 // render as a single text box
                 'widget' => 'single_text',
                 'format' => 'dd/MM/yyyy',
                 // do not render as type="date", to avoid HTML5 date pickers
                 'html5' => false,
                 // add a class that can be selected in JavaScript
                 'attr' => ['class' => 'js-datepicker']
                )
            )
            ->add('primerVencimiento','date', array(   
                'required' => FALSE,                     
                'constraints' => null,
                'data' => (isset($options['data']) && 
                                $options['data']->getPrimerVencimiento() !== null) ? $options['data']->getPrimerVencimiento() : null,
                 // render as a single text box
                 'widget' => 'single_text',
                 'format' => 'dd/MM/yyyy',
                 // do not render as type="date", to avoid HTML5 date pickers
                 'html5' => false,
                 // add a class that can be selected in JavaScript
                 'attr' => ['class' => 'js-datepicker']
                )
            )            
            ->add('segundoVencimiento','date', array(   
                'required' => FALSE,                     
                'constraints' => null,
                'data' => (isset($options['data']) && 
                                $options['data']->getSegundoVencimiento() !== null) ? $options['data']->getSegundoVencimiento() : null,
                 // render as a single text box
                 'widget' => 'single_text',
                 'format' => 'dd/MM/yyyy',
                 // do not render as type="date", to avoid HTML5 date pickers
                 'html5' => false,
                 // add a class that can be selected in JavaScript
                 'attr' => ['class' => 'js-datepicker']
                )
            )
            ->add('numeroCodigoBarra')            
            ->add('createdAt','date', array(   
                'required' => FALSE,   
                'read_only' => TRUE,                
                'constraints' => null,
                'data' => (isset($options['data']) && 
                                $options['data']->getCreatedAt() !== null) ? $options['data']->getCreatedAt() : new \DateTime(),
                 // render as a single text box
                 'widget' => 'single_text',
                 'format' => 'dd/MM/yyyy',
                 // do not render as type="date", to avoid HTML5 date pickers
                 'html5' => false,
                 // add a class that can be selected in JavaScript
                 'attr' => ['class' => 'js-datepicker']
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
            'data_class' => 'AppBundle\Entity\Comprobante'
        ));
    }
}
